<?php

namespace App\Imports;

use App\Models\Master\MasterGuru;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuruImport implements ToCollection, WithStartRow
{
    public function startRow(): int
    {
        return 6; // Index 5 + 1
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Check if Nama is empty, skip
            if (empty($row[1])) {
                continue;
            }

            // Parse Date
            $tanggalLahir = null;
            if (!empty($row[5])) {
                try {
                    if (is_numeric($row[5])) {
                        $tanggalLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5])->format('Y-m-d');
                    } else {
                        $tanggalLahir = Carbon::parse($row[5])->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $tanggalLahir = null;
                }
            }

            // Map Status Kepegawaian
            $statusPegawaiText = strtolower(trim($row[7] ?? ''));
            $statusPegawai = \App\Enums\StatusKepegawaianGuru::LAINNYA->value;
            if (str_contains($statusPegawaiText, 'pns')) {
                $statusPegawai = \App\Enums\StatusKepegawaianGuru::PNS->value;
            } elseif (str_contains($statusPegawaiText, 'gty') || str_contains($statusPegawaiText, 'pty')) {
                $statusPegawai = \App\Enums\StatusKepegawaianGuru::GTY->value;
            } elseif (str_contains($statusPegawaiText, 'honor')) {
                $statusPegawai = \App\Enums\StatusKepegawaianGuru::HONORER->value;
            }

            // Compile Alamat
            $alamatParts = [];
            if (!empty($row[10])) $alamatParts[] = trim($row[10]); // Jalan
            if (!empty($row[11]) || !empty($row[12])) $alamatParts[] = "RT " . trim($row[11] ?? '-') . " / RW " . trim($row[12] ?? '-');
            if (!empty($row[13])) $alamatParts[] = "Dusun " . trim($row[13]);
            if (!empty($row[14])) $alamatParts[] = "Kel/Desa " . trim($row[14]);
            if (!empty($row[15])) $alamatParts[] = trim($row[15]);
            $alamatLengkap = !empty($alamatParts) ? implode(', ', $alamatParts) : null;

            // Determine matching criteria (prioritize unique fields: NIP, then NUPTK, then Nama)
            $match = [];
            if (!empty($row[6])) {
                $match['nip'] = $row[6];
            } elseif (!empty($row[2])) {
                $match['nuptk'] = $row[2];
            } else {
                $match['nama'] = $row[1];
            }

            $guru = MasterGuru::updateOrCreate(
                $match,
                [
                    'nama' => $row[1],
                    'nuptk' => $row[2],
                    'jenis_kelamin' => (strtoupper(trim($row[3] ?? '')) === 'P') ? 'P' : 'L',
                    'tempat_lahir' => $row[4],
                    'tanggal_lahir' => $tanggalLahir,
                    'nip' => $row[6],
                    'status_kepegawaian' => $statusPegawai,
                    'alamat' => $alamatLengkap,
                    'no_hp' => $row[18],
                    'email' => $row[19] ?? null,
                ]
            );

            if (!$guru->user_id) {
                $email = $guru->email;
                if (empty($email)) {
                    if (!empty($guru->nip)) {
                        $email = $guru->nip . '@situ-sma.test';
                    } elseif (!empty($guru->nuptk)) {
                        $email = $guru->nuptk . '@situ-sma.test';
                    } else {
                        $email = Str::slug($guru->nama) . '_' . time() . rand(10, 99) . '@situ-sma.test';
                    }
                }
                
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $guru->nama,
                        'password' => Hash::make('password'),
                        'is_active' => true,
                        'nip' => $guru->nip,
                        'jenis_kelamin' => $guru->jenis_kelamin,
                        'no_hp' => $guru->no_hp,
                    ]
                );
                
                // assignRole is provided by Spatie Permission
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('guru');
                }

                $guru->update(['user_id' => $user->id]);
                
                // Link the user back to the guru if needed (since user has guru_id in UsersSeeder)
                // Assuming User model has guru_id based on UsersSeeder.php: $guruUser->update(['guru_id' => $guruMaster->id]);
                $user->update(['guru_id' => $guru->id]);
            }
        }
    }
}
