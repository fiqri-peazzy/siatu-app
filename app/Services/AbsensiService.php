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
    public function catatAbsenGuru(array $data, mixed $foto = null): \App\Models\AbsensiGuru
    {
        $absen = \App\Models\AbsensiGuru::create($data);

        if ($foto) {
            $path = '';
            $folder = 'absensi/' . date('Y/m');
            
            if ($foto instanceof \Illuminate\Http\UploadedFile) {
                $path = $foto->store($folder, 'public');
            } elseif (is_string($foto) && str_starts_with($foto, 'data:image')) {
                // Decode base64
                $image_parts = explode(";base64,", $foto);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = uniqid() . '.' . $image_type;
                $path = $folder . '/' . $filename;
                
                \Illuminate\Support\Facades\Storage::disk('public')->put($path, $image_base64);
            }

            if ($path) {
                $absen->update(['foto_path' => $path]);
            }
        }

        // Trigger Event
        \App\Events\AbsensiGuruDicatat::dispatch($absen);

        return $absen;
    }
    public function catatAbsenTendik(array $data, mixed $foto = null): \App\Models\AbsensiTendik
    {
        $absen = \App\Models\AbsensiTendik::create($data);

        if ($foto) {
            $path = '';
            $folder = 'absensi_tendik/' . date('Y/m');
            
            if ($foto instanceof \Illuminate\Http\UploadedFile) {
                $path = $foto->store($folder, 'public');
            } elseif (is_string($foto) && str_starts_with($foto, 'data:image')) {
                // Decode base64
                $image_parts = explode(";base64,", $foto);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = uniqid() . '.' . $image_type;
                $path = $folder . '/' . $filename;
                
                \Illuminate\Support\Facades\Storage::disk('public')->put($path, $image_base64);
            }

            if ($path) {
                $absen->update(['foto_path' => $path]);
            }
        }

        return $absen;
    }
}
