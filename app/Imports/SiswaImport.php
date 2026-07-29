<?php

namespace App\Imports;

use App\Models\Master\MasterSiswa;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterTahunAjaran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class SiswaImport implements ToCollection, WithStartRow
{
    public function startRow(): int
    {
        return 7;
    }

    public function collection(Collection $rows)
    {
        $activeTahunAjaran = MasterTahunAjaran::where('is_active', true)->first();
        $tahunAjaranId = $activeTahunAjaran ? $activeTahunAjaran->id : null;

        foreach ($rows as $row) {
            // Check if Nama is empty, skip
            if (empty($row[1])) {
                continue;
            }

            // Smart Match Kelas based on Rombel (index 42) vs MasterKelas
            $kelasId = null;
            if (!empty($row[42]) && $tahunAjaranId) {
                $rombelExcel = trim($row[42]);
                
                // Cari berdasarkan tingkat atau nama yang sama persis
                $kelas = \App\Models\Master\MasterKelas::where('tahun_ajaran_id', $tahunAjaranId)
                    ->where(function ($query) use ($rombelExcel) {
                        $query->where('tingkat', $rombelExcel)
                              ->orWhere('nama', $rombelExcel)
                              ->orWhere('nama', 'like', '%' . $rombelExcel . '%');
                    })
                    ->first();
                
                if ($kelas) {
                    $kelasId = $kelas->id;
                } else {
                    // Jika tidak ketemu sama sekali, buat baru agar data tidak hilang
                    $kelas = \App\Models\Master\MasterKelas::create([
                        'nama' => 'Kelas ' . $rombelExcel,
                        'tingkat' => $rombelExcel,
                        'jurusan' => '-',
                        'tahun_ajaran_id' => $tahunAjaranId
                    ]);
                    $kelasId = $kelas->id;
                }
            }

            // Parse Date
            $tanggalLahir = null;
            if (!empty($row[6])) {
                try {
                    // Excel dates might be numeric or strings like YYYY-MM-DD
                    if (is_numeric($row[6])) {
                        $tanggalLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[6])->format('Y-m-d');
                    } else {
                        $tanggalLahir = Carbon::parse($row[6])->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $tanggalLahir = null;
                }
            }

            // We update based on NISN (4) or NIS (2), or Nama if both are null
            $match = [];
            if (!empty($row[4])) {
                $match['nisn'] = $row[4];
            } elseif (!empty($row[2])) {
                $match['nis'] = $row[2];
            } else {
                $match['nama'] = $row[1];
            }

            MasterSiswa::updateOrCreate(
                $match,
                [
                    'nama' => $row[1],
                    'nis' => $row[2],
                    'jk' => $row[3],
                    'nisn' => $row[4],
                    'tempat_lahir' => $row[5],
                    'tanggal_lahir' => $tanggalLahir,
                    'nik' => $row[7],
                    'agama' => $row[8],
                    'alamat' => $row[9],
                    'rt' => $row[10],
                    'rw' => $row[11],
                    'dusun' => $row[12],
                    'kelurahan' => $row[13],
                    'kecamatan' => $row[14],
                    'kode_pos' => $row[15],
                    'jenis_tinggal' => $row[16],
                    'alat_transportasi' => $row[17],
                    'telepon' => $row[18],
                    'hp' => $row[19],
                    'email' => $row[20],
                    'skhun' => $row[21],
                    'penerima_kps' => $row[22],
                    'no_kps' => $row[23],
                    
                    // Ayah
                    'ayah_nama' => $row[24],
                    'ayah_tahun_lahir' => $row[25],
                    'ayah_pendidikan' => $row[26],
                    'ayah_pekerjaan' => $row[27],
                    'ayah_penghasilan' => $row[28],
                    'ayah_nik' => $row[29],
                    
                    // Ibu
                    'ibu_nama' => $row[30],
                    'ibu_tahun_lahir' => $row[31],
                    'ibu_pendidikan' => $row[32],
                    'ibu_pekerjaan' => $row[33],
                    'ibu_penghasilan' => $row[34],
                    'ibu_nik' => $row[35],
                    
                    // Wali
                    'wali_nama' => $row[36],
                    'wali_tahun_lahir' => $row[37],
                    'wali_pendidikan' => $row[38],
                    'wali_pekerjaan' => $row[39],
                    'wali_penghasilan' => $row[40],
                    'wali_nik' => $row[41],
                    
                    'kelas_id' => $kelasId,
                    'no_peserta_un' => $row[43],
                    'no_seri_ijazah' => $row[44],
                    'penerima_kip' => $row[45],
                    'nomor_kip' => $row[46],
                    'nama_di_kip' => $row[47],
                    'nomor_kks' => $row[48],
                    'no_akta_lahir' => $row[49],
                    'bank' => $row[50],
                    'no_rekening_bank' => $row[51],
                    'rekening_atas_nama' => $row[52],
                    'layak_pip' => $row[53],
                    'alasan_layak_pip' => $row[54],
                    'kebutuhan_khusus' => $row[55],
                    'sekolah_asal' => $row[56],
                    'anak_ke' => $row[57],
                    'lintang' => $row[58],
                    'bujur' => $row[59],
                    'no_kk' => $row[60],
                    'berat_badan' => $row[61],
                    'tinggi_badan' => $row[62],
                    'lingkar_kepala' => $row[63],
                    'jml_saudara_kandung' => $row[64],
                    'jarak_rumah_ke_sekolah' => $row[65],
                ]
            );
        }
    }
}
