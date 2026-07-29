<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\MasterJadwalPelajaran;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterMapel;
use App\Models\Master\MasterGuru;
use App\Models\Master\MasterTahunAjaran;
use Illuminate\Support\Str;

class MasterJadwalPelajaranSeeder extends Seeder
{
    public function run()
    {
        MasterJadwalPelajaran::truncate();

        // Ensure active tahun ajaran
        $tahunAjaran = MasterTahunAjaran::where('is_active', true)->first();
        if (!$tahunAjaran) {
            $tahunAjaran = MasterTahunAjaran::firstOrCreate(
                ['is_active' => true],
                ['nama' => '2024/2025', 'semester' => 'Ganjil']
            );
        }
        $tahunAjaranId = $tahunAjaran->id;

        // Ensure we have some teachers
        if (MasterGuru::count() == 0) {
            MasterGuru::create(['nama' => 'Guru Dummy', 'nip' => '123456789']);
        }
        $gurus = MasterGuru::pluck('id')->toArray();

        $rawData = <<<EOD
Hari	Waktu	JP	KELAS X	KELAS XI	KELAS XII
SENIN	07.00-07.45		UPACARA BENDERA
	07.45-08.15	I	Fisika	Ekonomi 	Informatika 	Sejarah 	BING TKA
	08.15-09.45	II	Fisika	Ekonomi 	Informatika 	Sejarah	BING TKA
	08.45-09.15	III	Fisika	Ekonomi 	Informatika 	Sejarah	TKA PILIHAN
	09.15-09.45	IV	Biologi	Sejarah	Matematika	Sosiologi	TKA PILIHAN
	09.45.10.15		ISTIRAHAT/ SHALAT DHUHA
	10.15-10.45	V	Biologi	Sejarah	Matematika	Sosiologi	BIND TKA
	10.45-11.15	VI	Biologi	Sejarah	Matematika	Sosiologi	BIND TKA
	11.15-11.45	VII	Informatika 	Sosiologi 	Biologi	Geografi	TKA PILIHAN
	11.45-13.00		ISHOMA
	13.00-13.30	VIII	Informatika 	Sosiologi 	Biologi	Geografi	TKA PILIHAN
	13.30.14.00	IX	Informatika 	Sosiologi 	Biologi	Geografi	
SELASA	07.15-07.45	I	Tahfidz	Bahasa Arab	Bahasa Inggris
	07.45-08.15	II	Tahfidz	Bahasa Arab	Bahasa Inggris
	08.15-09.45	III	Matematika	Geografi	BIND	Sejarah 	BIND TKA
	08.45-09.15	IV	Matematika	Geografi	BIND	Sejarah 	BIND TKA
	09.15-09.45	V	Matematika	Geografi	BIND*	Sejarah 	TKA PILIHAN
	09.45.10.15		ISTIRAHAT/ SHALAT DHUHA
	10.15-10.45	VI	Kimia	Antropologi 	OPSI	Ekonomi 	TKA PILIHAN
	10.45-11.15	VII	Kimia	Antropologi 	OPSI	Ekonomi 	MAT TKA
	11.15-11.45	VIII	Kimia	Antropologi 	OPSI	Ekonomi 	MAT TKA
	11.45-13.00		ISHOMA
	13.00-13.30	IX	PKn	BIND	OPSI	PKn	TKA PILIHAN
	13.30.14.00	X	PKn	BIND	OPSI	PKn	TKA PILIHAN
RABU	07.15-07.45	I	Bahasa Arab	Tahfidz	Bahasa Inggris
	07.45-08.15	II	Bahasa Arab	Tahfidz	Bahasa Inggris
	08.15-09.45	III	BIND	PKn	Fisika	BIND	BING TKA
	08.45-09.15	IV	BIND	PKn	Fisika	BIND	BING TKA
	09.15-09.45	V	BIND*	Geografi	Fisika	Sejarah	TKA PILIHAN
	09.45.10.15		ISTIRAHAT/ SHALAT DHUHA
	10.15-10.45	VI	OPSI	Geografi	Kimia	Sejarah	TKA PILIHAN
	10.45-11.15	VII	OPSI	Ekonomi	Kimia	Geografi	MAT TKA
	11.15-11.45	VIII	OPSI	Ekonomi	Kimia	Geografi	MAT TKA
	11.45-13.00		ISHOMA
	13.00-13.30	IX	OPSI	Sejarah	PKn	Ekonomi	TKA PILIHAN
	13.30.14.00	X	OPSI	Sejarah	PKn	Ekonomi	TKA PILIHAN
KAMIS	07.15-07.45	I	Bahasa Inggris	Bahasa Arab	Tahfidz
	07.45-08.15	II	Bahasa Inggris	Bahasa Arab	Tahfidz
	08.15-09.45	III	Pembinaan Ekstrakurikuler Olahraga 
	08.45-09.15	IV	
	09.15-09.45	V	
	09.45.10.15	VI	
	10.15-10.45		ISTIRAHAT/ SHALAT DHUHA
	10.45-11.15	VII	Pembinaan Ekstrakurikuler Seni
	11.15-11.45	VIII	
	11.45-13.00		ISHOMA
	13.00-13.30	IX	Pembinaan Ekstrakurikuler Seni
	13.30.14.00	X	
JUM’AT	07.15-07.45	I	Pembinaan ORTOM (HIZBUL WATHAN, IPM, TAPAK SUCI)
	07.45-08.15	II	
	08.15-09.45	III	
	08.45-09.15	IV	
	09.15-09.45	V	
	09.45.10.15		ISTIRAHAT/ SHALAT DHUHA
	10.15-10.45	VI	Pembinaan ORTOM (HIZBUL WATHAN, IPM, TAPAK SUCI)
	10.45-11.15	VII	
	11.15-11.45	VIII	
	11.45-13.00		ISHOMA
	13.00-13.30	IX	Pembinaan ORTOM (HIZBUL WATHAN, IPM, TAPAK SUCI)
	13.30.14.00	X	
EOD;

        $lines = explode("\n", $rawData);
        $currentHari = '';

        // Class Mapping
        $kelasNames = ['Kelas X', 'Kelas XI', 'Kelas XII'];
        $kelasIds = [];
        foreach ($kelasNames as $nama) {
            $k = MasterKelas::firstOrCreate(
                ['nama' => $nama, 'tahun_ajaran_id' => $tahunAjaranId],
                ['tingkat' => explode(' ', $nama)[1] ?? 'X']
            );
            $kelasIds[] = $k->id;
        }

        // Cache to assign the same guru to the same subject in the same class consistently
        $guruSubjectCache = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, 'Hari')) {
                continue;
            }

            $parts = explode("\t", $line);
            $parts = array_map('trim', $parts);

            // Handle Hari
            if (!empty($parts[0]) && !preg_match('/^[0-9]/', $parts[0])) {
                $currentHari = ucfirst(strtolower($parts[0]));
                if ($currentHari === "Jum’at" || $currentHari === "Jumat") {
                    $currentHari = "Jumat";
                }
            }

            // Time usually in parts[1] or parts[0] if Hari is empty
            $timePart = '';
            $jpPart = '';
            $subjectStartIndex = 0;

            if (!empty($parts[0]) && preg_match('/^[0-9]/', $parts[0])) {
                $timePart = $parts[0];
                $jpPart = $parts[1] ?? '';
                $subjectStartIndex = 2;
            } else if (!empty($parts[1]) && preg_match('/^[0-9]/', $parts[1])) {
                $timePart = $parts[1];
                $jpPart = $parts[2] ?? '';
                $subjectStartIndex = 3;
            } else {
                continue; 
            }

            // Parse Time
            if (strpos($timePart, '-') === false && strlen($timePart) >= 11) {
                $timePart = substr($timePart, 0, 5) . '-' . substr($timePart, 6);
            }
            $timePart = str_replace('.', ':', $timePart);
            $timeParts = explode('-', $timePart);
            if (count($timeParts) < 2) continue;
            
            $jamMulai = trim($timeParts[0]) . ':00';
            $jamSelesai = trim($timeParts[1]) . ':00';

            // Clean Jam Ke
            $jamKe = null;
            if (preg_match('/^[IVX]+$/', $jpPart)) {
                $jamMap = ['I'=>1, 'II'=>2, 'III'=>3, 'IV'=>4, 'V'=>5, 'VI'=>6, 'VII'=>7, 'VIII'=>8, 'IX'=>9, 'X'=>10];
                $jamKe = $jamMap[$jpPart] ?? null;
            }

            $subjects = array_slice($parts, $subjectStartIndex);
            // Filter out purely empty strings to handle trailing/intermediate missing data gracefully
            $subjects = array_values(array_filter($subjects, fn($s) => $s !== ''));

            if (count($subjects) === 0) {
                continue; // Skip blank slots
            }

            // Check if global event
            $firstSubjectUpper = strtoupper($subjects[0]);
            $isGlobal = false;
            $kategoriKegiatan = 'pembelajaran';
            
            if (str_contains($firstSubjectUpper, 'UPACARA')) {
                $kategoriKegiatan = 'upacara';
                $isGlobal = true;
            } else if (str_contains($firstSubjectUpper, 'ISTIRAHAT') || str_contains($firstSubjectUpper, 'DHUHA')) {
                $kategoriKegiatan = 'istirahat';
                $isGlobal = true;
            } else if (str_contains($firstSubjectUpper, 'ISHOMA')) {
                $kategoriKegiatan = 'ishoma';
                $isGlobal = true;
            } else if (str_contains($firstSubjectUpper, 'EKSTRAKURIKULER')) {
                $kategoriKegiatan = 'ekstrakurikuler';
                $isGlobal = true;
            } else if (str_contains($firstSubjectUpper, 'PEMBINAAN')) {
                $kategoriKegiatan = 'pembinaan';
                $isGlobal = true;
            }

            if ($isGlobal) {
                MasterJadwalPelajaran::create([
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'kelas_id' => null, 
                    'kategori_kegiatan' => $kategoriKegiatan,
                    'keterangan_kegiatan' => count($subjects) > 0 ? $subjects[0] : null,
                    'hari' => $currentHari,
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'jam_ke' => null,
                ]);
                continue;
            }

            // Figure out class mapping based on number of subjects parsed
            $mapping = [];
            $numSubjects = count($subjects);
            
            if ($numSubjects == 5) {
                // Split classes for X and XI, full for XII
                $mapping = [0 => $kelasIds[0], 1 => $kelasIds[0], 2 => $kelasIds[1], 3 => $kelasIds[1], 4 => $kelasIds[2]];
            } else if ($numSubjects == 4) {
                // Split classes for X and XI, missing XII
                $mapping = [0 => $kelasIds[0], 1 => $kelasIds[0], 2 => $kelasIds[1], 3 => $kelasIds[1]];
            } else if ($numSubjects == 3) {
                // One subject per class
                $mapping = [0 => $kelasIds[0], 1 => $kelasIds[1], 2 => $kelasIds[2]];
            } else if ($numSubjects == 2) {
                // One subject per class, missing XII
                $mapping = [0 => $kelasIds[0], 1 => $kelasIds[1]];
            } else {
                // Fallback (just map to available sequentially)
                foreach ($subjects as $idx => $s) {
                    $mapping[$idx] = $kelasIds[$idx] ?? $kelasIds[0];
                }
            }

            foreach ($subjects as $idx => $subjectName) {
                $subjectName = str_replace('*', '', $subjectName);
                if (empty($subjectName)) continue;

                $kelasId = $mapping[$idx] ?? $kelasIds[0];

                // Ensure Mapel exists
                $mapel = MasterMapel::firstOrCreate(
                    ['nama' => $subjectName],
                    ['kode' => Str::upper(Str::slug($subjectName))]
                );

                // Ensure consistent Guru per Mapel per Kelas
                $cacheKey = $mapel->id . '_' . $kelasId;
                if (!isset($guruSubjectCache[$cacheKey])) {
                    $guruSubjectCache[$cacheKey] = !empty($gurus) ? $gurus[array_rand($gurus)] : null;
                }
                
                $guruId = $guruSubjectCache[$cacheKey];

                MasterJadwalPelajaran::create([
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'kelas_id' => $kelasId,
                    'mapel_id' => $mapel->id,
                    'guru_id' => $guruId,
                    'kategori_kegiatan' => 'pembelajaran',
                    'hari' => $currentHari,
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'jam_ke' => $jamKe,
                ]);
            }
        }
    }
}
