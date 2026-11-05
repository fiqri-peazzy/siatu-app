<?php

namespace App\Services;

use App\Enums\Hari;
use App\Enums\JenisAbsenGuru;
use App\Enums\KategoriAbsensi;
use App\Enums\StatusAbsensiGuru;
use App\Models\AturanAbsensi;
use Carbon\Carbon;

/**
 * Aturan jendela waktu check-in / check-out (tabel aturan_absensis).
 * Satu-satunya sumber kebenaran untuk portal guru & tendik.
 */
class AturanAbsensiService
{
    /**
     * Nilai awal, diturunkan dari jadwal 2026/2027:
     * Senin mulai 07.00 (upacara) & selesai 15.00; Selasa–Jumat mulai 07.15 & selesai 14.00.
     * Admin dapat mengubah semuanya di Pengaturan Sistem.
     */
    public const DEFAULT = [
        'guru' => [
            'senin' => [true, '06:00', '07:00', 10, '12:00', '15:00', '18:00'],
            'selasa' => [true, '06:00', '07:15', 10, '12:00', '14:00', '18:00'],
            'rabu' => [true, '06:00', '07:15', 10, '12:00', '14:00', '18:00'],
            'kamis' => [true, '06:00', '07:15', 10, '12:00', '14:00', '18:00'],
            'jumat' => [true, '06:00', '07:15', 10, '12:00', '14:00', '18:00'],
            'sabtu' => [false, '06:00', '07:15', 10, '12:00', '14:00', '18:00'],
        ],
        'tendik' => [
            'senin' => [true, '06:00', '07:00', 10, '12:00', '15:00', '18:00'],
            'selasa' => [true, '06:00', '07:15', 10, '12:00', '15:00', '18:00'],
            'rabu' => [true, '06:00', '07:15', 10, '12:00', '15:00', '18:00'],
            'kamis' => [true, '06:00', '07:15', 10, '12:00', '15:00', '18:00'],
            'jumat' => [true, '06:00', '07:15', 10, '12:00', '15:00', '18:00'],
            'sabtu' => [false, '06:00', '07:15', 10, '12:00', '15:00', '18:00'],
        ],
    ];

    /** Aturan untuk kategori & tanggal; jatuh ke DEFAULT bila belum disimpan admin. */
    public function aturan(KategoriAbsensi $kategori, Carbon $tanggal): ?AturanAbsensi
    {
        $hari = Hari::fromIso($tanggal->dayOfWeekIso);
        if (! $hari) {
            return null; // Minggu
        }

        return AturanAbsensi::query()
            ->where('kategori', $kategori->value)
            ->where('hari', $hari->value)
            ->first()
            ?? $this->dariDefault($kategori, $hari);
    }

    /** Buat/isi ulang semua baris aturan dari DEFAULT (tidak menimpa yang sudah ada kecuali $timpa). */
    public function pasangDefault(bool $timpa = false): void
    {
        foreach (KategoriAbsensi::cases() as $kategori) {
            foreach (Hari::cases() as $hari) {
                $nilai = $this->dariDefault($kategori, $hari)->only((new AturanAbsensi)->getFillable());
                $kunci = ['kategori' => $kategori->value, 'hari' => $hari->value];

                $timpa
                    ? AturanAbsensi::query()->updateOrCreate($kunci, $nilai)
                    : AturanAbsensi::query()->firstOrCreate($kunci, $nilai);
            }
        }
    }

