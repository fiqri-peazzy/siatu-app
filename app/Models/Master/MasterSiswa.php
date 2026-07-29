<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterSiswa extends Model
{
    protected $fillable = [
        'nis', 'nisn', 'nama', 'kelas_id', 'status',
        'jk', 'tempat_lahir', 'tanggal_lahir', 'nik', 'agama', 'alamat', 'rt', 'rw', 'dusun',
        'kelurahan', 'kecamatan', 'kode_pos', 'jenis_tinggal', 'alat_transportasi', 'telepon',
        'hp', 'email', 'skhun', 'penerima_kps', 'no_kps',
        'ayah_nama', 'ayah_tahun_lahir', 'ayah_pendidikan', 'ayah_pekerjaan', 'ayah_penghasilan', 'ayah_nik',
        'ibu_nama', 'ibu_tahun_lahir', 'ibu_pendidikan', 'ibu_pekerjaan', 'ibu_penghasilan', 'ibu_nik',
        'wali_nama', 'wali_tahun_lahir', 'wali_pendidikan', 'wali_pekerjaan', 'wali_penghasilan', 'wali_nik',
        'no_peserta_un', 'no_seri_ijazah', 'penerima_kip', 'nomor_kip', 'nama_di_kip', 'nomor_kks',
        'no_akta_lahir', 'bank', 'no_rekening_bank', 'rekening_atas_nama', 'layak_pip', 'alasan_layak_pip',
        'kebutuhan_khusus', 'sekolah_asal', 'anak_ke', 'lintang', 'bujur', 'no_kk', 'berat_badan',
        'tinggi_badan', 'lingkar_kepala', 'jml_saudara_kandung', 'jarak_rumah_ke_sekolah'
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }

    public function user()
    {
        return $this->hasOne(\App\Models\User::class, 'siswa_id');
    }

    protected static function booted()
    {
        static::deleting(function ($siswa) {
            if ($siswa->user) {
                $siswa->user()->delete();
            }
        });
    }

    public function peminatanMapels()
    {
        return $this->belongsToMany(MasterMapel::class, 'peminatan_siswas', 'siswa_id', 'mapel_id')
                    ->withPivot('tahun_ajaran_id')
                    ->withTimestamps();
    }
}
