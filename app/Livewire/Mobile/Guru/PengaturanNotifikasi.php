<?php

namespace App\Livewire\Mobile\Guru;

use App\Models\PreferensiNotifikasiGuru;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PengaturanNotifikasi extends Component
{
    public $preferensi;
    public $reminder_jurnal_aktif;
    public $kanal_notifikasi;

    public function mount()
    {
        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;
        if (!$guruId) abort(403);

        $this->preferensi = PreferensiNotifikasiGuru::firstOrCreate(
            ['guru_id' => $guruId],
            ['reminder_jurnal_aktif' => true, 'kanal_notifikasi' => 'wa_saja']
        );

        $this->reminder_jurnal_aktif = $this->preferensi->reminder_jurnal_aktif;
        $this->kanal_notifikasi = $this->preferensi->kanal_notifikasi;
    }

    public function simpan()
    {
        $this->preferensi->update([
            'reminder_jurnal_aktif' => $this->reminder_jurnal_aktif,
            'kanal_notifikasi' => $this->kanal_notifikasi,
        ]);

        $this->dispatch('swal:success', 'Pengaturan notifikasi berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.mobile.guru.pengaturan-notifikasi')->layout('components.layouts.pwa', ['judul' => 'Pengingat']);
    }
}