    /**
     * Evaluasi apakah absen boleh dicatat sekarang, dan dengan status apa.
     *
     * @return array{boleh: bool, status: ?string, pesan: string, butuh_alasan: bool, menit_terlambat: int}
     */
    public function evaluasi(
        KategoriAbsensi $kategori,
        JenisAbsenGuru $jenis,
        Carbon $waktu,
        bool $sudahMasuk,
        bool $sudahPulang,
        ?string $alasan = null,
    ): array {
        $aturan = $this->aturan($kategori, $waktu);

        if (! $aturan || ! $aturan->hari_kerja) {
            return $this->tolak('Hari ini bukan hari kerja.');
        }

        if ($libur = $this->namaLibur($waktu)) {
            return $this->tolak("Hari ini libur: {$libur}.");
        }

        $jam = fn (string $kolom) => $waktu->copy()->setTimeFromTimeString($aturan->jam($kolom));

        if ($jenis === JenisAbsenGuru::MASUK) {
            if ($sudahMasuk) {
                return $this->tolak('Anda sudah absen masuk hari ini.');
            }
            if ($waktu->lt($jam('masuk_buka'))) {
                return $this->tolak("Absen masuk baru dibuka pukul {$aturan->jam('masuk_buka')}.");
            }
            if ($waktu->gt($jam('masuk_tutup'))) {
                return $this->tolak("Absen masuk sudah ditutup pukul {$aturan->jam('masuk_tutup')}. Silakan ajukan izin ke admin.");
            }

            $batas = $jam('masuk_tepat')->addMinutes($aturan->toleransi_menit);
            if ($waktu->gt($batas)) {
                $menit = (int) ceil($jam('masuk_tepat')->diffInMinutes($waktu));

                return $this->terima(StatusAbsensiGuru::TERLAMBAT, "Tercatat terlambat {$menit} menit.", $menit);
            }

            return $this->terima(StatusAbsensiGuru::HADIR, 'Absen masuk tepat waktu.');
        }

        // ---- PULANG ----
        if (! $sudahMasuk) {
            return $this->tolak('Anda belum absen masuk hari ini.');
        }
        if ($sudahPulang) {
            return $this->tolak('Anda sudah absen pulang hari ini.');
        }
        if ($waktu->gt($jam('pulang_tutup'))) {
            return $this->tolak("Absen pulang sudah ditutup pukul {$aturan->jam('pulang_tutup')}.");
        }

        if ($waktu->lt($jam('pulang_buka'))) {
            if (! $aturan->pulang_cepat_diizinkan) {
                return $this->tolak("Absen pulang baru dibuka pukul {$aturan->jam('pulang_buka')}.");
            }
            if (blank($alasan)) {
                return [...$this->tolak("Belum jam pulang ({$aturan->jam('pulang_buka')}). Isi alasan untuk pulang lebih awal."), 'butuh_alasan' => true];
            }

            return $this->terima(StatusAbsensiGuru::PULANG_CEPAT, 'Tercatat pulang lebih awal.');
        }

        return $this->terima(StatusAbsensiGuru::HADIR, 'Absen pulang tercatat.');
    }

    // ------------------------------------------------------------------

    private function dariDefault(KategoriAbsensi $kategori, Hari $hari): AturanAbsensi
    {
        [$kerja, $buka, $tepat, $toleransi, $tutup, $pulangBuka, $pulangTutup] = self::DEFAULT[$kategori->value][$hari->value];

        return new AturanAbsensi([
            'kategori' => $kategori,
            'hari' => $hari,
            'hari_kerja' => $kerja,
            'masuk_buka' => $buka,
            'masuk_tepat' => $tepat,
            'toleransi_menit' => $toleransi,
            'masuk_tutup' => $tutup,
            'pulang_buka' => $pulangBuka,
            'pulang_tutup' => $pulangTutup,
            'pulang_cepat_diizinkan' => true,
        ]);
    }

    private function namaLibur(Carbon $tanggal): ?string
    {
        return HolidayService::getHolidays($tanggal->year)[$tanggal->format('Y-m-d')] ?? null;
    }

    private function tolak(string $pesan): array
    {
        return ['boleh' => false, 'status' => null, 'pesan' => $pesan, 'butuh_alasan' => false, 'menit_terlambat' => 0];
    }

    private function terima(StatusAbsensiGuru $status, string $pesan, int $menitTerlambat = 0): array
    {
        return ['boleh' => true, 'status' => $status->value, 'pesan' => $pesan, 'butuh_alasan' => false, 'menit_terlambat' => $menitTerlambat];
    }
}
