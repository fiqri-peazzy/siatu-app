<?php

namespace App\Livewire\Administrasi\SuratKeluar;

use App\Enums\StatusSuratKeluar;
use App\Models\SuratKeluar;
use App\Services\SuratService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class DetailSuratKeluar extends Component
{
    use AuthorizesRequests;

    public SuratKeluar $surat;
    public bool $isRejectModalOpen = false;
    public string $catatan_revisi = '';

    public function mount(SuratKeluar $surat)
    {
        $this->authorize('view', $surat);
        $this->surat = $surat->load(['kategori', 'dibuatOleh', 'disetujuiOleh', 'balasanDari']);
    }

    public function ajukanApproval()
    {
        $this->authorize('update', $this->surat);
        
        $this->surat->update([
            'status' => StatusSuratKeluar::MENUNGGU_APPROVAL
        ]);
        
        // Notify Kepsek
        $kepsek = \App\Models\User::role('kepala-sekolah')->first();
        if ($kepsek && $kepsek->no_hp) {
            $pesan = "Halo {$kepsek->name}, ada draf surat keluar baru yang menunggu approval Anda.\nPerihal: {$this->surat->perihal}\nSilakan cek di sistem SITU-SMA.";
            \App\Jobs\SendWhatsAppNotification::dispatch($kepsek->no_hp, $pesan);
        }

        $this->dispatch('swal:success', 'Draft surat telah diajukan untuk approval Kepala Sekolah.');
        $this->surat->refresh();
    }

    public function approve(SuratService $service)
    {
        $this->authorize('approve', $this->surat);
        
        $service->approveSuratKeluar($this->surat);
        
        // Notify pembuat
        $pembuat = $this->surat->dibuatOleh;
        if ($pembuat && $pembuat->no_hp) {
            $pesan = "Halo {$pembuat->name}, draf surat Anda (Perihal: {$this->surat->perihal}) telah DISETUJUI oleh Kepala Sekolah.\nNomor Surat: {$this->surat->no_surat}\nSilakan cek di sistem SITU-SMA.";
            \App\Jobs\SendWhatsAppNotification::dispatch($pembuat->no_hp, $pesan);
        }

        $this->dispatch('swal:success', 'Surat berhasil di-approve. Nomor surat dan PDF telah di-generate.');
        $this->surat->refresh();
    }

    public function openRejectModal()
    {
        $this->authorize('approve', $this->surat);
        $this->catatan_revisi = '';
        $this->isRejectModalOpen = true;
    }

    public function closeRejectModal()
    {
        $this->isRejectModalOpen = false;
    }

    public function reject(SuratService $service)
    {
        $this->authorize('approve', $this->surat);
        
        $this->validate([
            'catatan_revisi' => 'required|string|min:5'
        ]);

        $service->rejectSuratKeluar($this->surat, $this->catatan_revisi);
        
        $this->isRejectModalOpen = false;
        // Notify pembuat
        $pembuat = $this->surat->dibuatOleh;
        if ($pembuat && $pembuat->no_hp) {
            $pesan = "Halo {$pembuat->name}, draf surat Anda (Perihal: {$this->surat->perihal}) DITOLAK/DIKEMBALIKAN oleh Kepala Sekolah.\nCatatan: {$this->catatan_revisi}\nSilakan cek di sistem SITU-SMA.";
            \App\Jobs\SendWhatsAppNotification::dispatch($pembuat->no_hp, $pesan);
        }

        $this->dispatch('swal:success', 'Surat telah dikembalikan untuk direvisi.');
        $this->surat->refresh();
    }

    public function render()
    {
        return view('livewire.administrasi.surat-keluar.detail-surat-keluar');
    }
}
