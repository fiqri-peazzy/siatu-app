<?php

namespace App\Livewire\Master\Siswa;

use App\Models\Master\MasterSiswa;
use App\Models\Master\MasterKelas;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Layout('components.layouts.app')]
#[Title('Edit Data Siswa')]
class EditSiswa extends Component
{
    public MasterSiswa $siswa;
    public array $state = [];

    public function mount(MasterSiswa $siswa)
    {
        if (!auth()->user()->hasAnyRole(['super-admin', 'admin-tu', 'admin'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $this->siswa = $siswa;
        $this->state = $siswa->toArray();
    }

    #[Computed]
    public function kelas()
    {
        return MasterKelas::with('tahunAjaran')
            ->whereHas('tahunAjaran', function($q) {
                $q->where('is_active', true);
            })
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();
    }

    public function save()
    {
        $this->validate([
            'state.nama' => 'required|string|max:255',
            'state.nis' => 'nullable|string|max:255|unique:master_siswas,nis,' . $this->siswa->id,
            'state.nisn' => 'nullable|string|max:255|unique:master_siswas,nisn,' . $this->siswa->id,
            'state.kelas_id' => 'nullable|exists:master_kelas,id',
            'state.status' => 'required|in:aktif,lulus,pindah,keluar',
        ]);

        $data = $this->state;
        $data['nis'] = !empty($data['nis']) ? $data['nis'] : null;
        $data['nisn'] = !empty($data['nisn']) ? $data['nisn'] : null;
        $data['kelas_id'] = !empty($data['kelas_id']) ? $data['kelas_id'] : null;

        $this->siswa->update($data);

        session()->flash('success', 'Data siswa berhasil diperbarui.');
        return $this->redirect('/master/siswa', navigate: true);
    }

    public function render()
    {
        return view('livewire.master.siswa.edit-siswa');
    }
}
