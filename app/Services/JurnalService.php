<?php

namespace App\Services;

use App\Enums\JenisSesi;
use App\Models\AbsensiSiswa;
use App\Models\JadwalSesi;
use App\Models\JurnalGuru;
use App\Models\Master\MasterSiswa;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Jurnal mengajar per blok sesi (jadwal v2).
 * Satu blok = satu/lebih sesi berurutan (jam ke-) dengan mapel & rombel sama.
 * Disimpan per jam × per rombel agar kompatibel dengan rekap lama.
 */
class JurnalService
{
    public function __construct(private JadwalService $jadwal) {}

    /**
     * Data untuk form jurnal.
     *
     * @return array{sesis: Collection, mapel: ?\App\Models\Master\MasterMapel, kelas: Collection, jam_ke: list<int>,
     *               mulai: string, selesai: string, siswa: Collection, jurnal: ?JurnalGuru, sebelumnya: ?JurnalGuru, absensi: array<int,string>}
     */
    public function siapkan(int $guruId, array $sesiIds, Carbon $tanggal): array
    {
        $sesis = JadwalSesi::query()
            ->with(['slot', 'mapel', 'kelas'])
            ->whereIn('id', $sesiIds)
            ->get()
            ->sortBy(fn ($s) => $s->slot->urutan)
            ->values();

        if ($sesis->isEmpty() || $sesis->count() !== count(array_unique($sesiIds))) {
            throw new InvalidArgumentException('Sesi jadwal tidak ditemukan.');
        }
        if ($sesis->contains(fn ($s) => $s->guru_id !== $guruId)) {
            throw new AuthorizationException('Sesi ini bukan jadwal mengajar Anda.');
        }
        if ($sesis->contains(fn ($s) => $s->jenis === JenisSesi::KEGIATAN)) {
            throw new InvalidArgumentException('Kegiatan sekolah tidak memerlukan jurnal mengajar.');
        }
        if ($sesis->pluck('slot.hari')->unique()->count() > 1 || $sesis->first()->slot->hari->iso() !== $tanggal->dayOfWeekIso) {
            throw new InvalidArgumentException('Sesi tidak sesuai dengan hari ini.');
        }

        // Jam yang tergeser upacara/kegiatan tidak wajib & tidak bisa diisi
        $efektif = $this->jadwal->jadwalEfektif($tanggal)->keyBy(fn ($e) => $e['slot']?->id);
        $tergeser = $sesis->first(fn ($s) => in_array($efektif[$s->slot_jam_id]['status'] ?? null, ['tergeser', 'ditiadakan'], true));
        if ($tergeser) {
            throw new InvalidArgumentException("Jam ke-{$tergeser->slot->jam_ke} tergeser oleh kegiatan lain hari ini.");
        }

        $pertama = $sesis->first();
        $kelas = $sesis->flatMap->kelas->unique('id')->sortBy('kode')->values();

        $siswa = $this->pesertaSesi($pertama, $kelas->pluck('id')->all());

        $jurnal = JurnalGuru::query()
            ->where('guru_id', $guruId)
            ->whereDate('tanggal', $tanggal)
            ->whereIn('jadwal_sesi_id', $sesis->pluck('id'))
            ->first();

        $sebelumnya = JurnalGuru::query()
            ->where('guru_id', $guruId)
            ->where('mapel_id', $pertama->mapel_id)
            ->whereIn('kelas_id', $kelas->pluck('id'))
            ->whereDate('tanggal', '<', $tanggal)
            ->where('status', 'diisi')
            ->latest('tanggal')
            ->first();

        // Absensi yang sudah tercatat (mode edit), default 'hadir'
        $tercatat = AbsensiSiswa::query()
            ->whereDate('tanggal', $tanggal)
            ->whereIn('jadwal_sesi_id', $sesis->pluck('id'))
            ->pluck('status', 'siswa_id');

        return [
            'sesis' => $sesis,
            'mapel' => $pertama->mapel,
            'kelas' => $kelas,
            'jam_ke' => $sesis->pluck('slot.jam_ke')->all(),
            'mulai' => substr($pertama->slot->jam_mulai, 0, 5),
            'selesai' => substr($sesis->last()->slot->jam_selesai, 0, 5),
            'siswa' => $siswa,
            'jurnal' => $jurnal,
            'sebelumnya' => $sebelumnya,
            'absensi' => $siswa->mapWithKeys(fn ($s) => [$s->id => $tercatat[$s->id] ?? 'hadir'])->all(),
        ];
    }

