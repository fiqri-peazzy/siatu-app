<?php

namespace App\Livewire\Master\Siswa;

use App\Models\Master\MasterKelas;
use App\Models\Master\MasterSiswa;
use App\Services\RombelService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/** Pindah rombel massal: pilih rombel asal → centang siswa → pilih rombel tujuan. */
#[Layout('components.layouts.app')]
class PindahRombel extends Component
{
    /** id rombel asal, atau 'tanpa' untuk siswa aktif yang belum punya rombel */
    #[Url(as: 'asal')]
    public string $asal = '';

    #[Url(as: 'tujuan')]
    public string $tujuan = '';

    public string $cari = '';

    /** @var list<string> */
    public array $dipilih = [];

    public function mount(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['admin-tu', 'super-admin', 'staf-tu']), 403);
        if ($this->asal === '') {
            // Bawaan: rombel dengan siswa terbanyak (paling mungkin perlu dipecah)
            $this->asal = (string) ($this->rombel->sortByDesc('siswa_count')->first()?->id ?? '');
        }
    }

    #[Computed]
    public function rombel()
    {
        return MasterKelas::query()
            ->whereHas('tahunAjaran', fn ($q) => $q->where('is_active', true))
            ->withCount(['siswa' => fn ($q) => $q->where('status', 'aktif')])
            ->with('waliKelas:id,nama')
            ->orderBy('tingkat')->orderBy('nama')
            ->get(['id', 'nama', 'tingkat', 'jurusan', 'wali_kelas_id']);
    }

    #[Computed]
    public function tanpaRombel(): int
    {
        $aktif = $this->rombel->pluck('id');

        return MasterSiswa::where('status', 'aktif')->where(fn ($q) => $q->whereNull('kelas_id')->orWhereNotIn('kelas_id', $aktif))->count();
    }

    #[Computed]
    public function siswaAsal()
    {
        if ($this->asal === '') {
            return collect();
        }
        $aktif = $this->rombel->pluck('id');

        return MasterSiswa::query()
            ->where('status', 'aktif')
            ->when($this->asal === 'tanpa',
                fn ($q) => $q->where(fn ($q) => $q->whereNull('kelas_id')->orWhereNotIn('kelas_id', $aktif)),
                fn ($q) => $q->where('kelas_id', (int) $this->asal))
            ->when($this->cari !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('nama', 'like', "%{$this->cari}%")->orWhere('nis', 'like', "%{$this->cari}%")->orWhere('nisn', 'like', "%{$this->cari}%")))
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis', 'nisn', 'jk']);
    }

    #[Computed]
    public function siswaTujuan()
    {
        return $this->tujuan === '' ? collect() : MasterSiswa::where('status', 'aktif')->where('kelas_id', (int) $this->tujuan)->orderBy('nama')->get(['id', 'nama', 'nis', 'jk']);
    }

    public function updatedAsal(): void
    {
        $this->dipilih = [];
        $this->cari = '';
        if ($this->tujuan === $this->asal) {
            $this->tujuan = '';
        }
    }

    public function pindahkan(RombelService $rombel): void
    {
        $this->validate([
            'tujuan' => ['required', 'integer', 'different:asal'],
            'dipilih' => ['required', 'array', 'min:1'],
        ], [
            'tujuan.required' => 'Pilih rombel tujuan.',
            'tujuan.different' => 'Rombel tujuan harus berbeda dengan rombel asal.',
            'dipilih.required' => 'Centang minimal satu siswa.',
        ]);

        // Hanya siswa yang memang ada di daftar asal saat ini (cegah id titipan)
        $sah = array_values(array_intersect($this->dipilih, $this->siswaAsal->pluck('id')->map(fn ($id) => (string) $id)->all()));
        $jumlah = $rombel->pindahkan($sah, (int) $this->tujuan);
        $nama = $this->rombel->firstWhere('id', (int) $this->tujuan)?->nama;

        $this->dipilih = [];
        unset($this->rombel, $this->siswaAsal, $this->siswaTujuan, $this->tanpaRombel);
        $this->dispatch('swal:success', "{$jumlah} siswa dipindahkan ke {$nama}.");
    }

    public function render()
    {
        return view('livewire.master.siswa.pindah-rombel');
    }
}
