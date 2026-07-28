<?php

namespace App\Livewire\Mobile\Guru;

use App\Models\Master\MasterGuru;
use App\Models\Master\MasterJadwalPelajaran;
use App\Models\JurnalGuru;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ProfilGuru extends Component
{
    public $guru;
    public $persentase = 0;
    
    public $no_hp;
    public $alamat;
    
    public $password_lama;
    public $password_baru;
    public $password_baru_confirmation;

    public function mount()
    {
        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;
        if (!$guruId) abort(403);

        $this->guru = MasterGuru::find($guruId);
        $this->no_hp = $this->guru->no_hp;
        $this->alamat = $this->guru->alamat;

        $this->hitungPersentase();
    }

    public function hitungPersentase()
    {
        $bulanIni = Carbon::now()->format('m');
        $tahunIni = Carbon::now()->format('Y');
        
        $totalJadwalSebulan = MasterJadwalPelajaran::where('guru_id', $this->guru->id)->count() * 4; // Approx 4 weeks
        
        $totalTerisi = JurnalGuru::where('guru_id', $this->guru->id)
            ->where('status', 'diisi')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->count();

        if ($totalJadwalSebulan > 0) {
            $this->persentase = round(($totalTerisi / $totalJadwalSebulan) * 100);
            if ($this->persentase > 100) $this->persentase = 100;
        }
    }

    public function updateProfil()
    {
        $this->validate([
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
        ]);

        $this->guru->update([
            'no_hp' => $this->no_hp,
            'alamat' => $this->alamat,
        ]);

        $this->dispatch('swal:success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword()
    {
        $this->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($this->password_lama, Auth::user()->password)) {
            $this->addError('password_lama', 'Password lama tidak sesuai.');
            return;
        }

        Auth::user()->update([
            'password' => Hash::make($this->password_baru)
        ]);

        $this->reset(['password_lama', 'password_baru', 'password_baru_confirmation']);
        $this->dispatch('swal:success', 'Password berhasil diubah!');
    }

    public function render()
    {
        return view('livewire.mobile.guru.profil-guru')->layout('components.layouts.pwa');
    }
}
