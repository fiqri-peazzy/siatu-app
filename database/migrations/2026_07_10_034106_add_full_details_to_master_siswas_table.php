<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_siswas', function (Blueprint $table) {
            $table->string('jk')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nik')->nullable();
            $table->string('agama')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('dusun')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('jenis_tinggal')->nullable();
            $table->string('alat_transportasi')->nullable();
            $table->string('telepon')->nullable();
            $table->string('hp')->nullable();
            $table->string('email')->nullable();
            $table->string('skhun')->nullable();
            $table->string('penerima_kps')->nullable();
            $table->string('no_kps')->nullable();
            
            // Data Ayah
            $table->string('ayah_nama')->nullable();
            $table->string('ayah_tahun_lahir')->nullable();
            $table->string('ayah_pendidikan')->nullable();
            $table->string('ayah_pekerjaan')->nullable();
            $table->string('ayah_penghasilan')->nullable();
            $table->string('ayah_nik')->nullable();
            
            // Data Ibu
            $table->string('ibu_nama')->nullable();
            $table->string('ibu_tahun_lahir')->nullable();
            $table->string('ibu_pendidikan')->nullable();
            $table->string('ibu_pekerjaan')->nullable();
            $table->string('ibu_penghasilan')->nullable();
            $table->string('ibu_nik')->nullable();
            
            // Data Wali
            $table->string('wali_nama')->nullable();
            $table->string('wali_tahun_lahir')->nullable();
            $table->string('wali_pendidikan')->nullable();
            $table->string('wali_pekerjaan')->nullable();
            $table->string('wali_penghasilan')->nullable();
            $table->string('wali_nik')->nullable();
            
            // Data Tambahan
            $table->string('no_peserta_un')->nullable();
            $table->string('no_seri_ijazah')->nullable();
            $table->string('penerima_kip')->nullable();
            $table->string('nomor_kip')->nullable();
            $table->string('nama_di_kip')->nullable();
            $table->string('nomor_kks')->nullable();
            $table->string('no_akta_lahir')->nullable();
            $table->string('bank')->nullable();
            $table->string('no_rekening_bank')->nullable();
            $table->string('rekening_atas_nama')->nullable();
            $table->string('layak_pip')->nullable();
            $table->string('alasan_layak_pip')->nullable();
            $table->string('kebutuhan_khusus')->nullable();
            $table->string('sekolah_asal')->nullable();
            $table->string('anak_ke')->nullable();
            $table->string('lintang')->nullable();
            $table->string('bujur')->nullable();
            $table->string('no_kk')->nullable();
            $table->string('berat_badan')->nullable();
            $table->string('tinggi_badan')->nullable();
            $table->string('lingkar_kepala')->nullable();
            $table->string('jml_saudara_kandung')->nullable();
            $table->string('jarak_rumah_ke_sekolah')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('master_siswas', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
