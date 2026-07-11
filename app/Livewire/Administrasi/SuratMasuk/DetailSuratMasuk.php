<?php

namespace App\Livewire\Administrasi\SuratMasuk;

use App\Models\SuratMasuk;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class DetailSuratMasuk extends Component
{
    use AuthorizesRequests;

    public SuratMasuk $surat;

    public bool $isDisposisiModalOpen = false;
    public $ke_user_id = '';
    public $catatan = '';

    protected $rules = [
        'ke_user_id' => 'required|exists:users,id',
        'catatan' => 'required|string|max:500',
    ];

    public function mount(SuratMasuk $surat)
    {
        $this->authorize('view', $surat);
        $this->surat = $surat->load(['kategori', 'klasifikasiArsip', 'inputOleh', 'disposisis.dariUser', 'disposisis.keUser']);
    }

    #[\Livewire\Attributes\Computed]
    public function users()
    {
        // Get all users except current user
        return \App\Models\User::where('id', '!=', auth()->id())->get();
    }

    public function openDisposisiModal()
    {
        $this->authorize('dispose', $this->surat);
        $this->reset(['ke_user_id', 'catatan']);
        $this->isDisposisiModalOpen = true;
    }

    public function closeDisposisiModal()
    {
        $this->isDisposisiModalOpen = false;
    }

    public function saveDisposisi()
    {
        $this->authorize('dispose', $this->surat);
        $this->validate();

        // Cari disposisi terakhir yang ditujukan ke user saat ini (jika ada) untuk parent
        $parentDisposisi = $this->surat->disposisis()
            ->where('ke_user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->first();

        $level = $parentDisposisi ? $parentDisposisi->level + 1 : 1;

        \Illuminate\Support\Facades\DB::transaction(function () use ($parentDisposisi, $level) {
            \App\Models\Disposisi::create([
                'surat_masuk_id' => $this->surat->id,
                'parent_disposisi_id' => $parentDisposisi ? $parentDisposisi->id : null,
                'level' => $level,
                'dari_user_id' => auth()->id(),
                'ke_user_id' => $this->ke_user_id,
                'catatan' => $this->catatan,
                'status' => \App\Enums\StatusDisposisi::DITERIMA,
            ]);

            // Update status parent disposisi (jika ada)
            if ($parentDisposisi) {
                $parentDisposisi->update(['status' => \App\Enums\StatusDisposisi::DITERUSKAN]);
            }

            // Update status surat masuk jika masih 'baru'
            if ($this->surat->status->value === 'baru') {
                $this->surat->update(['status' => \App\Enums\StatusSuratMasuk::DIDISPOSISI]);
            }
        });

        // Dispatch WA Notification to the recipient
        $penerima = \App\Models\User::find($this->ke_user_id);
        if ($penerima && $penerima->no_hp) {
            $pesan = "Halo {$penerima->name}, Anda mendapat disposisi surat baru (No: {$this->surat->no_agenda}).\nCatatan: {$this->catatan}\nSilakan cek di sistem SITU-SMA.";
            \App\Jobs\SendWhatsAppNotification::dispatch($penerima->no_hp, $pesan);
        }

        $this->surat->refresh();
        $this->isDisposisiModalOpen = false;
        $this->dispatch('swal:success', 'Disposisi berhasil dikirim.');
    }

    public function tandaiSelesai()
    {
        $this->authorize('dispose', $this->surat);

        $disposisi = $this->surat->disposisis()
            ->where('ke_user_id', auth()->id())
            ->where('status', \App\Enums\StatusDisposisi::DITERIMA)
            ->first();

        if ($disposisi) {
            $disposisi->update(['status' => \App\Enums\StatusDisposisi::SELESAI]);
            
            // Check if there are no more active dispositions
            $activeDisposisiCount = $this->surat->disposisis()
                ->where('status', \App\Enums\StatusDisposisi::DITERIMA)
                ->count();
                
            if ($activeDisposisiCount === 0) {
                $this->surat->update(['status' => \App\Enums\StatusSuratMasuk::SELESAI]);
            }
            
            $this->dispatch('swal:success', 'Disposisi telah ditandai sebagai selesai dan ditindaklanjuti.');
        } else {
            // For kepsek/admin marking the whole letter done directly
            $this->surat->update(['status' => \App\Enums\StatusSuratMasuk::SELESAI]);
            $this->dispatch('swal:success', 'Surat masuk telah ditandai selesai.');
        }

        $this->surat->refresh();
    }

    public function render()
    {
        return view('livewire.administrasi.surat-masuk.detail-surat-masuk');
    }
}
