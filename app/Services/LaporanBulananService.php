<?php

namespace App\Services;

use App\Models\AbsensiSiswa;
use App\Models\JurnalGuru;
use App\Models\Master\MasterGuru;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterSiswa;
use App\Models\Master\MasterTahunAjaran;
use Carbon\Carbon;

/**
 * Laporan bulanan untuk pimpinan:
 *   - absensiSiswa(): matriks siswa × tanggal per rombel + ringkasan per rombel
 *   - jurnal():       keterisian jurnal mengajar per guru + daftar jurnal
 *
 * Hari sekolah = hari yang punya jadwal efektif (pola jam aktif) dan bukan libur nasional.
 */
class LaporanBulananService
{
    /** Kode harian siswa → [label, warna latar ARGB] */
    public const KODE_SISWA = [
        'H' => ['Hadir', 'FFE7F3EC'],
        'I' => ['Izin', 'FFE3EBFB'],
        'S' => ['Sakit', 'FFE3EBFB'],
        'A' => ['Alfa (tanpa keterangan)', 'FFFBE1DE'],
        'L' => ['Libur / bukan hari sekolah', 'FFD9D9D9'],
    ];

    private const STATUS_KE_KODE = ['hadir' => 'H', 'izin' => 'I', 'sakit' => 'S', 'alfa' => 'A'];

    /** Urutan saat jumlah jam seimbang: yang lebih "berat" menang */
    private const BOBOT = ['A' => 4, 'S' => 3, 'I' => 2, 'H' => 1];

    /** @var array<string, bool> cache hari sekolah per tanggal */
    private array $hariSekolah = [];

    public function __construct(private JadwalService $jadwal) {}

    /** @return array<int, array{tgl: Carbon, off: bool, keterangan: ?string}> */
    public function kalender(Carbon $bulan): array
    {
        $libur = HolidayService::getHolidays($bulan->year);
        $hari = [];
        for ($d = $bulan->copy()->startOfMonth(); $d->lte($bulan->copy()->endOfMonth()); $d->addDay()) {
            $namaLibur = $libur[$d->toDateString()] ?? null;
            $sekolah = ! $namaLibur && $this->hariSekolah($d);
            $hari[$d->day] = ['tgl' => $d->copy(), 'off' => ! $sekolah, 'keterangan' => $namaLibur ?? ($sekolah ? null : 'Bukan hari sekolah')];
        }

        return $hari;
    }

    private function hariSekolah(Carbon $tgl): bool
    {
        return $this->hariSekolah[$tgl->toDateString()] ??= $this->jadwal->jadwalEfektif($tgl->copy()->startOfDay())->isNotEmpty();
    }

