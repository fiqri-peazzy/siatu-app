<?php

use App\Support\Berkas;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

function pakaiR2S3(array $tambahan = []): void
{
    config(['filesystems.disks.r2' => [
        'driver' => 's3', 'key' => 'kunci-uji', 'secret' => 'rahasia-uji', 'region' => 'auto',
        'bucket' => 'smamgo-uji', 'endpoint' => 'https://akunuji.r2.cloudflarestorage.com',
        'use_path_style_endpoint' => true, 'url' => null, 'publik' => false, ...$tambahan,
    ]]);
    Storage::forgetDisk('r2');
    Cache::flush();
}

it('mode privat: signed URL R2 yang kedaluwarsa & stabil selama di-cache', function () {
    pakaiR2S3();
    $url = Berkas::url('absensi/2026/11/abc.jpg');

    expect($url)->toStartWith('https://akunuji.r2.cloudflarestorage.com/smamgo-uji/absensi/2026/11/abc.jpg?')
        ->toContain('X-Amz-Signature=')
        ->toContain('X-Amz-Expires=7200')
        ->and(Berkas::url('absensi/2026/11/abc.jpg'))->toBe($url); // cache → URL sama, gambar bisa di-cache browser
});

it('mode publik: memakai custom domain', function () {
    pakaiR2S3(['publik' => true, 'url' => 'https://storage.smamuhkotagorontalo.my.id/']);

    expect(Berkas::url('/foto_guru/x.jpg'))->toBe('https://storage.smamuhkotagorontalo.my.id/foto_guru/x.jpg');
});

it('publik=true tanpa URL tetap aman (jatuh ke signed URL)', function () {
    pakaiR2S3(['publik' => true, 'url' => '']);

    expect(Berkas::url('a.jpg'))->toContain('X-Amz-Signature=');
});

it('path kosong menghasilkan null', function () {
    expect(Berkas::url(null))->toBeNull()->and(Berkas::url(''))->toBeNull();
});

it('nama file acak tidak bisa ditebak & unik', function () {
    $nama = collect(range(1, 200))->map(fn () => Berkas::namaAcak('JPG'));

    expect($nama->unique())->toHaveCount(200)
        ->and($nama->first())->toMatch('/^[0-9a-z]{26}[0-9A-Za-z]{16}\.jpg$/');
});