    /**
     * Simpan jurnal + absensi siswa untuk satu blok sesi.
     *
     * @param  array{kd_cp: ?string, materi: string, kegiatan: string, catatan?: ?string}  $isi
     * @param  array<int, string>  $absensi  siswa_id => hadir|izin|sakit|alfa
     * @param  array<int, string>  $buktiIzin  siswa_id => path
     * @param  list<string>  $dokumentasi  path foto
     */
    public function simpan(int $guruId, int $userId, array $sesiIds, Carbon $tanggal, array $isi, array $absensi, array $buktiIzin = [], array $dokumentasi = []): void
    {
        $ctx = $this->siapkan($guruId, $sesiIds, $tanggal);
        $siswaPerKelas = $ctx['siswa']->groupBy('kelas_id');

        DB::transaction(function () use ($ctx, $guruId, $userId, $tanggal, $isi, $absensi, $buktiIzin, $dokumentasi, $siswaPerKelas) {
            foreach ($ctx['sesis'] as $sesi) {
                foreach ($sesi->kelas as $kelas) {
                    $stat = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0];

                    foreach ($siswaPerKelas->get($kelas->id, collect()) as $s) {
                        $status = $absensi[$s->id] ?? 'hadir';
                        $stat[$status] = ($stat[$status] ?? 0) + 1;

                        AbsensiSiswa::updateOrCreate(
                            ['siswa_id' => $s->id, 'kelas_id' => $kelas->id, 'tanggal' => $tanggal->toDateString(), 'jam_ke' => $sesi->slot->jam_ke],
                            array_filter([
                                'jadwal_sesi_id' => $sesi->id,
                                'status' => $status,
                                'dicatat_oleh' => $userId,
                                'bukti_file' => $buktiIzin[$s->id] ?? null,
                            ], fn ($v) => $v !== null)
                        );
                    }

                    $data = [
                        'jadwal_sesi_id' => $sesi->id,
                        'kd_cp' => $isi['kd_cp'] ?? null,
                        'materi' => $isi['materi'],
                        'kegiatan' => $isi['kegiatan'],
                        'status' => 'diisi',
                        'jumlah_hadir' => $stat['hadir'],
                        'jumlah_izin' => $stat['izin'],
                        'jumlah_sakit' => $stat['sakit'],
                        'jumlah_alfa' => $stat['alfa'],
                    ];
                    if ($dokumentasi) {
                        $data['dokumentasi'] = $dokumentasi;
                    }

                    // tanggal sebagai Carbon: kolom di-cast 'date', sehingga kunci pencarian cocok di MySQL maupun SQLite
                    JurnalGuru::updateOrCreate(
                        ['guru_id' => $guruId, 'kelas_id' => $kelas->id, 'mapel_id' => $sesi->mapel_id, 'tanggal' => $tanggal->copy()->startOfDay(), 'jam_ke' => $sesi->slot->jam_ke],
                        $data
                    );
                }
            }
        });
    }

    /** Siswa aktif peserta sesi; mapel pilihan → hanya peminat mapel tsb. */
    private function pesertaSesi(JadwalSesi $sesi, array $kelasIds): Collection
    {
        $query = MasterSiswa::query()
            ->whereIn('kelas_id', $kelasIds)
            ->where('status', 'aktif')
            ->orderBy('nama');

        $taId = $sesi->slot->polaJam->tahun_ajaran_id;
        $peminat = DB::table('peminatan_siswas')
            ->where('mapel_id', $sesi->mapel_id)
            ->where('tahun_ajaran_id', $taId)
            ->pluck('siswa_id');

        if ($sesi->jenis === JenisSesi::PILIHAN || $peminat->isNotEmpty()) {
            $query->whereIn('id', $peminat);
        }

        return $query->get(['id', 'nama', 'nis', 'kelas_id']);
    }
}
