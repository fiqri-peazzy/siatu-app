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
            // Seed a dummy teacher
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

        // Class Mapping. Strictly use the 3 main classes.
        $kelasNames = ['Kelas X', 'Kelas XI', 'Kelas XII'];
        $kelasIds = [];
        foreach ($kelasNames as $nama) {
            $k = MasterKelas::firstOrCreate(
                ['nama' => $nama, 'tahun_ajaran_id' => $tahunAjaranId],
                ['tingkat' => explode(' ', $nama)[1] ?? 'X']
            );
            $kelasIds[] = $k->id;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, 'Hari')) {
                continue;
            }

            // Split by tab
            $parts = explode("\t", $line);
            
            // Clean up parts
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
                // e.g. 07.45-08.15
                $timePart = $parts[0];
                $jpPart = $parts[1] ?? '';
                $subjectStartIndex = 2;
            } else if (!empty($parts[1]) && preg_match('/^[0-9]/', $parts[1])) {
                // e.g. SENIN 07.00-07.45
                $timePart = $parts[1];
                $jpPart = $parts[2] ?? '';
                $subjectStartIndex = 3;
            } else {
                continue; // Can't parse time
            }

            // Parse Time (e.g. 07.00-07.45 or 09.45.10.15)
            // Fix weird formats like 09.45.10.15 to 09.45-10.15
            if (strpos($timePart, '-') === false && strlen($timePart) >= 11) {
                $timePart = substr($timePart, 0, 5) . '-' . substr($timePart, 6);
            }
            $timePart = str_replace('.', ':', $timePart);
            $timeParts = explode('-', $timePart);
            if (count($timeParts) < 2) {
                // try replacing last : with -
                continue;
            }
            $jamMulai = trim($timeParts[0]) . ':00';
            $jamSelesai = trim($timeParts[1]) . ':00';

            // Clean Jam Ke
            $jamKe = null;
            if (preg_match('/^[IVX]+$/', $jpPart)) {
                $jamMap = ['I'=>1, 'II'=>2, 'III'=>3, 'IV'=>4, 'V'=>5, 'VI'=>6, 'VII'=>7, 'VIII'=>8, 'IX'=>9, 'X'=>10];
                $jamKe = $jamMap[$jpPart] ?? null;
            }

            $kategoriKegiatan = 'pembelajaran';
            $keterangan = '';
            $isGlobal = false;
            if (isset($parts[$subjectStartIndex])) {
                $checkStr = strtoupper($parts[$subjectStartIndex]);
                if (str_contains($checkStr, 'UPACARA')) {
                    $kategoriKegiatan = 'upacara';
                    $isGlobal = true;
                } else if (str_contains($checkStr, 'ISTIRAHAT') || str_contains($checkStr, 'DHUHA')) {
                    $kategoriKegiatan = 'istirahat';
                    $isGlobal = true;
                } else if (str_contains($checkStr, 'ISHOMA')) {
                    $kategoriKegiatan = 'ishoma';
                    $isGlobal = true;
                } else if (str_contains($checkStr, 'EKSTRAKURIKULER')) {
                    $kategoriKegiatan = 'ekstrakurikuler';
                    $isGlobal = true;
                    $keterangan = $parts[$subjectStartIndex];
                } else if (str_contains($checkStr, 'PEMBINAAN')) {
                    $kategoriKegiatan = 'pembinaan';
                    $isGlobal = true;
                    $keterangan = $parts[$subjectStartIndex];
                }
            }

            if ($isGlobal) {
                MasterJadwalPelajaran::create([
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'kelas_id' => null, // Global
                    'kategori_kegiatan' => $kategoriKegiatan,
                    'keterangan_kegiatan' => $keterangan ?: null,
                    'hari' => $currentHari,
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'jam_ke' => null,
                ]);
            } else {
                // Subjects
                $colIndex = 0;
                for ($i = $subjectStartIndex; $i < count($parts); $i++) {
                    if ($colIndex >= count($kelasIds)) {
                        break; // Ignore extra subjects beyond the 3 main classes
                    }
                    $subjectName = trim($parts[$i]);
                    if (!empty($subjectName)) {
                        $subjectName = str_replace('*', '', $subjectName);
                        // Ensure Mapel exists
                        $mapel = MasterMapel::firstOrCreate(
                            ['nama' => $subjectName],
                            ['kode' => Str::upper(Str::slug($subjectName))]
                        );

                        // Random guru
                        $randomGuruId = !empty($gurus) ? $gurus[array_rand($gurus)] : null;
                        
                        // Map to class
                        $kelasId = $kelasIds[$colIndex] ?? $kelasIds[0];

                        MasterJadwalPelajaran::create([
                            'tahun_ajaran_id' => $tahunAjaranId,
                            'kelas_id' => $kelasId,
                            'mapel_id' => $mapel->id,
                            'guru_id' => $randomGuruId,
                            'kategori_kegiatan' => 'pembelajaran',
                            'hari' => $currentHari,
                            'jam_mulai' => $jamMulai,
                            'jam_selesai' => $jamSelesai,
                            'jam_ke' => $jamKe,
                        ]);
                    }
                    $colIndex++;
                }
            }
        }
    }
}
