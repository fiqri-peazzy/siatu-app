<?php

namespace App\Services;

use App\Models\Pengaturan;

class AbsensiService
{
    /**
     * Hitung jarak (dalam meter) antara 2 titik koordinat (Haversine formula).
     */
    public function hitungJarak(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return (int) round($distance);
    }

    /**
     * Cek apakah jarak saat ini masih dalam radius sekolah.
     */
    public function validasiLokasi(float $lat, float $lon): array
    {
        $latSekolah = (float) Pengaturan::where('key', 'lat_sekolah')->value('value');
        $lonSekolah = (float) Pengaturan::where('key', 'lng_sekolah')->value('value');
        $radiusMaksimal = (int) Pengaturan::where('key', 'radius_absen')->value('value');

        $jarak = $this->hitungJarak($lat, $lon, $latSekolah, $lonSekolah);

        return [
            'valid' => $jarak <= $radiusMaksimal,
            'jarak' => $jarak,
            'radius_maks' => $radiusMaksimal
        ];
    }

    /**
     * Cek apakah waktu absen masuk sudah melewati batas telat.
     */
    public function isTerlambat(\Carbon\Carbon $waktuAbsen): bool
    {
        $batasTelat = Pengaturan::where('key', 'batas_telat')->value('value') ?? '07:45';
        $waktuBatas = \Carbon\Carbon::createFromFormat('H:i', $batasTelat)->setDate(
            $waktuAbsen->year, $waktuAbsen->month, $waktuAbsen->day
        );

        return $waktuAbsen->greaterThan($waktuBatas);
    }

    /**
     * Catat absensi guru (masuk/pulang).
     */
    public function catatAbsenGuru(array $data, ?\Illuminate\Http\UploadedFile $foto): \App\Models\AbsensiGuru
    {
        if ($foto) {
            $tahun = date('Y');
            $bulan = date('m');
            $data['foto_path'] = $foto->store("absensi/{$tahun}/{$bulan}", 'local');
        }

        $absen = \App\Models\AbsensiGuru::create($data);

        // Fire event
        event(new \App\Events\AbsensiGuruDicatat($absen));

        return $absen;
    }
}
