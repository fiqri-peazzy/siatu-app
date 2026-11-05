<?php

namespace App\Services;

use App\Enums\KategoriAbsensi;
use App\Models\AbsensiGuru;
use App\Models\AbsensiTendik;
use App\Models\Master\MasterGuru;
use App\Models\Master\MasterTendik;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Rekap kehadiran bulanan guru / tendik:
 *   - matriks pegawai × tanggal 1..akhir bulan (kode harian)
 *   - detail harian (jam datang, jam pulang, status, keterangan)
 * Hari off = hari non-kerja (aturan_absensis) atau libur nasional.
 */
class RekapKehadiranService
{
    /** Kode sel harian → [label, warna latar ARGB] */
    public const KODE = [
        'H' => ['Hadir tepat waktu', 'FFE7F3EC'],
        'T' => ['Terlambat', 'FFFDF1DD'],
        'PC' => ['Pulang cepat', 'FFFDF1DD'],
        'I' => ['Izin', 'FFE3EBFB'],
        'S' => ['Sakit', 'FFE3EBFB'],
        'DL' => ['Dinas luar', 'FFE3EBFB'],
        'A' => ['Tidak hadir tanpa keterangan', 'FFFBE1DE'],
        'L' => ['Libur / hari off', 'FFD9D9D9'],
    ];

    public function __construct(private AturanAbsensiService $aturan) {}

    /**
     * @return array{kategori: KategoriAbsensi, bulan: Carbon, hari: list<array{tgl: Carbon, off: bool, keterangan: ?string}>,
     *               pegawai: list<array{nama: string, nip: string, sel: array<int,string>, detail: list<array>, total: array<string,int>, efektif: int, persen: float}>}
     */
    public function bulanan(KategoriAbsensi $kategori, Carbon $bulan): array
    {
        $awal = $bulan->copy()->startOfMonth();
        $akhir = $bulan->copy()->endOfMonth();
        $hariIni = now()->startOfDay();
        $libur = HolidayService::getHolidays($awal->year);

        $hari = [];
        for ($d = $awal->copy(); $d->lte($akhir); $d->addDay()) {
            $namaLibur = $libur[$d->toDateString()] ?? null;
            $aturan = $this->aturan->aturan($kategori, $d);
            $hari[$d->day] = [
                'tgl' => $d->copy(),
                'off' => $namaLibur || ! $aturan || ! $aturan->hari_kerja,
                'keterangan' => $namaLibur ?? (! $aturan || ! $aturan->hari_kerja ? 'Hari off' : null),
            ];
        }

        [$pegawai, $absensi] = $kategori === KategoriAbsensi::GURU
            ? [MasterGuru::where('status', 'aktif')->orderBy('nama')->get(['id', 'nama', 'nip', 'nuptk']),
                AbsensiGuru::whereBetween('tanggal', [$awal->toDateString(), $akhir->toDateString()])->get()->groupBy('guru_id')]
            : [MasterTendik::where('status', 'aktif')->orderBy('nama')->get(['id', 'nama', 'nip_nik']),
                AbsensiTendik::whereBetween('tanggal', [$awal->toDateString(), $akhir->toDateString()])->get()->groupBy('tendik_id')];

        $hasil = $pegawai->map(function ($p) use ($absensi, $hari, $hariIni) {
            /** @var Collection $milik */
            $milik = ($absensi[$p->id] ?? collect())->groupBy(fn ($a) => Carbon::parse($a->tanggal)->day);
            $sel = [];
            $detail = [];
            $total = array_fill_keys(array_keys(self::KODE), 0);
            $efektif = 0;

            foreach ($hari as $tgl => $h) {
                $catatan = $milik[$tgl] ?? collect();
                $masuk = $catatan->firstWhere('jenis', 'masuk');
                $pulang = $catatan->firstWhere('jenis', 'pulang');
                $lewat = $h['tgl']->lte($hariIni);

                if ($h['off'] && ! $masuk) {
                    $sel[$tgl] = 'L';

                    continue;
                }
                if (! $h['off']) {
                    $efektif += $lewat ? 1 : 0;
                }

                $kode = match (true) {
                    (bool) $masuk => match ($masuk->status) {
                        'terlambat' => 'T', 'izin' => 'I', 'sakit' => 'S', 'dinas_luar' => 'DL', 'alfa' => 'A',
                        default => $pulang?->status === 'pulang_cepat' ? 'PC' : 'H',
                    },
                    $lewat => 'A',
                    default => '',
                };
                $sel[$tgl] = $kode;
                if ($kode !== '') {
                    $total[$kode]++;
                }

                if ($lewat || $masuk) {
                    $datang = $masuk ? Carbon::parse($masuk->waktu_absen) : null;
                    $detail[] = [
                        'tanggal' => $h['tgl'],
                        'datang' => in_array($kode, ['H', 'T', 'PC'], true) ? $datang?->format('H:i') : null,
                        'pulang' => $pulang ? Carbon::parse($pulang->waktu_absen)->format('H:i') : null,
                        'kode' => $kode,
                        'keterangan' => $this->keterangan($kode, $masuk, $pulang, $h),
                    ];
                }
            }

            $hadir = $total['H'] + $total['T'] + $total['PC'] + $total['DL'];

            return [
                'nama' => $p->nama,
                'nip' => $p->nip ?? $p->nuptk ?? $p->nip_nik ?? '-',
                'sel' => $sel,
                'detail' => $detail,
                'total' => $total,
                'efektif' => $efektif,
                'persen' => $efektif > 0 ? round($hadir / $efektif * 100, 1) : 0.0,
            ];
        })->values()->all();

        return ['kategori' => $kategori, 'bulan' => $awal, 'hari' => array_values($hari), 'pegawai' => $hasil];
    }

    private function keterangan(string $kode, $masuk, $pulang, array $hari): string
    {
        $bagian = [];
        if ($kode === 'A') {
            $bagian[] = 'Tidak hadir tanpa keterangan';
        } elseif (in_array($kode, ['I', 'S', 'DL'], true)) {
            $bagian[] = self::KODE[$kode][0];
        } elseif ($kode === 'T') {
            $bagian[] = 'Terlambat';
        }
        if ($masuk && in_array($kode, ['H', 'T', 'PC'], true) && ! $pulang) {
            $bagian[] = 'Tidak absen pulang';
        }
        if ($pulang?->status === 'pulang_cepat') {
            $bagian[] = 'Pulang cepat'.($pulang->keterangan ? ': '.$pulang->keterangan : '');
        }
        if ($masuk?->keterangan && ! in_array($kode, ['PC'], true)) {
            $bagian[] = $masuk->keterangan;
        }
        if ($hari['off']) {
            $bagian[] = 'Masuk di hari off ('.$hari['keterangan'].')';
        }

        return implode(' · ', $bagian) ?: 'Hadir';
    }
}
