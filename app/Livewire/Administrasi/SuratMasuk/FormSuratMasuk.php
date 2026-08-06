<?php

namespace App\Livewire\Administrasi\SuratMasuk;

use App\Enums\SifatSurat;
use App\Models\Master\MasterKategoriSurat;
use App\Models\Master\MasterKlasifikasiArsip;
use App\Models\SuratMasuk;
use App\Services\SuratService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class FormSuratMasuk extends Component
{
    use WithFileUploads, AuthorizesRequests;

    public ?SuratMasuk $surat = null;

    public $kategori_surat_id = '';
    public $klasifikasi_arsip_id = '';
    public $tanggal_terima = '';
    public $tanggal_surat = '';
    public $asal_instansi = '';
    public $perihal = '';
    public $sifat = 'biasa';
    public $file;
    public $metadata = [];
    public $dynamicConfig = [];

    public bool $isEdit = false;

    protected function rules()
    {
        $rules = [
            'kategori_surat_id' => ['required', 'exists:master_kategori_surats,id'],
            'klasifikasi_arsip_id' => ['nullable', 'exists:master_klasifikasi_arsips,id'],
            'tanggal_terima' => ['required', 'date', 'before_or_equal:today'],
            'tanggal_surat' => ['required', 'date', 'before_or_equal:tanggal_terima'],
            'asal_instansi' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string', 'max:255'],
            'sifat' => ['required', \Illuminate\Validation\Rule::in(SifatSurat::values())],
            'file' => [$this->isEdit ? 'nullable' : 'required', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
        ];

        if ($this->dynamicConfig && is_array($this->dynamicConfig)) {
            foreach ($this->dynamicConfig as $field) {
                $rules['metadata.' . $field['name']] = $field['required'] ? ['required'] : ['nullable'];
            }
        }

        return $rules;
    }

    protected function messages()
    {
        $messages = [
            'file.max' => 'Ukuran file maksimal 5MB.',
            'file.mimes' => 'Format file harus PDF, JPG, atau PNG.',
            'tanggal_surat.before_or_equal' => 'Tanggal surat asli tidak boleh melewati tanggal terima.',
            'tanggal_terima.before_or_equal' => 'Tanggal terima tidak boleh melewati hari ini.',
        ];

        if ($this->dynamicConfig && is_array($this->dynamicConfig)) {
            foreach ($this->dynamicConfig as $field) {
                if ($field['required']) {
                    $messages['metadata.' . $field['name'] . '.required'] = $field['label'] . ' wajib diisi.';
                }
            }
        }

        return $messages;
    }

    public function mount(?SuratMasuk $surat = null)
    {
        if ($surat && $surat->exists) {
            $this->authorize('update', $surat);
            $this->isEdit = true;
            $this->surat = $surat;
            $this->kategori_surat_id = $surat->kategori_surat_id;
            $this->klasifikasi_arsip_id = $surat->klasifikasi_arsip_id;
            $this->tanggal_terima = $surat->tanggal_terima->format('Y-m-d');
            $this->tanggal_surat = $surat->tanggal_surat->format('Y-m-d');
            $this->asal_instansi = $surat->asal_instansi;
            $this->perihal = $surat->perihal;
            $this->sifat = $surat->sifat->value;
            $this->metadata = $surat->metadata ?? [];
            $this->loadDynamicConfig();
        } else {
            $this->authorize('create', SuratMasuk::class);
            $this->tanggal_terima = date('Y-m-d');
        }
    }

    public function updatedKategoriSuratId()
    {
        $this->metadata = [];
        $this->loadDynamicConfig();
    }

    private function loadDynamicConfig()
    {
        if ($this->kategori_surat_id) {
            $kategori = MasterKategoriSurat::find($this->kategori_surat_id);
            $this->dynamicConfig = $kategori ? $kategori->config : [];
            
            if ($this->dynamicConfig && is_array($this->dynamicConfig)) {
                foreach ($this->dynamicConfig as $field) {
                    if (!isset($this->metadata[$field['name']])) {
                        $this->metadata[$field['name']] = '';
                    }
                }
            }
        } else {
            $this->dynamicConfig = [];
        }
    }

    #[Computed]
    public function kategoris()
    {
        return MasterKategoriSurat::whereIn('tipe', ['masuk', 'keduanya'])->get();
    }

    #[Computed]
    public function klasifikasis()
    {
        return MasterKlasifikasiArsip::all();
    }

    public function simpan(SuratService $suratService)
    {
        $validatedData = $this->validate();
        $validatedData['input_oleh'] = auth()->id();
        $validatedData['metadata'] = $this->metadata;

        if ($this->isEdit) {
            $suratService->updateSuratMasuk($this->surat, $validatedData, $this->file);
            session()->flash('success', 'Surat Masuk berhasil diperbarui.');
        } else {
            $suratService->simpanSuratMasuk($validatedData, $this->file);
            session()->flash('success', 'Surat Masuk berhasil ditambahkan. Nomor agenda di-generate otomatis.');
        }

        return redirect()->route('administrasi.surat-masuk.index');
    }

    public function render()
    {
        return view('livewire.administrasi.surat-masuk.form-surat-masuk');
    }
}
