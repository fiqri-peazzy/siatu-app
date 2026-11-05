<?php

namespace App\Livewire\Sistem;

use App\Enums\Hari;
use App\Enums\KategoriAbsensi;
use App\Models\AturanAbsensi;
use App\Services\AturanAbsensiService;
use App\Services\PengaturanService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Pengaturan Sistem terpadu (Admin TU / Super Admin):
 * identitas sekolah (kop), jam absensi guru & tendik, lokasi absen, notifikasi.
 */
#[Layout('components.layouts.app')]
class PengaturanSistem extends Component
{
    #[Url]
    public string $tab = 'identitas';

    #[Url(as: 'kategori')]
    public string $kategori = 'guru';

    /** @var array<string, string> */
    public array $identitas = [];

    /** @var array<string, array<string, array<string, mixed>>> kategori => hari => kolom */
    public array $jam = [];

    public $lat_sekolah;

    public $lng_sekolah;

    public $radius_absen;

    public bool $reminder_jurnal_aktif = true;

    public string $reminder_jurnal_jam = '14:00';

    public int $ambang_alfa_siswa = 3;

    private const KOLOM_JAM = ['masuk_buka', 'masuk_tepat', 'masuk_tutup', 'pulang_buka', 'pulang_tutup'];

    public function mount(PengaturanService $p, AturanAbsensiService $aturan): void
    {
        abort_unless(auth()->user()->hasAnyRole(['admin-tu', 'super-admin']), 403);
        if (! in_array($this->tab, ['identitas', 'absensi', 'lokasi', 'notifikasi'], true)) {
            $this->tab = 'identitas';
        }
        if (! in_array($this->kategori, KategoriAbsensi::values(), true)) {
            $this->kategori = 'guru';
        }

        foreach (array_keys(PengaturanService::IDENTITAS) as $k) {
            $this->identitas[$k] = (string) config("sekolah.{$k}", '');
        }

        $aturan->pasangDefault(); // pastikan semua baris ada
        $this->muatJam();

        $this->lat_sekolah = $p->nilai('lat_sekolah');
        $this->lng_sekolah = $p->nilai('lng_sekolah');
        $this->radius_absen = $p->nilai('radius_absen', 50);

        $this->reminder_jurnal_aktif = $p->aktif('reminder_jurnal_aktif');
        $this->reminder_jurnal_jam = (string) $p->nilai('reminder_jurnal_jam');
        $this->ambang_alfa_siswa = (int) $p->nilai('ambang_alfa_siswa');
    }

    private function muatJam(): void
    {
        $this->jam = [];
        foreach (AturanAbsensi::all() as $a) {
            $baris = ['hari_kerja' => $a->hari_kerja, 'toleransi_menit' => $a->toleransi_menit, 'pulang_cepat_diizinkan' => $a->pulang_cepat_diizinkan];
            foreach (self::KOLOM_JAM as $k) {
                $baris[$k] = $a->jam($k);
            }
            $this->jam[$a->kategori->value][$a->hari->value] = $baris;
        }
    }

    public function simpanIdentitas(PengaturanService $p): void
    {
        $data = $this->validate([
            'identitas.nama' => ['required', 'string', 'max:150'],
            'identitas.npsn' => ['nullable', 'digits:8'],
            'identitas.akreditasi' => ['nullable', 'string', 'max:5'],
            'identitas.alamat' => ['required', 'string', 'max:255'],
            'identitas.kota' => ['required', 'string', 'max:100'],
            'identitas.provinsi' => ['required', 'string', 'max:100'],
            'identitas.telepon' => ['nullable', 'string', 'max:30'],
            'identitas.email' => ['nullable', 'email', 'max:150'],
            'identitas.kepala' => ['required', 'string', 'max:150'],
            'identitas.nip_kepala' => ['nullable', 'string', 'max:30'],
            'identitas.kepala_tu' => ['nullable', 'string', 'max:150'],
            'identitas.nip_ka_tu' => ['nullable', 'string', 'max:30'],
        ], [
            'identitas.npsn.digits' => 'NPSN harus 8 digit angka.',
        ], $this->labelIdentitas())['identitas'];

        $p->simpan(collect($data)->mapWithKeys(fn ($v, $k) => ["sekolah.{$k}" => trim((string) $v)])->all(), 'text');
        $this->dispatch('swal:success', 'Identitas sekolah disimpan. Kop surat & laporan memakai data baru.');
    }

