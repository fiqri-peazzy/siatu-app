<?php

namespace App\Services;

use App\Enums\AksiPengecualianJadwal;
use App\Enums\Hari;
use App\Enums\JenisSesi;
use App\Enums\JenisSlotJam;
use App\Models\JadwalPengecualian;
use App\Models\JadwalSesi;
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
