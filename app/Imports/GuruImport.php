<?php

namespace App\Imports;

use App\Models\Master\MasterGuru;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

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

            // Determine matching criteria
            $match = [];
            if (!empty($row[2])) {
                $match['nuptk'] = $row[2];
            } elseif (!empty($row[6])) {
                $match['nip'] = $row[6];
            } else {
                $match['nama'] = $row[1];
            }

            MasterGuru::updateOrCreate(
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
        }
    }
}
