<?php

namespace App\Livewire\Mobile\Guru;

use App\Services\JurnalService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Form jurnal mengajar 3 langkah untuk satu blok sesi (jadwal v2):
 *   1. Kehadiran siswa  2. Materi & kegiatan  3. Dokumentasi & simpan
 * Aturan (kepemilikan sesi, jam tergeser, peserta mapel pilihan) ada di JurnalService.
 */
class FormJurnal extends Component
{
    use WithFileUploads;

    public string $sesi = '';

    /** Langkah aktif tersimpan di URL: refresh / tombol back tidak mengulang dari awal */
    #[\Livewire\Attributes\Url(except: 1)]
    public int $langkah = 1;

    /** Tanggal pertemuan (Y-m-d); kosong = hari ini. Boleh tanggal lampau, tidak boleh mendatang. */
    #[\Livewire\Attributes\Url(except: '')]
    public string $tgl = '';

    private function tanggal(): Carbon
    {
        try {
            return $this->tgl ? Carbon::createFromFormat('Y-m-d', $this->tgl)->startOfDay() : Carbon::today();
        } catch (\Throwable) {
            return Carbon::today();
        }
    }

    public array $info = [];
    public array $kelasList = [];
    public array $siswaList = [];
    public array $absensi = [];
    public ?array $sebelumnya = null;
    public bool $modeEdit = false;

    public ?string $kd_cp = null;
    public string $materi = '';
    public string $kegiatan = '';
    public array $dokumentasi = [];
    public array $bukti_izin = [];

    public function mount(string $sesi, JurnalService $service)
    {
        $this->sesi = $sesi;
        $this->langkah = max(1, min(3, $this->langkah));

        try {
            $ctx = $service->siapkan(Auth::user()->guru_id, $this->sesiIds(), $this->tanggal());
        } catch (AuthorizationException) {
            abort(403);
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());

            return $this->redirectRoute('guru.jadwal', navigate: true);
        }

        $this->info = [
            'mapel' => $ctx['mapel']?->nama ?? '-',
            'mulai' => $ctx['mulai'],
            'selesai' => $ctx['selesai'],
            'jam_ke' => $ctx['jam_ke'],
            'tanggal' => $this->tanggal()->locale('id')->isoFormat('dddd, D MMMM YYYY'),
            'lampau' => $this->tanggal()->lt(Carbon::today()),
        ];
        $this->kelasList = $ctx['kelas']->map(fn ($k) => ['id' => $k->id, 'nama' => $k->nama])->all();
        $this->siswaList = $ctx['siswa']->map(fn ($s) => ['id' => $s->id, 'nama' => $s->nama, 'nis' => $s->nis, 'kelas_id' => $s->kelas_id])->all();
        $this->absensi = array_map('strval', $ctx['absensi']);

