<?php

use App\Enums\JenisAbsenGuru;
use App\Enums\KategoriAbsensi;
use App\Models\AturanAbsensi;
use App\Services\AturanAbsensiService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Senin 9 Nov 2026 (default guru: buka 06:00, tepat 07:00 + toleransi 10', tutup 12:00, pulang 15:00–18:00)
// Selasa 10 Nov 2026 (tepat 07:15, pulang 14:00)

beforeEach(function () {
    $this->aturan = app(AturanAbsensiService::class);
    $this->masuk = fn (string $waktu, bool $sudahMasuk = false) => $this->aturan->evaluasi(
        KategoriAbsensi::GURU, JenisAbsenGuru::MASUK, Carbon::parse($waktu), $sudahMasuk, false
    );
    $this->pulang = fn (string $waktu, ?string $alasan = null, bool $sudahPulang = false) => $this->aturan->evaluasi(
        KategoriAbsensi::GURU, JenisAbsenGuru::PULANG, Carbon::parse($waktu), true, $sudahPulang, $alasan
    );
});

test('check-in sebelum jam buka ditolak', function () {
    $hasil = ($this->masuk)('2026-11-09 05:30');

    expect($hasil['boleh'])->toBeFalse()
        ->and($hasil['pesan'])->toContain('06:00');
});

test('check-in tepat waktu dan dalam toleransi tercatat hadir', function () {
    expect(($this->masuk)('2026-11-09 06:55')['status'])->toBe('hadir')
        ->and(($this->masuk)('2026-11-09 07:10')['status'])->toBe('hadir');
});

test('check-in lewat toleransi tercatat terlambat dengan menitnya', function () {
    $hasil = ($this->masuk)('2026-11-09 07:25');

    expect($hasil['boleh'])->toBeTrue()
        ->and($hasil['status'])->toBe('terlambat')
        ->and($hasil['menit_terlambat'])->toBe(25);
});

test('batas tepat waktu berbeda per hari (Senin 07:00, Selasa 07:15)', function () {
    expect(($this->masuk)('2026-11-09 07:20')['status'])->toBe('terlambat')
        ->and(($this->masuk)('2026-11-10 07:20')['status'])->toBe('hadir');
});

test('check-in setelah jam tutup ditolak dan diarahkan ke izin', function () {
    $hasil = ($this->masuk)('2026-11-09 12:30');

    expect($hasil['boleh'])->toBeFalse()
        ->and($hasil['pesan'])->toContain('izin');
});

test('check-in kedua kali ditolak', function () {
    expect(($this->masuk)('2026-11-09 07:00', sudahMasuk: true)['boleh'])->toBeFalse();
});

test('pulang sebelum waktunya butuh alasan, lalu tercatat pulang cepat', function () {
    $tanpaAlasan = ($this->pulang)('2026-11-10 11:00');
    $denganAlasan = ($this->pulang)('2026-11-10 11:00', 'Dinas ke Dikbud provinsi');

    expect($tanpaAlasan['boleh'])->toBeFalse()
        ->and($tanpaAlasan['butuh_alasan'])->toBeTrue()
        ->and($denganAlasan['status'])->toBe('pulang_cepat');
});

test('pulang cepat bisa dimatikan admin', function () {
    $this->aturan->pasangDefault();
    AturanAbsensi::where('kategori', 'guru')->where('hari', 'selasa')->update(['pulang_cepat_diizinkan' => false]);

    $hasil = ($this->pulang)('2026-11-10 11:00', 'Ada urusan');

    expect($hasil['boleh'])->toBeFalse()
        ->and($hasil['butuh_alasan'])->toBeFalse();
});

test('pulang normal, setelah jam tutup, dan pulang kedua kali', function () {
    expect(($this->pulang)('2026-11-10 14:05')['status'])->toBe('hadir')
        ->and(($this->pulang)('2026-11-10 18:30')['boleh'])->toBeFalse()
        ->and(($this->pulang)('2026-11-10 15:00', sudahPulang: true)['boleh'])->toBeFalse();
});

test('pulang tanpa absen masuk ditolak', function () {
    $hasil = $this->aturan->evaluasi(KategoriAbsensi::GURU, JenisAbsenGuru::PULANG, Carbon::parse('2026-11-10 14:30'), false, false);

    expect($hasil['boleh'])->toBeFalse()
        ->and($hasil['pesan'])->toContain('belum absen masuk');
});

test('hari non-kerja (Sabtu, Minggu) ditolak', function () {
    expect(($this->masuk)('2026-11-14 07:00')['boleh'])->toBeFalse()
        ->and(($this->masuk)('2026-11-15 07:00')['boleh'])->toBeFalse();
});

test('aturan yang diubah admin dipakai, bukan default', function () {
    $this->aturan->pasangDefault();
    AturanAbsensi::where('kategori', 'guru')->where('hari', 'senin')->update(['masuk_tepat' => '07:30', 'toleransi_menit' => 0]);

    expect(($this->masuk)('2026-11-09 07:25')['status'])->toBe('hadir')
        ->and(($this->masuk)('2026-11-09 07:31')['status'])->toBe('terlambat');
});

test('pasangDefault tidak menimpa perubahan admin', function () {
    $this->aturan->pasangDefault();
    AturanAbsensi::where('kategori', 'tendik')->where('hari', 'rabu')->update(['masuk_tepat' => '08:00']);

    $this->aturan->pasangDefault();

    expect(AturanAbsensi::count())->toBe(12)
        ->and(AturanAbsensi::where('kategori', 'tendik')->where('hari', 'rabu')->value('masuk_tepat'))->toStartWith('08:00');
});