    /**
     * Kode harian siswa = status terbanyak di jam-jam pelajaran hari itu
     * (seimbang → yang lebih berat). Jam alfa tetap dihitung terpisah agar bolos per jam terlihat.
     */
    public function absensiSiswa(Carbon $bulan): array
    {
        $hari = $this->kalender($bulan);
        $ambang = (int) app(PengaturanService::class)->nilai('ambang_alfa_siswa', 3);
        $ta = MasterTahunAjaran::where('is_active', true)->first();

        $rombel = MasterKelas::query()
            ->with('waliKelas:id,nama')
            ->when($ta, fn ($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->whereHas('siswa', fn ($q) => $q->where('status', 'aktif'))
            ->orderBy('tingkat')->orderBy('nama')
            ->get(['id', 'nama', 'tingkat', 'wali_kelas_id']);

        $absensi = AbsensiSiswa::query()
            ->whereBetween('tanggal', [$bulan->copy()->startOfMonth()->toDateString(), $bulan->copy()->endOfMonth()->toDateString()])
            ->whereIn('kelas_id', $rombel->pluck('id'))
            ->get(['siswa_id', 'tanggal', 'status'])
            ->groupBy('siswa_id');

        $kelas = $rombel->map(function (MasterKelas $k) use ($absensi, $hari, $ambang) {
            $siswa = MasterSiswa::where('kelas_id', $k->id)->where('status', 'aktif')->orderBy('nama')->get(['id', 'nama', 'nis', 'nisn']);

            $baris = $siswa->map(function ($s) use ($absensi, $hari) {
                $perHari = ($absensi[$s->id] ?? collect())->groupBy(fn ($a) => Carbon::parse($a->tanggal)->day);
                $sel = [];
                $total = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0];
                $jamAlfa = 0;

                foreach ($hari as $tgl => $h) {
                    $catatan = $perHari[$tgl] ?? collect();
                    if ($catatan->isEmpty()) {
                        $sel[$tgl] = $h['off'] ? 'L' : '';

                        continue;
                    }
                    $hitung = $catatan->map(fn ($a) => self::STATUS_KE_KODE[$a->status] ?? null)->filter()->countBy();
                    // jumlah jam diutamakan; bobot (< 10) hanya pemecah seri
                    $kode = $hitung->keys()->sortByDesc(fn ($k) => $hitung[$k] * 10 + self::BOBOT[$k])->first();
                    $jamAlfa += $catatan->where('status', 'alfa')->count();
                    if ($kode) {
                        $sel[$tgl] = $kode;
                        $total[$kode]++;
                    }
                }
                $tercatat = array_sum($total);

                return [
                    'nama' => $s->nama, 'nis' => $s->nis ?: $s->nisn, 'sel' => $sel, 'total' => $total,
                    'jam_alfa' => $jamAlfa, 'tercatat' => $tercatat,
                    'persen' => $tercatat ? round($total['H'] / $tercatat * 100, 1) : null,
                ];
            });

            $tercatat = $baris->sum('tercatat');

            return [
                'nama' => $k->nama,
                'wali' => $k->waliKelas?->nama,
                'siswa' => $baris->values()->all(),
                'ringkasan' => [
                    'jumlah_siswa' => $baris->count(),
                    'hari_tercatat' => collect($baris)->flatMap(fn ($b) => array_keys(array_filter($b['sel'], fn ($v) => $v !== '' && $v !== 'L')))->unique()->count(),
                    'H' => $baris->sum('total.H'), 'I' => $baris->sum('total.I'), 'S' => $baris->sum('total.S'), 'A' => $baris->sum('total.A'),
                    'jam_alfa' => $baris->sum('jam_alfa'),
                    'persen' => $tercatat ? round($baris->sum('total.H') / $tercatat * 100, 1) : null,
                    'alfa_berulang' => $baris->filter(fn ($b) => $b['total']['A'] >= $ambang)->pluck('nama')->values()->all(),
                ],
            ];
        });

        return [
            'bulan' => $bulan->copy()->startOfMonth(),
            'hari' => $hari,
            'hari_sekolah' => collect($hari)->where('off', false)->count(),
            'ambang' => $ambang,
            'kelas' => $kelas->values()->all(),
        ];
    }

    /**
     * Keterisian jurnal per guru. Pertemuan dihitung seperti di aplikasi guru (blok yang dipisah
     * istirahat = satu pertemuan); jam tergeser/ditiadakan tidak wajib; hanya sampai hari ini.
     */
    public function jurnal(Carbon $bulan): array
    {
        $hari = $this->kalender($bulan);
        $hariIni = now();
        $tanggalSekolah = collect($hari)->where('off', false)->pluck('tgl')->filter(fn ($t) => $t->lte($hariIni->copy()->endOfDay()));

        $guru = MasterGuru::where('status', 'aktif')->orderBy('nama')->get(['id', 'nama', 'nip']);
        $rekap = $guru->map(function ($g) use ($tanggalSekolah, $hariIni) {
            $n = ['wajib' => 0, 'diisi' => 0, 'sebagian' => 0, 'belum' => 0];
            foreach ($tanggalSekolah as $tgl) {
                $harian = $this->jadwal->jadwalGuruHarian($g->id, $tgl, $hariIni);
                foreach ($harian['items'] as $it) {
                    if ($it['tipe'] !== 'blok' || $it['lanjutan'] || $it['jurnal'] === 'tidak_wajib' || $it['fase'] === 'mendatang') {
                        continue;
                    }
                    $n['wajib']++;
                    $n[$it['jurnal']]++;
                }
            }

            return ['nama' => $g->nama, 'nip' => $g->nip, ...$n,
                'persen' => $n['wajib'] ? round($n['diisi'] / $n['wajib'] * 100, 1) : null];
        })->values()->all();

        $detail = JurnalGuru::query()
            ->with(['guru:id,nama', 'kelas:id,nama', 'mapel:id,nama'])
            ->whereBetween('tanggal', [$bulan->copy()->startOfMonth()->toDateString(), $bulan->copy()->endOfMonth()->toDateString()])
            ->where('status', 'diisi')
            ->orderBy('tanggal')->orderBy('jam_ke')
            ->get()
            // satu baris per guru + tanggal + mapel + materi (gabungkan jam & rombel)
            ->groupBy(fn ($j) => $j->guru_id.'|'.$j->tanggal->toDateString().'|'.$j->mapel_id.'|'.md5((string) $j->materi))
            ->map(function ($grup) {
                $j = $grup->first();

                return [
                    'tanggal' => $j->tanggal,
                    'guru' => $j->guru?->nama ?? '-',
                    'mapel' => $j->mapel?->nama ?? '-',
                    'kelas' => $grup->pluck('kelas.nama')->filter()->unique()->implode(', '),
                    'jam' => $grup->pluck('jam_ke')->unique()->sort()->implode(', '),
                    'materi' => (string) $j->materi,
                    'kegiatan' => (string) $j->kegiatan,
                    'hadir' => $grup->max('jumlah_hadir'),
                    'tidak_hadir' => $grup->max(fn ($x) => (int) $x->jumlah_izin + (int) $x->jumlah_sakit + (int) $x->jumlah_alfa),
                ];
            })
            ->sortBy([['tanggal', 'asc'], ['guru', 'asc']])
            ->values()->all();

        $wajib = array_sum(array_column($rekap, 'wajib'));

        return [
            'bulan' => $bulan->copy()->startOfMonth(),
            'sampai' => min($hariIni->copy(), $bulan->copy()->endOfMonth()),
            'guru' => $rekap,
            'detail' => $detail,
            'total' => [
                'wajib' => $wajib,
                'diisi' => array_sum(array_column($rekap, 'diisi')),
                'persen' => $wajib ? round(array_sum(array_column($rekap, 'diisi')) / $wajib * 100, 1) : null,
            ],
        ];
    }
}
