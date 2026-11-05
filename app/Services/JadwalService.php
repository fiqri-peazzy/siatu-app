<?php

namespace App\Services;

use App\Enums\AksiPengecualianJadwal;
use App\Enums\Hari;
use App\Enums\JenisSesi;
use App\Enums\JenisSlotJam;
use App\Models\JadwalPengecualian;
use App\Models\JadwalSesi;
use App\Models\JurnalGuru;
use App\Models\Master\MasterSlotJam;
use Carbon\Carbon;
use App\Models\Master\MasterGuru;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterMapel;
use App\Models\Master\MasterPengampu;
use App\Models\Master\MasterPolaJam;
use App\Models\Master\MasterTahunAjaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class JadwalService
{
    /**
     * Bangun pola jam + sesi dari data terstruktur
     * (format: database/seeders/data/jadwal_2026_2027_ganjil.php).
     * Dipakai seeder dan nantinya import Excel.
     *
     * Idempoten: pola jam dengan nama sama dibangun ulang; pengampu yang sudah
     * diatur admin TIDAK ditimpa.
     */
    public function importDariArray(array $data): MasterPolaJam
    {
        return DB::transaction(function () use ($data) {
            $tahunAjaran = MasterTahunAjaran::query()
                ->where('nama', $data['tahun_ajaran']['nama'])
                ->where('semester', $data['tahun_ajaran']['semester'])
                ->firstOrFail();

            $kelas = $this->siapkanRombel($tahunAjaran, $data['rombel']);
            $mapel = $this->siapkanMapel($data['mapel']);

            MasterPolaJam::query()
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->where('nama', $data['pola_jam'])
                ->get()
                ->each->delete(); // cascade ke slot & sesi

            MasterPolaJam::query()
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->update(['is_active' => false]);

            $pola = MasterPolaJam::create([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'nama' => $data['pola_jam'],
                'is_active' => true,
            ]);

            foreach ($data['hari'] as $hari => $slots) {
                foreach (array_values($slots) as $i => [$jamKe, $mulai, $selesai, $isi]) {
                    $this->buatSlot($pola, $hari, $i + 1, $jamKe, $mulai, $selesai, $isi, $kelas, $mapel);
                }
            }

            $this->pastikanPengampu($tahunAjaran->id, $pola->id);
            $this->sinkronGuruSesi($tahunAjaran->id);

            return $pola;
        });
    }

    /**
     * Isi pengampu yang masih kosong secara acak — utamakan guru yang
     * mapel utamanya cocok. Dipakai sementara sebelum admin mengatur manual.
     *
     * @return int jumlah pengampu yang diisi
     */
    public function isiPengampuAcak(int $tahunAjaranId): int
    {
        $gurus = MasterGuru::query()->get(['id', 'mapel_id']);
        if ($gurus->isEmpty()) {
            return 0;
        }

        $terisi = 0;
        MasterPengampu::query()
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->whereNull('guru_id')
            ->get()
            ->each(function (MasterPengampu $p) use ($gurus, &$terisi) {
                $cocok = $gurus->where('mapel_id', $p->mapel_id);
                $p->update(['guru_id' => ($cocok->isNotEmpty() ? $cocok : $gurus)->random()->id]);
                $terisi++;
            });

        $this->sinkronGuruSesi($tahunAjaranId);

        return $terisi;
    }

    /**
     * Salin guru dari pengampu ke setiap sesi (kecuali sesi dengan guru_manual).
     * Sesi gabungan memakai pengampu rombel pertama (urut id kelas).
     */
    public function sinkronGuruSesi(int $tahunAjaranId): void
    {
        $pengampu = MasterPengampu::query()
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->get()
            ->keyBy(fn ($p) => $p->kelas_id.':'.$p->mapel_id);

        JadwalSesi::query()
            ->where('guru_manual', false)
            ->whereNotNull('mapel_id')
            ->whereHas('slot.polaJam', fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->with('kelas:id')
            ->chunkById(200, function ($sesis) use ($pengampu) {
                foreach ($sesis as $sesi) {
                    $guruId = $sesi->kelas->sortBy('id')
                        ->map(fn ($k) => $pengampu->get($k->id.':'.$sesi->mapel_id)?->guru_id)
                        ->filter()
                        ->first();

                    if ($sesi->guru_id !== $guruId) {
                        $sesi->update(['guru_id' => $guruId]);
                    }
                }
            });
    }

    /**
     * Jadwal yang benar-benar berlaku pada satu tanggal: pola jam induk +
     * pengecualian tanggal itu (tiadakan / ubah waktu / kegiatan sisipan).
     *
     * Jam pelajaran yang bertabrakan dengan upacara/kegiatan sisipan:
     *  - tertutup penuh → status 'tergeser' (tidak berlangsung, tidak wajib jurnal)
     *  - tertutup sebagian → status 'terpotong', jam mulai/selesai disesuaikan
     *
     * Libur sehari penuh (master_hari_liburs / HolidayService) ditangani pemanggil.
     *
     * @return Collection<int, array{slot: ?MasterSlotJam, jenis: string, jam_ke: ?int, label: ?string,
     *                               mulai: string, selesai: string, status: string, catatan: ?string}>
     */
    public function jadwalEfektif(Carbon $tanggal, ?MasterPolaJam $pola = null): Collection
    {
        $pola ??= $this->polaAktif();
        $hari = Hari::fromIso($tanggal->dayOfWeekIso);

        if (! $pola || ! $hari) {
            return collect();
        }

        $pengecualian = JadwalPengecualian::query()->whereDate('tanggal', $tanggal)->get();
        $perSlot = $pengecualian->whereNotNull('slot_jam_id')->keyBy('slot_jam_id');

        $items = $pola->slots()->where('hari', $hari->value)->get()->map(function (MasterSlotJam $slot) use ($perSlot) {
            $p = $perSlot->get($slot->id);

            return [
                'slot' => $slot,
                'jenis' => $slot->jenis->value,
                'jam_ke' => $slot->jam_ke,
                'label' => $slot->label,
                'mulai' => substr($p?->jam_mulai ?? $slot->jam_mulai, 0, 5),
                'selesai' => substr($p?->jam_selesai ?? $slot->jam_selesai, 0, 5),
                'status' => match ($p?->aksi) {
                    AksiPengecualianJadwal::TIADAKAN => 'ditiadakan',
                    AksiPengecualianJadwal::UBAH_WAKTU => 'diubah',
                    default => 'normal',
                },
                'catatan' => $p?->keterangan,
            ];
        });

        $sisipan = $pengecualian
            ->where('aksi', AksiPengecualianJadwal::TAMBAH)
            ->map(fn (JadwalPengecualian $p) => [
                'slot' => null,
                'jenis' => 'sisipan',
                'jam_ke' => null,
                'label' => $p->label,
                'mulai' => substr($p->jam_mulai, 0, 5),
                'selesai' => substr($p->jam_selesai, 0, 5),
                'status' => 'sisipan',
                'catatan' => $p->keterangan,
            ]);

        // Blok penghalang: upacara yang tetap berlangsung + kegiatan sisipan
        $penghalang = $items
            ->filter(fn ($i) => $i['jenis'] === JenisSlotJam::UPACARA->value && $i['status'] !== 'ditiadakan')
            ->concat($sisipan);

        $items = $items->map(function (array $item) use ($penghalang) {
            if ($item['jenis'] !== JenisSlotJam::PELAJARAN->value || $item['status'] === 'ditiadakan') {
                return $item;
            }

            foreach ($penghalang as $blok) {
                $tumpangTindih = $blok['mulai'] < $item['selesai'] && $blok['selesai'] > $item['mulai'];
                if (! $tumpangTindih) {
                    continue;
                }

                $tertutupAwal = $blok['mulai'] <= $item['mulai'];
                $tertutupAkhir = $blok['selesai'] >= $item['selesai'];

                if ($tertutupAwal && $tertutupAkhir) {
                    return [...$item, 'status' => 'tergeser', 'catatan' => 'Tergeser oleh '.($blok['label'] ?? 'kegiatan')];
                }

                $item = [
                    ...$item,
                    'mulai' => $tertutupAwal ? $blok['selesai'] : $item['mulai'],
                    'selesai' => $tertutupAkhir ? $blok['mulai'] : $item['selesai'],
                    'status' => 'terpotong',
                    'catatan' => 'Terpotong oleh '.($blok['label'] ?? 'kegiatan'),
                ];
            }

            return $item;
        });

        return $items->concat($sisipan)->sortBy('mulai')->values();
    }

    /**
     * Timeline satu hari untuk seorang guru, siap ditampilkan.
     *
     * items[] berurutan waktu, tipe:
     *  - 'blok'  : mengajar (sesi berurutan, mapel & rombel sama digabung)
     *  - 'jeda'  : istirahat / upacara / kegiatan sekolah / kegiatan sisipan
     *  - 'kosong': jam tanpa tugas mengajar (berurutan digabung)
     *
     * @return array{hari_kerja: bool, items: list<array<string, mixed>>, jumlah_blok: int, belum_jurnal: int}
     */
    public function jadwalGuruHarian(int $guruId, Carbon $tanggal, ?Carbon $sekarang = null): array
    {
        $sekarang ??= now();
        $efektif = $this->jadwalEfektif($tanggal);

        if ($efektif->isEmpty()) {
            return ['hari_kerja' => false, 'items' => [], 'jumlah_blok' => 0, 'belum_jurnal' => 0];
        }

        $slotIds = $efektif->pluck('slot.id')->filter()->all();

        $sesiGuru = JadwalSesi::query()
            ->with(['mapel:id,nama', 'kelas:id,kode,nama'])
            ->whereIn('slot_jam_id', $slotIds)
            ->where('guru_id', $guruId)
            ->get()
            ->groupBy('slot_jam_id');

        $kegiatan = JadwalSesi::query()
            ->whereIn('slot_jam_id', $slotIds)
            ->where('jenis', JenisSesi::KEGIATAN)
            ->get()
            ->keyBy('slot_jam_id');

        $terisi = JurnalGuru::query()
            ->where('guru_id', $guruId)
            ->whereDate('tanggal', $tanggal)
            ->where('status', 'diisi')
            ->whereNotNull('jadwal_sesi_id')
            ->pluck('jadwal_sesi_id')
            ->flip();

        $hariIni = $tanggal->isSameDay($sekarang);
        $jamSekarang = $sekarang->format('H:i');
        $fase = function (string $mulai, string $selesai) use ($hariIni, $tanggal, $sekarang, $jamSekarang): string {
            if (! $hariIni) {
                return $tanggal->lt($sekarang->copy()->startOfDay()) ? 'lewat' : 'mendatang';
            }

            return match (true) {
                $selesai <= $jamSekarang => 'lewat',
                $mulai <= $jamSekarang => 'berlangsung',
                default => 'mendatang',
            };
        };

        $items = [];
        foreach ($efektif as $e) {
            $slotId = $e['slot']?->id;
            $baru = null;

            if ($e['jenis'] === JenisSlotJam::PELAJARAN->value && $slotId && ($sesis = $sesiGuru->get($slotId))) {
                $sesi = $sesis->first();
                $tergeser = in_array($e['status'], ['tergeser', 'ditiadakan'], true);
                $baru = [
                    'tipe' => 'blok',
                    'kunci' => $sesi->mapel_id.'|'.$sesi->kelas->pluck('id')->sort()->implode(',').'|'.($tergeser ? 'x' : 'ok'),
                    'mulai' => $e['mulai'],
                    'selesai' => $e['selesai'],
                    'jam_ke' => [$e['jam_ke']],
                    'mapel' => $sesi->mapel?->nama ?? $sesi->label ?? '-',
                    'mapel_id' => $sesi->mapel_id,
                    'kelas' => $sesi->kelas->pluck('nama')->all(),
                    'kelas_ids' => $sesi->kelas->pluck('id')->all(),
                    'jenis' => $sesi->jenis->value,
                    'grup_pilihan' => $sesi->grup_pilihan,
                    'sesi_ids' => [$sesi->id],
                    'terisi' => isset($terisi[$sesi->id]) ? 1 : 0,
                    'status_slot' => $e['status'],
                    'catatan' => $e['catatan'],
                ];
            } elseif ($e['jenis'] === JenisSlotJam::PELAJARAN->value && $slotId && $kegiatan->has($slotId)) {
                $label = $kegiatan[$slotId]->label;
                $baru = ['tipe' => 'jeda', 'jenis' => 'kegiatan', 'kunci' => 'k|'.$label, 'label' => $label, 'mulai' => $e['mulai'], 'selesai' => $e['selesai']];
            } elseif ($e['jenis'] === JenisSlotJam::PELAJARAN->value) {
                $baru = ['tipe' => 'kosong', 'kunci' => 'kosong', 'label' => 'Tidak ada jadwal mengajar', 'mulai' => $e['mulai'], 'selesai' => $e['selesai']];
            } elseif ($e['status'] !== 'ditiadakan') {
                $baru = ['tipe' => 'jeda', 'jenis' => $e['jenis'], 'kunci' => 'j|'.$e['label'], 'label' => $e['label'] ?? 'Kegiatan', 'mulai' => $e['mulai'], 'selesai' => $e['selesai']];
            }

            if (! $baru) {
                continue;
            }

            // Gabungkan dengan item sebelumnya bila sama & bersambung
            $akhir = array_key_last($items);
            if ($akhir !== null && $items[$akhir]['tipe'] === $baru['tipe'] && $items[$akhir]['kunci'] === $baru['kunci'] && $items[$akhir]['selesai'] === $baru['mulai']) {
                $items[$akhir]['selesai'] = $baru['selesai'];
                if ($baru['tipe'] === 'blok') {
                    $items[$akhir]['jam_ke'][] = $baru['jam_ke'][0];
                    $items[$akhir]['sesi_ids'][] = $baru['sesi_ids'][0];
                    $items[$akhir]['terisi'] += $baru['terisi'];
                }

                continue;
            }

            $items[] = $baru;
        }

        // Satu "pertemuan" = blok-blok dengan mapel & rombel sama yang hanya dipisah
        // istirahat → satu jurnal (guru tidak mengisi dua kali untuk materi yang sama).
        $grupDari = [];
        $terakhirBlok = null;
        foreach ($items as $i => $item) {
            if ($item['tipe'] === 'blok') {
                $grupDari[$i] = ($terakhirBlok !== null && $items[$terakhirBlok]['kunci'] === $item['kunci']) ? $grupDari[$terakhirBlok] : $i;
                $terakhirBlok = $i;
            } elseif (! ($item['tipe'] === 'jeda' && $item['jenis'] === JenisSlotJam::ISTIRAHAT->value)) {
                $terakhirBlok = null;
            }
        }
        $pertemuan = collect($grupDari)->groupBy(fn ($g) => $g, true)->map(fn ($anggota) => [
            'sesi_ids' => collect($anggota->keys())->flatMap(fn ($i) => $items[$i]['sesi_ids'])->all(),
            'terisi' => collect($anggota->keys())->sum(fn ($i) => $items[$i]['terisi']),
        ]);

        // Status jurnal & fase per item
        foreach ($items as $i => &$item) {
            $item['fase'] = $fase($item['mulai'], $item['selesai']);
            if ($item['tipe'] === 'blok') {
                $p = $pertemuan[$grupDari[$i]];
                $item['jurnal_sesi_ids'] = $p['sesi_ids'];
                $item['lanjutan'] = $grupDari[$i] !== $i;
                $item['jurnal'] = match (true) {
                    in_array($item['status_slot'], ['tergeser', 'ditiadakan'], true) => 'tidak_wajib',
                    $p['terisi'] >= count($p['sesi_ids']) => 'diisi',
                    $p['terisi'] > 0 => 'sebagian',
                    default => 'belum',
                };
            }
        }
        unset($item);

        // Hitung per pertemuan (bukan per potongan blok)
        $blok = collect($items)->where('tipe', 'blok')->where('lanjutan', false);

        return [
            'hari_kerja' => true,
            'items' => array_values($items),
            'jumlah_blok' => $blok->count(),
            'belum_jurnal' => $blok->whereIn('jurnal', ['belum', 'sebagian'])->where('fase', '!=', 'mendatang')->count(),
        ];
    }

    /** Jumlah jam mengajar guru per hari (untuk strip hari) — ['senin' => 4, ...] */
    public function ringkasanMingguGuru(int $guruId): array
    {
        $pola = $this->polaAktif();
        if (! $pola) {
            return [];
        }

        return JadwalSesi::query()
            ->join('master_slot_jams', 'master_slot_jams.id', '=', 'jadwal_sesis.slot_jam_id')
            ->where('master_slot_jams.pola_jam_id', $pola->id)
            ->where('jadwal_sesis.guru_id', $guruId)
            ->selectRaw('master_slot_jams.hari, COUNT(*) as jumlah')
            ->groupBy('master_slot_jams.hari')
            ->pluck('jumlah', 'hari')
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    /** Pola jam aktif pada tahun ajaran aktif */
    public function polaAktif(): ?MasterPolaJam
    {
        return MasterPolaJam::query()
            ->aktif()
            ->whereHas('tahunAjaran', fn ($q) => $q->where('is_active', true))
            ->latest('id')
            ->first();
    }

    // ------------------------------------------------------------------

    /** @return Collection<string, MasterKelas> keyed by kode */
    private function siapkanRombel(MasterTahunAjaran $ta, array $rombel): Collection
    {
        $hasil = collect();

        foreach ($rombel as $kode => [$nama, $tingkat, $jurusan]) {
            $kelas = MasterKelas::query()->where('tahun_ajaran_id', $ta->id)->where('kode', $kode)->first();

            // Rombel lama tanpa kode (mis. "Kelas X") diambil alih oleh rombel pertama
            // di tingkat tsb. agar siswa yang sudah terdaftar tidak hilang.
            $kelas ??= MasterKelas::query()
                ->where('tahun_ajaran_id', $ta->id)
                ->where('tingkat', $tingkat)
                ->whereNull('kode')
                ->orderBy('id')
                ->first();

            $kelas ??= new MasterKelas(['tahun_ajaran_id' => $ta->id]);
            $kelas->fill(['kode' => $kode, 'nama' => $nama, 'tingkat' => $tingkat, 'jurusan' => $jurusan])->save();

            $hasil[$kode] = $kelas;
        }

        return $hasil;
    }

    /** @return Collection<string, MasterMapel> keyed by singkatan jadwal */
    private function siapkanMapel(array $mapel): Collection
    {
        return collect($mapel)->map(function (array $def) {
            [$kode, $nama, $kelompok] = $def;

            return MasterMapel::query()->firstOrCreate(
                ['kode' => $kode],
                ['nama' => $nama, 'kelompok' => $kelompok]
            );
        });
    }

    private function buatSlot(
        MasterPolaJam $pola, string $hari, int $urutan, ?int $jamKe,
        string $mulai, string $selesai, array $isi, Collection $kelas, Collection $mapel
    ): void {
        $nonPelajaran = $jamKe === null;

        $slot = $pola->slots()->create([
            'hari' => $hari,
            'urutan' => $urutan,
            'jam_ke' => $jamKe,
            'jam_mulai' => $mulai,
            'jam_selesai' => $selesai,
            'jenis' => $nonPelajaran ? JenisSlotJam::from($isi[0]) : JenisSlotJam::PELAJARAN,
            'label' => $nonPelajaran ? $isi[1] : null,
        ]);

        if ($nonPelajaran) {
            return;
        }

        foreach ($isi as $target => $nilai) {
            $kelasIds = $this->resolveTarget($target, $kelas);

            if (str_starts_with($nilai, '#')) {
                $this->buatSesi($slot->id, JenisSesi::KEGIATAN, null, substr($nilai, 1), null, $kelasIds);

                continue;
            }

            $opsi = explode('|', $nilai);
            $jenis = count($opsi) > 1 ? JenisSesi::PILIHAN : JenisSesi::PEMBELAJARAN;

            foreach ($opsi as $singkatan) {
                $m = $mapel->get($singkatan) ?? throw new InvalidArgumentException("Mapel '{$singkatan}' tidak terdaftar ({$hari} jam ke-{$jamKe}).");
                $this->buatSesi($slot->id, $jenis, $m->id, null, $jenis === JenisSesi::PILIHAN ? $nilai : null, $kelasIds);
            }
        }
    }

    private function buatSesi(int $slotId, JenisSesi $jenis, ?int $mapelId, ?string $label, ?string $grup, array $kelasIds): void
    {
        $sesi = JadwalSesi::create([
            'slot_jam_id' => $slotId,
            'jenis' => $jenis,
            'mapel_id' => $mapelId,
            'label' => $label,
            'grup_pilihan' => $grup ? str_replace('|', '/', $grup) : null,
        ]);

        $sesi->kelas()->sync($kelasIds);
    }

    /** 'X' → [X-SCI, X-SOC], 'X-SCI' → [X-SCI], 'ALL' → semua */
    private function resolveTarget(string $target, Collection $kelas): array
    {
        $kodes = match (true) {
            $target === 'ALL' => $kelas->keys()->all(),
            $kelas->has($target) => [$target],
            default => $kelas->keys()->filter(fn ($k) => str_starts_with($k, $target.'-'))->all(),
        };

        if (empty($kodes)) {
            throw new InvalidArgumentException("Target rombel '{$target}' tidak dikenal.");
        }

        return collect($kodes)->map(fn ($k) => $kelas[$k]->id)->values()->all();
    }

    /** Buat baris pengampu (guru kosong) untuk setiap pasangan rombel × mapel di jadwal */
    private function pastikanPengampu(int $tahunAjaranId, int $polaJamId): void
    {
        DB::table('jadwal_sesis')
            ->join('master_slot_jams', 'master_slot_jams.id', '=', 'jadwal_sesis.slot_jam_id')
            ->join('jadwal_sesi_kelas', 'jadwal_sesi_kelas.jadwal_sesi_id', '=', 'jadwal_sesis.id')
            ->where('master_slot_jams.pola_jam_id', $polaJamId)
            ->whereNotNull('jadwal_sesis.mapel_id')
            ->distinct()
            ->get(['jadwal_sesi_kelas.kelas_id', 'jadwal_sesis.mapel_id'])
            ->each(fn ($row) => MasterPengampu::query()->firstOrCreate([
                'tahun_ajaran_id' => $tahunAjaranId,
                'kelas_id' => $row->kelas_id,
                'mapel_id' => $row->mapel_id,
            ]));
    }
}