    public function simpanJam(): void
    {
        $kategori = KategoriAbsensi::from($this->kategori);
        foreach (Hari::cases() as $h) {
            foreach (self::KOLOM_JAM as $k) {
                $this->jam[$kategori->value][$h->value][$k] = self::rapikanJam($this->jam[$kategori->value][$h->value][$k] ?? '');
            }
        }
        $rules = [];
        foreach (Hari::cases() as $h) {
            $p = "jam.{$kategori->value}.{$h->value}";
            $rules["{$p}.hari_kerja"] = ['boolean'];
            $rules["{$p}.pulang_cepat_diizinkan"] = ['boolean'];
            $rules["{$p}.toleransi_menit"] = ['required', 'integer', 'min:0', 'max:120'];
            foreach (self::KOLOM_JAM as $k) {
                $rules["{$p}.{$k}"] = ['required', 'date_format:H:i'];
            }
        }
        $this->validate($rules, [
            '*.date_format' => 'Format jam harus JJ:MM.',
            '*.required' => 'Wajib diisi.',
            '*.max' => 'Maksimal 120 menit.',
        ]);

        $galat = [];
        foreach (Hari::cases() as $h) {
            $b = $this->jam[$kategori->value][$h->value];
            if (! $b['hari_kerja']) {
                continue;
            }
            $urut = [$b['masuk_buka'], $b['masuk_tepat'], $b['masuk_tutup'], $b['pulang_buka'], $b['pulang_tutup']];
            for ($i = 1; $i < count($urut); $i++) {
                if ($urut[$i] < $urut[$i - 1] || ($i === 4 && $urut[4] === $urut[3])) {
                    $galat["jam.{$kategori->value}.{$h->value}.".self::KOLOM_JAM[$i]] =
                        "{$h->label()}: urutan jam harus buka ≤ tepat ≤ tutup masuk ≤ mulai pulang < tutup pulang.";
                    break;
                }
            }
        }
        if ($galat) {
            throw ValidationException::withMessages($galat);
        }

        DB::transaction(function () use ($kategori) {
            foreach (Hari::cases() as $h) {
                $b = $this->jam[$kategori->value][$h->value];
                AturanAbsensi::updateOrCreate(
                    ['kategori' => $kategori->value, 'hari' => $h->value],
                    [
                        'hari_kerja' => (bool) $b['hari_kerja'],
                        'toleransi_menit' => (int) $b['toleransi_menit'],
                        'pulang_cepat_diizinkan' => (bool) $b['pulang_cepat_diizinkan'],
                        ...collect(self::KOLOM_JAM)->mapWithKeys(fn ($k) => [$k => $b[$k]])->all(),
                    ]
                );
            }
        });

        $this->dispatch('swal:success', "Jam absensi {$kategori->label()} disimpan dan langsung berlaku.");
    }

    /** Salin jam hari Selasa ke Rabu–Sabtu (Senin sering berbeda karena upacara) */
    public function salinKeHariLain(string $dari): void
    {
        $sumber = $this->jam[$this->kategori][$dari] ?? null;
        if (! $sumber) {
            return;
        }
        foreach (Hari::cases() as $h) {
            if ($h->value !== $dari && $h !== Hari::SENIN && $this->jam[$this->kategori][$h->value]['hari_kerja']) {
                $this->jam[$this->kategori][$h->value] = [...$sumber, 'hari_kerja' => true];
            }
        }
        $this->dispatch('swal:info', 'Jam '.ucfirst($dari).' disalin ke hari kerja lain (kecuali Senin). Jangan lupa simpan.');
    }

    public function kembalikanDefault(AturanAbsensiService $aturan): void
    {
        $default = AturanAbsensiService::DEFAULT[$this->kategori];
        foreach ($default as $hari => [$kerja, $buka, $tepat, $toleransi, $tutup, $pulangBuka, $pulangTutup]) {
            $this->jam[$this->kategori][$hari] = [
                'hari_kerja' => $kerja, 'masuk_buka' => $buka, 'masuk_tepat' => $tepat, 'toleransi_menit' => $toleransi,
                'masuk_tutup' => $tutup, 'pulang_buka' => $pulangBuka, 'pulang_tutup' => $pulangTutup, 'pulang_cepat_diizinkan' => true,
            ];
        }
        $this->dispatch('swal:info', 'Nilai bawaan dimuat. Tekan Simpan untuk menerapkan.');
    }

    public function simpanLokasi(PengaturanService $p): void
    {
        $this->validate([
            'lat_sekolah' => ['required', 'numeric', 'between:-90,90'],
            'lng_sekolah' => ['required', 'numeric', 'between:-180,180'],
            'radius_absen' => ['required', 'integer', 'min:10', 'max:1000'],
        ], [
            'radius_absen.min' => 'Radius minimal 10 meter.',
            'radius_absen.max' => 'Radius maksimal 1000 meter.',
        ], ['lat_sekolah' => 'latitude', 'lng_sekolah' => 'longitude', 'radius_absen' => 'radius']);

        $p->simpan(['lat_sekolah' => $this->lat_sekolah, 'lng_sekolah' => $this->lng_sekolah], 'location');
        $p->simpan(['radius_absen' => (int) $this->radius_absen], 'number');
        $this->dispatch('swal:success', 'Lokasi & radius absensi disimpan.');
    }

    /** "7.15" / "7:15" / "0715" → "07:15"; selain itu dikembalikan apa adanya agar ditolak validasi */
    private static function rapikanJam(?string $v): string
    {
        $v = trim((string) $v);
        if (preg_match('/^(\d{1,2})[.:]?(\d{2})$/', $v, $m)) {
            return sprintf('%02d:%s', $m[1], $m[2]);
        }

        return $v;
    }

    public function simpanNotifikasi(PengaturanService $p): void
    {
        $this->reminder_jurnal_jam = self::rapikanJam($this->reminder_jurnal_jam);
        $this->validate([
            'reminder_jurnal_jam' => ['required', 'date_format:H:i'],
            'ambang_alfa_siswa' => ['required', 'integer', 'min:1', 'max:31'],
        ], [], ['reminder_jurnal_jam' => 'jam pengingat', 'ambang_alfa_siswa' => 'ambang alfa']);

        $p->simpan([
            'reminder_jurnal_aktif' => $this->reminder_jurnal_aktif,
            'reminder_jurnal_jam' => $this->reminder_jurnal_jam,
            'ambang_alfa_siswa' => $this->ambang_alfa_siswa,
        ], 'notifikasi');
        $this->dispatch('swal:success', 'Pengaturan notifikasi disimpan.');
    }

    private function labelIdentitas(): array
    {
        return collect(PengaturanService::IDENTITAS)->mapWithKeys(fn ($l, $k) => ["identitas.{$k}" => strtolower($l)])->all();
    }

    public function render()
    {
        return view('livewire.sistem.pengaturan-sistem', [
            'labelIdentitas' => PengaturanService::IDENTITAS,
            'hariList' => Hari::cases(),
            'pushSiap' => filled(config('webpush.vapid.public_key')),
        ]);
    }
}
