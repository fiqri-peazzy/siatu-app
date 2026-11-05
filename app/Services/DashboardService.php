<?php

namespace App\Services;

use App\Enums\JenisSesi;
use App\Enums\JenisSlotJam;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\AbsensiTendik;
use App\Models\JadwalSesi;
use App\Models\JurnalGuru;
use App\Models\Master\MasterGuru;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterPengampu;
use App\Models\Master\MasterSiswa;
use App\Models\Master\MasterTahunAjaran;
use App\Models\Master\MasterTendik;
use App\Models\Pengaturan;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/** Angka & daftar untuk dashboard Kepala Sekolah dan Admin TU. */
class DashboardService
{
    public function __construct(private JadwalService $jadwal) {}

    /** Kehadiran pegawai hari ini: hadir (tepat), terlambat, izin/sakit/DL, belum absen */
    public function kehadiranPegawai(string $jenis, ?Carbon $hari = null): array
    {
        $hari ??= today();
        [$total, $absen] = $jenis === 'guru'
            ? [MasterGuru::where('status', 'aktif')->count(), AbsensiGuru::query()]
            : [MasterTendik::where('status', 'aktif')->count(), AbsensiTendik::query()];

        $status = $absen->whereDate('tanggal', $hari)->where('jenis', 'masuk')->pluck('status')->countBy();
        $tercatat = $status->sum();

        return [
            'total' => $total,
            'hadir' => (int) ($status['hadir'] ?? 0),
            'terlambat' => (int) ($status['terlambat'] ?? 0),
            'izin' => (int) (($status['izin'] ?? 0) + ($status['sakit'] ?? 0) + ($status['dinas_luar'] ?? 0)),
            'belum' => max(0, $total - $tercatat),
            'persen' => $total ? round((($status['hadir'] ?? 0) + ($status['terlambat'] ?? 0)) / $total * 100) : 0,
        ];
    }

    /** Kehadiran siswa hari ini (per siswa, status terburuk di hari itu) */
    public function kehadiranSiswa(?Carbon $hari = null): array
    {
        $hari ??= today();
        $peringkat = ['alfa' => 4, 'sakit' => 3, 'izin' => 2, 'hadir' => 1];
        $perSiswa = AbsensiSiswa::whereDate('tanggal', $hari)->get(['siswa_id', 'status'])
            ->groupBy('siswa_id')
            ->map(fn ($r) => $r->sortByDesc(fn ($x) => $peringkat[$x->status] ?? 0)->first()->status)
            ->countBy();

        return [
            'total' => MasterSiswa::where('status', 'aktif')->count(),
            'tercatat' => $perSiswa->sum(),
            'hadir' => (int) ($perSiswa['hadir'] ?? 0),
            'izin' => (int) (($perSiswa['izin'] ?? 0) + ($perSiswa['sakit'] ?? 0)),
            'alfa' => (int) ($perSiswa['alfa'] ?? 0),
        ];
    }

    /**
     * KBM hari ini: sesi yang sedang berlangsung + progres jurnal sesi yang sudah selesai.
     *
     * @return array{berlangsung: list<array>, slot: ?array, jurnal_terisi: int, jurnal_wajib: int}
     */
    public function kbm(?Carbon $sekarang = null): array
    {
        $sekarang ??= now();
        $jam = $sekarang->format('H:i');
        $efektif = $this->jadwal->jadwalEfektif($sekarang->copy()->startOfDay())
            ->filter(fn ($e) => $e['jenis'] === JenisSlotJam::PELAJARAN->value && $e['slot'] && ! in_array($e['status'], ['tergeser', 'ditiadakan'], true));

        $slotKini = $efektif->first(fn ($e) => $e['mulai'] <= $jam && $e['selesai'] > $jam);
        $slotSelesai = $efektif->filter(fn ($e) => $e['selesai'] <= $jam)->pluck('slot.id');

        $guruHadir = AbsensiGuru::whereDate('tanggal', $sekarang)->where('jenis', 'masuk')
            ->whereIn('status', ['hadir', 'terlambat'])->pluck('guru_id')->flip();
        $jurnalSesi = JurnalGuru::whereDate('tanggal', $sekarang)->where('status', 'diisi')
            ->whereNotNull('jadwal_sesi_id')->pluck('jadwal_sesi_id')->flip();

        $berlangsung = [];
        if ($slotKini) {
            $berlangsung = JadwalSesi::with(['mapel:id,nama', 'guru:id,nama', 'kelas:id,nama'])
                ->where('slot_jam_id', $slotKini['slot']->id)
                ->get()
                ->map(fn ($s) => [
                    'mapel' => $s->jenis === JenisSesi::KEGIATAN ? ($s->label ?? 'Kegiatan') : ($s->mapel?->nama ?? '-'),
                    'kelas' => $s->kelas->pluck('nama')->implode(' + '),
                    'guru' => $s->guru?->nama,
                    'kegiatan' => $s->jenis === JenisSesi::KEGIATAN,
                    'guru_hadir' => $s->guru_id ? isset($guruHadir[$s->guru_id]) : null,
                    'jurnal' => isset($jurnalSesi[$s->id]),
                ])
                ->sortBy('kelas')->values()->all();
        }

        $wajib = JadwalSesi::whereIn('slot_jam_id', $slotSelesai)->where('jenis', '!=', JenisSesi::KEGIATAN)->whereNotNull('guru_id')->pluck('id');

        return [
            'berlangsung' => $berlangsung,
            'slot' => $slotKini ? ['jam_ke' => $slotKini['jam_ke'], 'mulai' => $slotKini['mulai'], 'selesai' => $slotKini['selesai']] : null,
            'jurnal_wajib' => $wajib->count(),
            'jurnal_terisi' => $wajib->filter(fn ($id) => isset($jurnalSesi[$id]))->count(),
        ];
    }

