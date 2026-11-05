{{--
    Kop resmi untuk export Excel berbasis view (tepat 4 baris agar posisi tabel tetap).
    Logo, garis ganda, dan tanda tangan ditambahkan oleh KopResmi::finalisasiDokumen() di AfterSheet.
    @param int $kolom  jumlah kolom tabel
--}}
@php
    $s = config('sekolah');
    $induk = collect(explode('|', (string) ($s['induk'] ?? '')))->map(fn ($b) => trim($b))->filter()->implode(' — ');
    $kontak = collect([
        $s['tagline'] ?? null,
        collect([$s['alamat'] ?? null, $s['kota'] ?? null, $s['provinsi'] ?? null])->filter()->implode(', '),
        ! empty($s['telepon']) ? 'Telp. '.$s['telepon'] : null,
        ! empty($s['email']) ? $s['email'] : null,
        ! empty($s['npsn']) ? 'NPSN '.$s['npsn'] : null,
    ])->filter()->implode(' | ');
@endphp
<tr><th colspan="{{ $kolom }}" style="text-align: center; font-size: 10px; font-weight: bold;">{{ mb_strtoupper($induk) }}</th></tr>
<tr><th colspan="{{ $kolom }}" style="text-align: center; font-size: 16px; font-weight: bold;">{{ mb_strtoupper($s['nama'] ?? '') }}{{ ! empty($s['akreditasi']) ? ' — TERAKREDITASI '.mb_strtoupper($s['akreditasi']) : '' }}</th></tr>
<tr><th colspan="{{ $kolom }}" style="text-align: center; font-size: 9px;">{{ $kontak }}</th></tr>
<tr><th colspan="{{ $kolom }}"></th></tr>
