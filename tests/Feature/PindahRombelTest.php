<?php

use App\Livewire\Master\Siswa\PindahRombel;
use App\Models\AbsensiSiswa;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterSiswa;
use App\Models\Master\MasterTahunAjaran;
use App\Models\User;
use App\Services\RombelService;
use Database\Seeders\MasterGurusTableSeeder;
use Database\Seeders\MasterKelasTableSeeder;
use Database\Seeders\MasterSiswasTableSeeder;
use Database\Seeders\MasterTahunAjaransTableSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

uses(DatabaseMigrations::class);

beforeEach(function () {
    Schema::disableForeignKeyConstraints();
    $this->seed([RolesAndPermissionsSeeder::class, MasterTahunAjaransTableSeeder::class, MasterGurusTableSeeder::class, MasterKelasTableSeeder::class, MasterSiswasTableSeeder::class]);
    Schema::enableForeignKeyConstraints();

    $ta = MasterTahunAjaran::where('is_active', true)->firstOrFail();
    $this->asal = MasterKelas::where('tahun_ajaran_id', $ta->id)->whereHas('siswa', fn ($q) => $q->where('status', 'aktif'))->firstOrFail();
    $this->tujuan = MasterKelas::create(['kode' => 'X-UJI', 'nama' => 'X Uji', 'tingkat' => 'X', 'jurusan' => 'IPS', 'tahun_ajaran_id' => $ta->id]);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin-tu');
});

it('memindahkan siswa terpilih & riwayat absensi tetap di rombel lama', function () {
    $siswa = MasterSiswa::where('kelas_id', $this->asal->id)->where('status', 'aktif')->orderBy('nama')->take(3)->get();
    AbsensiSiswa::create(['siswa_id' => $siswa[0]->id, 'kelas_id' => $this->asal->id, 'tanggal' => '2026-11-02', 'jam_ke' => 1, 'status' => 'hadir', 'dicatat_oleh' => $this->admin->id]);
    $sisaAwal = MasterSiswa::where('kelas_id', $this->asal->id)->where('status', 'aktif')->count();

    Livewire::actingAs($this->admin)->test(PindahRombel::class, ['asal' => (string) $this->asal->id])
        ->set('tujuan', (string) $this->tujuan->id)
        ->set('dipilih', $siswa->pluck('id')->map(fn ($i) => (string) $i)->all())
        ->call('pindahkan')
        ->assertHasNoErrors()
        ->assertSet('dipilih', [])
        ->assertDispatched('swal:success');

    expect(MasterSiswa::where('kelas_id', $this->tujuan->id)->count())->toBe(3)
        ->and(MasterSiswa::where('kelas_id', $this->asal->id)->where('status', 'aktif')->count())->toBe($sisaAwal - 3)
        ->and(AbsensiSiswa::first()->kelas_id)->toBe($this->asal->id);
});

it('hanya siswa dari rombel asal yang dipindah (id titipan diabaikan)', function () {
    $lain = MasterSiswa::where('kelas_id', '!=', $this->asal->id)->where('status', 'aktif')->first();
    $milik = MasterSiswa::where('kelas_id', $this->asal->id)->where('status', 'aktif')->first();
    if (! $lain) {
        $lain = MasterSiswa::create(['nama' => 'Siswa Lain', 'nis' => '999001', 'status' => 'aktif', 'kelas_id' => null]);
    }
    $kelasLain = $lain->kelas_id;

    Livewire::actingAs($this->admin)->test(PindahRombel::class, ['asal' => (string) $this->asal->id])
        ->set('tujuan', (string) $this->tujuan->id)
        ->set('dipilih', [(string) $milik->id, (string) $lain->id])
        ->call('pindahkan');

    expect($milik->fresh()->kelas_id)->toBe($this->tujuan->id)
        ->and($lain->fresh()->kelas_id)->toBe($kelasLain);
});

it('validasi: tujuan wajib, beda dari asal, minimal satu siswa', function () {
    $t = Livewire::actingAs($this->admin)->test(PindahRombel::class, ['asal' => (string) $this->asal->id]);
    $t->call('pindahkan')->assertHasErrors(['tujuan', 'dipilih']);
    $t->set('tujuan', (string) $this->asal->id)->set('dipilih', ['1'])->call('pindahkan')->assertHasErrors(['tujuan' => 'different']);
});

it('service menolak rombel tujuan di luar tahun ajaran aktif', function () {
    $lama = MasterTahunAjaran::create(['nama' => '2020/2021', 'semester' => 'Ganjil', 'is_active' => false, 'mulai' => '2020-07-01', 'selesai' => '2020-12-31']);
    $kelasLama = MasterKelas::create(['kode' => 'X-LAMA', 'nama' => 'X Lama', 'tingkat' => 'X', 'jurusan' => 'IPA', 'tahun_ajaran_id' => $lama->id]);
    $s = MasterSiswa::where('status', 'aktif')->first();

    expect(fn () => app(RombelService::class)->pindahkan([$s->id], $kelasLama->id))->toThrow(ValidationException::class);
});

it('guru tidak bisa membuka halaman pindah rombel', function () {
    $guru = User::factory()->create();
    $guru->assignRole('guru');
    $this->actingAs($guru)->get(route('master.siswa.pindah-rombel'))->assertForbidden();
    $this->actingAs($this->admin)->get(route('master.siswa.pindah-rombel'))->assertOk()->assertSee('Pindah rombel massal');
});