    /** Tren % kehadiran guru n hari kerja terakhir (termasuk hari ini) */
    public function trenKehadiranGuru(int $hari = 7): array
    {
        $total = max(1, MasterGuru::where('status', 'aktif')->count());
        $libur = HolidayService::getHolidays(now()->year);
        $tanggal = collect();
        for ($d = today(); $tanggal->count() < $hari && $d->gt(today()->subDays(30)); $d = $d->copy()->subDay()) {
            if (! $d->isWeekend() && ! isset($libur[$d->toDateString()])) {
                $tanggal->prepend($d->copy());
            }
        }

        $hadir = AbsensiGuru::whereBetween('tanggal', [$tanggal->first()?->toDateString(), today()->toDateString()])
            ->where('jenis', 'masuk')->whereIn('status', ['hadir', 'terlambat'])
            ->get(['tanggal'])->countBy(fn ($a) => Carbon::parse($a->tanggal)->toDateString());

        return $tanggal->map(fn ($d) => [
            'label' => $d->locale('id')->isoFormat('ddd D'),
            'persen' => (int) round(($hadir[$d->toDateString()] ?? 0) / $total * 100),
            'hari_ini' => $d->isToday(),
        ])->values()->all();
    }

    /** Guru aktif yang belum absen masuk hari ini */
    public function guruBelumAbsen(int $batas = 6): array
    {
        $sudah = AbsensiGuru::whereDate('tanggal', today())->where('jenis', 'masuk')->pluck('guru_id');

        return MasterGuru::where('status', 'aktif')->whereNotIn('id', $sudah)->orderBy('nama')->limit($batas)->pluck('nama')->all();
    }

    /** Siswa dengan alfa ≥ 3 hari pada bulan berjalan (aturan bisnis CLAUDE.md 5.3) */
    public function siswaAlfaBerulang(?int $minimal = null, int $batas = 6): array
    {
        $minimal ??= (int) app(PengaturanService::class)->nilai('ambang_alfa_siswa', 3);

        return AbsensiSiswa::query()
            ->where('status', 'alfa')
            ->whereYear('tanggal', now()->year)->whereMonth('tanggal', now()->month)
            ->select('siswa_id', DB::raw('COUNT(DISTINCT tanggal) as hari'))
            ->groupBy('siswa_id')
            ->having('hari', '>=', $minimal)
            ->orderByDesc('hari')
            ->limit($batas)
            ->get()
            ->map(function ($r) {
                $s = MasterSiswa::with('kelas:id,nama')->find($r->siswa_id, ['id', 'nama', 'kelas_id']);

                return ['nama' => $s?->nama ?? '-', 'kelas' => $s?->kelas?->nama ?? '-', 'hari' => (int) $r->hari];
            })->all();
    }

    /** Antrian administrasi surat */
    public function surat(): array
    {
        return [
            'masuk_baru' => SuratMasuk::where('status', 'baru')->count(),
            'masuk_bulan_ini' => SuratMasuk::whereYear('tanggal_terima', now()->year)->whereMonth('tanggal_terima', now()->month)->count(),
            'keluar_draft' => SuratKeluar::whereIn('status', ['draft', 'revisi'])->count(),
            'keluar_menunggu' => SuratKeluar::where('status', 'menunggu_approval')->count(),
            'terbaru' => SuratMasuk::where('status', 'baru')->latest('tanggal_terima')->limit(5)->get(['id', 'perihal', 'tanggal_terima', 'no_agenda'])
                ->map(fn ($s) => ['id' => $s->id, 'perihal' => $s->perihal, 'no' => $s->no_agenda, 'tanggal' => Carbon::parse($s->tanggal_terima)->locale('id')->isoFormat('D MMM')])->all(),
        ];
    }

    /** Ringkasan data master & kelengkapan konfigurasi untuk Admin TU */
    public function kelengkapanData(): array
    {
        $ta = MasterTahunAjaran::where('is_active', true)->first();
        $rombel = MasterKelas::query()->when($ta, fn ($q) => $q->where('tahun_ajaran_id', $ta->id))->withCount('siswa')->get(['id', 'nama']);
        $lokasi = Pengaturan::whereIn('key', ['lat_sekolah', 'lng_sekolah', 'radius_absen'])->pluck('value', 'key');

        return [
            'tahun_ajaran' => $ta ? "{$ta->nama} · {$ta->semester}" : null,
            'jumlah' => [
                'Guru' => MasterGuru::where('status', 'aktif')->count(),
                'Tendik' => MasterTendik::where('status', 'aktif')->count(),
                'Siswa' => MasterSiswa::where('status', 'aktif')->count(),
                'Rombel' => $rombel->count(),
            ],
            'rombel_kosong' => $rombel->where('siswa_count', 0)->pluck('nama')->all(),
            'pengampu_kosong' => $ta ? MasterPengampu::where('tahun_ajaran_id', $ta->id)->whereNull('guru_id')->count() : 0,
            'lokasi_diatur' => $lokasi->count() === 3 && (float) ($lokasi['lat_sekolah'] ?? 0) !== 0.0,
        ];
    }
}
