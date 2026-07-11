<?php

namespace App\Livewire\Administrasi;

use App\Models\Pengaturan;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class PengaturanLokasi extends Component
{
    public $lat_sekolah;
    public $lng_sekolah;
    public $radius_absen;
    
    public function mount()
    {
        $this->lat_sekolah = Pengaturan::where('key', 'lat_sekolah')->value('value');
        $this->lng_sekolah = Pengaturan::where('key', 'lng_sekolah')->value('value');
        $this->radius_absen = Pengaturan::where('key', 'radius_absen')->value('value') ?? '50';
    }
    
    public function save()
    {
        $this->validate([
            'lat_sekolah' => 'required|numeric',
            'lng_sekolah' => 'required|numeric',
            'radius_absen' => 'required|numeric|min:10',
        ]);
        
        Pengaturan::updateOrCreate(['key' => 'lat_sekolah'], ['value' => $this->lat_sekolah, 'description' => 'Garis Lintang Sekolah (Latitude)']);
        Pengaturan::updateOrCreate(['key' => 'lng_sekolah'], ['value' => $this->lng_sekolah, 'description' => 'Garis Bujur Sekolah (Longitude)']);
        Pengaturan::updateOrCreate(['key' => 'radius_absen'], ['value' => $this->radius_absen, 'description' => 'Maksimal Radius Absen (Meter)']);
        
        $this->dispatch('swal:success', 'Pengaturan lokasi berhasil disimpan!');
    }
    
    public function render()
    {
        return view('livewire.administrasi.pengaturan-lokasi');
    }
}