        if ($j = $ctx['jurnal']) {
            $this->modeEdit = true;
            $this->kd_cp = $j->kd_cp;
            $this->materi = (string) $j->materi;
            $this->kegiatan = (string) $j->kegiatan;
        }
        if ($p = $ctx['sebelumnya']) {
            $this->sebelumnya = [
                'tanggal' => $p->tanggal->locale('id')->isoFormat('D MMM'),
                'kd_cp' => $p->kd_cp,
                'materi' => $p->materi,
                'kegiatan' => $p->kegiatan,
            ];
        }
    }

    private function sesiIds(): array
    {
        return array_values(array_filter(array_map('intval', explode(',', $this->sesi))));
    }

    protected function rules(): array
    {
        return [
            'kd_cp' => ['nullable', 'string', 'max:500'],
            'materi' => ['required', 'string', 'min:5', 'max:2000'],
            'kegiatan' => ['required', 'string', 'min:5', 'max:2000'],
            'absensi.*' => ['in:hadir,izin,sakit,alfa'],
            'dokumentasi' => ['array', 'max:4'],
            'dokumentasi.*' => ['image', 'max:5120'],
            'bukti_izin.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    protected function messages(): array
    {
        return [
            'materi.required' => 'Materi pembelajaran wajib diisi.',
            'materi.min' => 'Materi minimal 5 karakter.',
            'kegiatan.required' => 'Kegiatan pembelajaran wajib diisi.',
            'kegiatan.min' => 'Kegiatan minimal 5 karakter.',
            'dokumentasi.max' => 'Maksimal 4 foto dokumentasi.',
            'dokumentasi.*.image' => 'Dokumentasi harus berupa foto.',
            'dokumentasi.*.max' => 'Ukuran foto maksimal 5MB.',
        ];
    }

    public function salinSebelumnya(): void
    {
        if (! $this->sebelumnya) {
            return;
        }
        $this->kd_cp = $this->sebelumnya['kd_cp'];
        $this->materi = (string) $this->sebelumnya['materi'];
        $this->kegiatan = (string) $this->sebelumnya['kegiatan'];
    }

    public function keLangkah(int $tujuan): void
    {
        if ($tujuan > 2 && $this->langkah <= 2) {
            $this->validateOnly('materi');
            $this->validateOnly('kegiatan');
            $this->validateOnly('kd_cp');
        }
        $this->langkah = max(1, min(3, $tujuan));
    }

    public function hapusFoto(int $i): void
    {
        unset($this->dokumentasi[$i]);
        $this->dokumentasi = array_values($this->dokumentasi);
    }

    public function simpan(JurnalService $service)
    {
        $this->validate();

        try {
            $dokumentasi = $this->unggahDokumentasi();
            $bukti = $this->unggahBuktiIzin();

            $service->simpan(
                guruId: Auth::user()->guru_id,
                userId: Auth::id(),
                sesiIds: $this->sesiIds(),
                tanggal: $this->tanggal(),
                isi: ['kd_cp' => $this->kd_cp, 'materi' => $this->materi, 'kegiatan' => $this->kegiatan],
                absensi: $this->absensi,
                buktiIzin: $bukti,
                dokumentasi: $dokumentasi,
            );
        } catch (InvalidArgumentException $e) {
            $this->dispatch('swal:error', $e->getMessage());

            return;
        } catch (\Throwable $e) {
            Log::error('Simpan jurnal gagal: '.$e->getMessage());
            $this->dispatch('swal:error', 'Jurnal gagal disimpan. Coba lagi beberapa saat.');

            return;
        }

        session()->flash('success', $this->modeEdit ? 'Jurnal berhasil diperbarui.' : 'Jurnal & absensi siswa tersimpan.');

        return $this->redirect(route('guru.jadwal', ['tgl' => $this->tanggal()->toDateString()]), navigate: true);
    }

    private function unggahDokumentasi(): array
    {
        $paths = [];
        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver);
        foreach ($this->dokumentasi as $foto) {
            $image = $manager->decodePath($foto->getRealPath())->scaleDown(width: 1280, height: 1280);
            $path = 'jurnal/'.now()->format('Y/m').'/'.uniqid().'.jpg';
            Storage::disk('r2')->put($path, (string) $image->encodeUsingFileExtension('jpg', quality: 70));
            $paths[] = $path;
        }

        return $paths;
    }

    private function unggahBuktiIzin(): array
    {
        $paths = [];
        foreach ($this->bukti_izin as $siswaId => $file) {
            if ($file && in_array($this->absensi[$siswaId] ?? null, ['izin', 'sakit'], true)) {
                $paths[(int) $siswaId] = $file->storeAs('bukti_izin', "bukti_izin_{$siswaId}_".uniqid().'.'.$file->getClientOriginalExtension(), 'r2');
            }
        }

        return $paths;
    }

    public function render()
    {
        return view('livewire.mobile.guru.form-jurnal')
            ->layout('components.layouts.pwa', ['judul' => 'Isi Jurnal', 'tanpaNav' => true]);
    }
}
