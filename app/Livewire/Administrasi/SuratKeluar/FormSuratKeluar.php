<?php

namespace App\Livewire\Administrasi\SuratKeluar;

use App\Models\Master\MasterKategoriSurat;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Services\SuratService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class FormSuratKeluar extends Component
{
    use WithFileUploads, AuthorizesRequests;

    public ?SuratKeluar $surat = null;

    public $kategori_surat_id = '';
    public $surat_masuk_id = '';
    public $tanggal = '';
    public $tujuan = '';
    public $perihal = '';
    public $isi_ringkas = '';
    public $file;

    public bool $isEdit = false;

    protected function rules()
    {
        return [
            'kategori_surat_id' => ['required', 'exists:master_kategori_surats,id'],
            'surat_masuk_id' => ['nullable', 'exists:surat_masuks,id'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'tujuan' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string', 'max:255'],
            'isi_ringkas' => ['required', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,png,doc,docx', 'max:5120'],
        ];
    }

    public function mount(?SuratKeluar $surat = null)
    {
        if ($surat && $surat->exists) {
            $this->authorize('update', $surat);
            $this->isEdit = true;
            $this->surat = $surat;
            $this->kategori_surat_id = $surat->kategori_surat_id;
            $this->surat_masuk_id = $surat->surat_masuk_id;
            $this->tanggal = $surat->tanggal->format('Y-m-d');
            $this->tujuan = $surat->tujuan;
            $this->perihal = $surat->perihal;
            $this->isi_ringkas = $surat->isi_ringkas;
        } else {
            $this->authorize('create', SuratKeluar::class);
            $this->tanggal = date('Y-m-d');
        }
    }

    #[Computed]
    public function kategoris()
    {
        return MasterKategoriSurat::whereIn('tipe', ['keluar', 'keduanya'])->get();
    }

    #[Computed]
    public function suratMasuks()
    {
        // Menampilkan 100 surat masuk terbaru untuk pilihan balasan
        return SuratMasuk::latest()->take(100)->get();
    }

    public function simpan(SuratService $suratService)
    {
        $validatedData = $this->validate();
        
        if ($this->surat_masuk_id == '') {
            $validatedData['surat_masuk_id'] = null;
        }

        if ($this->isEdit) {
            $suratService->updateSuratKeluar($this->surat, $validatedData, $this->file);
            session()->flash('success', 'Draft Surat Keluar berhasil diperbarui.');
        } else {
            $validatedData['dibuat_oleh'] = auth()->id();
            $suratService->simpanSuratKeluar($validatedData, $this->file);
            session()->flash('success', 'Draft Surat Keluar berhasil dibuat dan berstatus DRAFT.');
        }

        return redirect()->route('administrasi.surat-keluar.index');
    }

    public function render()
    {
        return view('livewire.administrasi.surat-keluar.form-surat-keluar');
    }
}
