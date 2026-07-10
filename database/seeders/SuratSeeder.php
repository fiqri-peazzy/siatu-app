<?php

namespace Database\Seeders;

use App\Models\Master\MasterKategoriSurat;
use App\Models\Master\MasterKlasifikasiArsip;
use App\Models\SuratMasuk;
use App\Models\Disposisi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan ada Kategori Surat
        $kategori = MasterKategoriSurat::firstOrCreate(
            ['kode' => 'UND'],
            ['nama' => 'Undangan', 'format_nomor' => 'UND/{ROMAWI_BULAN}/{TAHUN}', 'counter' => 0, 'tipe' => 'keduanya']
        );

        // 2. Pastikan ada Klasifikasi Arsip
        $klasifikasi = MasterKlasifikasiArsip::firstOrCreate(
            ['kode' => '000'],
            ['nama' => 'Umum']
        );

        // 3. Ambil/buat users untuk disposisi
        $admin = User::firstOrCreate(['email' => 'admin.tu@contoh.com'], [
            'name' => 'Admin TU', 'password' => bcrypt('password')
        ]);
        $kepsek = User::firstOrCreate(['email' => 'kepsek@contoh.com'], [
            'name' => 'Kepala Sekolah', 'password' => bcrypt('password')
        ]);
        $guru1 = User::firstOrCreate(['email' => 'guru1@contoh.com'], [
            'name' => 'Guru Wakasek', 'password' => bcrypt('password')
        ]);

        // 4. Buat Surat Masuk dummy
        $suratMasuk = SuratMasuk::create([
            'no_agenda' => 'UND/VII/2026',
            'kategori_surat_id' => $kategori->id,
            'klasifikasi_arsip_id' => $klasifikasi->id,
            'tanggal_terima' => now()->toDateString(),
            'tanggal_surat' => now()->subDays(2)->toDateString(),
            'asal_instansi' => 'Dinas Pendidikan',
            'perihal' => 'Undangan Rapat Koordinasi',
            'sifat' => \App\Enums\SifatSurat::PENTING,
            'status' => \App\Enums\StatusSuratMasuk::DIDISPOSISI,
            'input_oleh' => $admin->id,
        ]);

        // 5. Buat rantai disposisi 3 level: Admin -> Kepsek -> Guru Wakasek
        // Level 1
        $disposisi1 = Disposisi::create([
            'surat_masuk_id' => $suratMasuk->id,
            'parent_disposisi_id' => null,
            'level' => 1,
            'dari_user_id' => $admin->id,
            'ke_user_id' => $kepsek->id,
            'catatan' => 'Mohon arahan Bapak Kepsek',
            'status' => \App\Enums\StatusDisposisi::DITERUSKAN,
        ]);

        // Level 2
        $disposisi2 = Disposisi::create([
            'surat_masuk_id' => $suratMasuk->id,
            'parent_disposisi_id' => $disposisi1->id,
            'level' => 2,
            'dari_user_id' => $kepsek->id,
            'ke_user_id' => $guru1->id,
            'catatan' => 'Tolong wakili saya hadir di rapat ini',
            'status' => \App\Enums\StatusDisposisi::DITERIMA,
        ]);
    }
}
