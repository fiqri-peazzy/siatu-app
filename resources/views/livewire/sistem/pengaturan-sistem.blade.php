@php
    $input = 'h-10 w-full rounded-xl border-0 bg-white px-3 text-[14px] text-navy-900 ring-1 ring-ivory-300 placeholder:text-navy-300 focus:ring-2 focus:ring-navy-600';
    $inputJam = 'h-9 rounded-lg border-0 bg-white px-2 text-[13px] tabular-nums text-navy-900 ring-1 ring-ivory-300 focus:ring-2 focus:ring-navy-600 disabled:bg-ivory-100 disabled:text-navy-300';
    $label = 'mb-1.5 block text-[12px] font-semibold text-navy-600';
    $tombol = 'inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-navy-900 px-5 text-[13px] font-semibold text-white hover:bg-navy-800 disabled:opacity-60';
    $tabs = [
        'identitas' => ['Identitas sekolah', 'sekolah', 'Kop surat & laporan'],
        'absensi' => ['Jam absensi', 'jam', 'Check-in & check-out'],
        'lokasi' => ['Lokasi absen', 'lokasi', 'Titik & radius GPS'],
        'notifikasi' => ['Notifikasi', 'lonceng', 'Pengingat & ambang'],
    ];
@endphp

<div class="space-y-5 font-body">
    <x-slot name="header">Pengaturan Sistem</x-slot>

    <div>
        <h1 class="font-heading text-2xl font-semibold text-navy-900">Pengaturan Sistem</h1>
        <p class="mt-1 text-[13px] text-navy-500">Perubahan berlaku langsung untuk seluruh pengguna — portal absensi, kop laporan, dan pengingat.</p>
    </div>

    <div class="grid gap-5 lg:grid-cols-[15rem_1fr]">
        {{-- Navigasi tab --}}
        <nav class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 lg:mx-0 lg:flex-col lg:overflow-visible lg:px-0" aria-label="Bagian pengaturan">
            @foreach ($tabs as $k => [$judul, $ikon, $ket])
                <a href="{{ route('sistem.pengaturan', ['tab' => $k]) }}" @class([
                    'flex shrink-0 items-center gap-3 rounded-xl px-3 py-2.5 text-left transition',
                    'bg-navy-900 text-white' => $tab === $k,
                    'bg-white text-navy-700 ring-1 ring-ivory-300 hover:bg-ivory-100' => $tab !== $k,
                ]) aria-current="{{ $tab === $k ? 'page' : 'false' }}">
                    <x-ui.icon :name="$ikon" class="h-5 w-5 shrink-0 {{ $tab === $k ? 'text-gold-300' : 'text-navy-400' }}" />
                    <span>
                        <span class="block text-[13px] font-semibold">{{ $judul }}</span>
                        <span class="hidden text-[11px] lg:block {{ $tab === $k ? 'text-white/55' : 'text-navy-400' }}">{{ $ket }}</span>
                    </span>
                </a>
            @endforeach
        </nav>

        <div class="min-w-0">
            {{-- ============ IDENTITAS ============ --}}
            @if ($tab === 'identitas')
                <form wire:submit="simpanIdentitas" class="space-y-5">
                    <x-ui.panel judul="Identitas sekolah" keterangan="Dipakai di kop surat, export Excel, PDF, dan tanda tangan laporan">
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach (['nama', 'npsn', 'akreditasi', 'telepon', 'email', 'alamat', 'kota', 'provinsi'] as $k)
                                <div @class(['sm:col-span-2' => in_array($k, ['nama', 'alamat'])])>
                                    <label for="id-{{ $k }}" class="{{ $label }}">{{ $labelIdentitas[$k] }}</label>
                                    <input id="id-{{ $k }}" type="{{ $k === 'email' ? 'email' : 'text' }}" wire:model="identitas.{{ $k }}" class="{{ $input }}" @if ($k === 'npsn') inputmode="numeric" maxlength="8" @endif>
                                    @error("identitas.$k") <p class="mt-1 text-[12px] text-bad-500">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    </x-ui.panel>

                    <x-ui.panel judul="Pejabat penandatangan" keterangan="Muncul di blok tanda tangan rekap & laporan">
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach (['kepala', 'nip_kepala', 'kepala_tu', 'nip_ka_tu'] as $k)
                                <div>
                                    <label for="id-{{ $k }}" class="{{ $label }}">{{ $labelIdentitas[$k] }}</label>
                                    <input id="id-{{ $k }}" type="text" wire:model="identitas.{{ $k }}" class="{{ $input }}">
                                    @error("identitas.$k") <p class="mt-1 text-[12px] text-bad-500">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    </x-ui.panel>

                    {{-- Pratinjau kop --}}
                    <x-ui.panel judul="Pratinjau kop" keterangan="Tampilan ringkas, belum termasuk logo">
                        <div class="rounded-xl bg-ivory-100 p-4 text-center ring-1 ring-ivory-300">
                            @foreach (explode('|', config('sekolah.induk')) as $baris)
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-navy-500">{{ $baris }}</p>
                            @endforeach
                            <p class="mt-1 font-heading text-lg font-bold uppercase text-navy-900">{{ $identitas['nama'] ?: '—' }}</p>
                            <p class="text-[12px] text-navy-600">
                                {{ $identitas['alamat'] }}{{ $identitas['kota'] ? ', '.$identitas['kota'] : '' }}{{ $identitas['provinsi'] ? ', '.$identitas['provinsi'] : '' }}
                            </p>
                            <p class="text-[12px] text-navy-500">
                                {{ collect(['NPSN '.$identitas['npsn'] => $identitas['npsn'], 'Telp. '.$identitas['telepon'] => $identitas['telepon'], $identitas['email'] => $identitas['email']])->filter()->keys()->implode(' · ') }}
                            </p>
                            <div class="mt-2 border-t-2 border-double border-navy-900 pt-px"><div class="border-t border-navy-900"></div></div>
                        </div>
                    </x-ui.panel>

                    <div class="flex justify-end"><button type="submit" class="{{ $tombol }}" wire:loading.attr="disabled" wire:target="simpanIdentitas">Simpan identitas</button></div>
                </form>
            @endif

            {{-- ============ JAM ABSENSI ============ --}}
            @if ($tab === 'absensi')
                <form wire:submit="simpanJam" class="space-y-5">
                    <x-ui.panel judul="Jam absensi" keterangan="Portal guru/tendik membaca aturan ini setiap kali absen">
                        <x-slot:aksi>
                            <div class="flex rounded-xl bg-ivory-200 p-1" role="tablist">
                                @foreach (\App\Enums\KategoriAbsensi::cases() as $kat)
                                    <button type="button" role="tab" wire:click="$set('kategori', '{{ $kat->value }}')" @class([
                                        'rounded-lg px-3 py-1.5 text-[12px] font-semibold',
                                        'bg-white text-navy-900 shadow-sm' => $kategori === $kat->value,
                                        'text-navy-500' => $kategori !== $kat->value,
                                    ])>{{ $kat->label() }}</button>
                                @endforeach
                            </div>
                        </x-slot:aksi>

                        {{-- Legenda --}}
                        <div class="mb-4 grid gap-2 text-[12px] text-navy-600 sm:grid-cols-3">
                            <p class="rounded-xl bg-ivory-100 px-3 py-2"><b class="text-navy-900">Buka → Tepat</b><br>Absen masuk tercatat <span class="text-ok-500">hadir</span>.</p>
                            <p class="rounded-xl bg-ivory-100 px-3 py-2"><b class="text-navy-900">Tepat + toleransi → Tutup</b><br>Tercatat <span class="text-warn-500">terlambat</span>; lewat tutup ditolak.</p>
                            <p class="rounded-xl bg-ivory-100 px-3 py-2"><b class="text-navy-900">Pulang</b><br>Sebelum jam mulai pulang wajib alasan (bila diizinkan).</p>
                        </div>

                        <div class="-mx-4 overflow-x-auto sm:-mx-5">
                            <table class="w-full min-w-[52rem] text-left text-[12px]">
                                <thead class="text-[11px] uppercase tracking-wider text-navy-400">
                                    <tr>
                                        <th class="px-4 pb-2 font-semibold sm:px-5">Hari</th>
                                        <th class="px-1 pb-2 font-semibold">Masuk buka</th>
                                        <th class="px-1 pb-2 font-semibold">Tepat</th>
                                        <th class="px-1 pb-2 font-semibold">Toleransi</th>
                                        <th class="px-1 pb-2 font-semibold">Masuk tutup</th>
                                        <th class="px-1 pb-2 font-semibold">Mulai pulang</th>
                                        <th class="px-1 pb-2 font-semibold">Pulang tutup</th>
                                        <th class="px-1 pb-2 font-semibold">Pulang cepat</th>
                                        <th class="px-4 pb-2 sm:px-5"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-ivory-300">
                                    @foreach ($hariList as $h)
                                        @php $p = "jam.$kategori.{$h->value}"; $kerja = $jam[$kategori][$h->value]['hari_kerja'] ?? false; @endphp
                                        <tr wire:key="jam-{{ $kategori }}-{{ $h->value }}" class="{{ $kerja ? '' : 'bg-ivory-100/60' }}">
                                            <td class="px-4 py-2.5 sm:px-5">
                                                <label class="flex items-center gap-2.5">
                                                    <input type="checkbox" wire:model.live="{{ $p }}.hari_kerja" class="h-4 w-4 rounded border-ivory-300 text-navy-900 focus:ring-navy-600">
                                                    <span class="font-semibold {{ $kerja ? 'text-navy-900' : 'text-navy-300' }}">{{ $h->label() }}</span>
                                                </label>
                                            </td>
                                            @foreach (['masuk_buka', 'masuk_tepat'] as $k)
                                                <td class="px-1 py-2.5"><input type="text" inputmode="numeric" maxlength="5" placeholder="JJ:MM" pattern="[0-2][0-9]:[0-5][0-9]" wire:model="{{ $p }}.{{ $k }}" class="{{ $inputJam }} w-[4.25rem] text-center" @disabled(! $kerja) aria-label="{{ $h->label() }} {{ str_replace('_', ' ', $k) }}"></td>
                                            @endforeach
                                            <td class="px-1 py-2.5">
                                                <div class="flex items-center gap-1"><input type="number" min="0" max="120" wire:model="{{ $p }}.toleransi_menit" class="{{ $inputJam }} w-14 text-center" @disabled(! $kerja) aria-label="{{ $h->label() }} toleransi"><span class="text-navy-400">mnt</span></div>
                                            </td>
                                            @foreach (['masuk_tutup', 'pulang_buka', 'pulang_tutup'] as $k)
                                                <td class="px-1 py-2.5"><input type="text" inputmode="numeric" maxlength="5" placeholder="JJ:MM" pattern="[0-2][0-9]:[0-5][0-9]" wire:model="{{ $p }}.{{ $k }}" class="{{ $inputJam }} w-[4.25rem] text-center" @disabled(! $kerja) aria-label="{{ $h->label() }} {{ str_replace('_', ' ', $k) }}"></td>
                                            @endforeach
                                            <td class="px-1 py-2.5 text-center">
                                                <input type="checkbox" wire:model="{{ $p }}.pulang_cepat_diizinkan" class="h-4 w-4 rounded border-ivory-300 text-navy-900 focus:ring-navy-600" @disabled(! $kerja) aria-label="{{ $h->label() }} izinkan pulang cepat">
                                            </td>
                                            <td class="px-4 py-2.5 text-right sm:px-5">
                                                @if ($kerja && $h->value !== 'senin')
                                                    <button type="button" wire:click="salinKeHariLain('{{ $h->value }}')" class="whitespace-nowrap text-[12px] font-semibold text-navy-500 hover:text-navy-900" title="Salin ke hari kerja lain kecuali Senin">Salin</button>
                                                @endif
                                            </td>
                                        </tr>
                                        @foreach (['masuk_buka', 'masuk_tepat', 'toleransi_menit', 'masuk_tutup', 'pulang_buka', 'pulang_tutup'] as $k)
                                            @error("$p.$k")
                                                <tr><td colspan="9" class="px-4 pb-2 text-[12px] text-bad-500 sm:px-5">{{ $message }}</td></tr>
                                            @enderror
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-ui.panel>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <button type="button" wire:click="kembalikanDefault" wire:confirm="Muat ulang nilai bawaan untuk {{ $kategori }}? Perubahan belum tersimpan akan hilang." class="text-[13px] font-semibold text-navy-500 hover:text-navy-900">Kembalikan nilai bawaan</button>
                        <button type="submit" class="{{ $tombol }}" wire:loading.attr="disabled" wire:target="simpanJam">Simpan jam {{ $kategori }}</button>
                    </div>
                </form>
            @endif

            {{-- ============ LOKASI ============ --}}
            @if ($tab === 'lokasi')
                <form wire:submit="simpanLokasi" class="space-y-5">
                    <x-ui.panel judul="Titik lokasi absensi" keterangan="Guru & tendik hanya bisa absen di dalam lingkaran radius">
                        <div class="grid gap-5 lg:grid-cols-[18rem_1fr]">
                            <div class="space-y-4" x-data="{ mencari: false }">
                                <button type="button" class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-ivory-200 text-[13px] font-semibold text-navy-800 hover:bg-ivory-300 disabled:opacity-60"
                                        :disabled="mencari"
                                        @click="mencari = true; navigator.geolocation.getCurrentPosition(
                                            p => { $wire.set('lat_sekolah', p.coords.latitude.toFixed(7)); $wire.set('lng_sekolah', p.coords.longitude.toFixed(7)); window.dispatchEvent(new CustomEvent('peta-ke', { detail: { lat: p.coords.latitude, lng: p.coords.longitude } })); mencari = false; },
                                            () => { mencari = false; window.showAppAlert.error('Lokasi tidak bisa diambil. Pastikan izin lokasi & GPS aktif.'); },
                                            { enableHighAccuracy: true, timeout: 15000 })">
                                    <x-ui.icon name="lokasi" class="h-4 w-4" />
                                    <span x-text="mencari ? 'Mencari lokasi…' : 'Pakai lokasi saya sekarang'"></span>
                                </button>
                                <div>
                                    <label for="lat" class="{{ $label }}">Latitude</label>
                                    <input id="lat" type="text" inputmode="decimal" wire:model.blur="lat_sekolah" class="{{ $input }} tabular-nums">
                                    @error('lat_sekolah') <p class="mt-1 text-[12px] text-bad-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="lng" class="{{ $label }}">Longitude</label>
                                    <input id="lng" type="text" inputmode="decimal" wire:model.blur="lng_sekolah" class="{{ $input }} tabular-nums">
                                    @error('lng_sekolah') <p class="mt-1 text-[12px] text-bad-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="radius" class="{{ $label }}">Radius (meter)</label>
                                    <input id="radius" type="number" min="10" max="1000" wire:model.live.debounce.400ms="radius_absen" class="{{ $input }} tabular-nums">
                                    @error('radius_absen') <p class="mt-1 text-[12px] text-bad-500">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-[12px] text-navy-400">Disarankan 50–100 m. GPS ponsel bisa meleset 10–30 m.</p>
                                </div>
                            </div>
                            <div wire:ignore class="relative z-0 h-80 overflow-hidden rounded-xl ring-1 ring-ivory-300 lg:h-full lg:min-h-[22rem]" id="peta-lokasi"></div>
                        </div>
                        <p class="mt-3 text-[12px] text-navy-400">Klik peta atau geser penanda untuk memindahkan titik.</p>
                    </x-ui.panel>
                    <div class="flex justify-end"><button type="submit" class="{{ $tombol }}" wire:loading.attr="disabled" wire:target="simpanLokasi">Simpan lokasi</button></div>
                </form>

                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
                @script
                <script>
                    const muatLeaflet = () => window.L ? Promise.resolve() : new Promise((ok, gagal) => {
                        const s = document.createElement('script');
                        s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        s.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
                        s.crossOrigin = '';
                        s.onload = ok; s.onerror = gagal;
                        document.head.appendChild(s);
                    });

                    muatLeaflet().then(() => {
                        const el = document.getElementById('peta-lokasi');
                        if (!el || el._leaflet_id) return;
                        const lat = parseFloat($wire.lat_sekolah) || 0.540182, lng = parseFloat($wire.lng_sekolah) || 123.059293;
                        const peta = L.map(el).setView([lat, lng], 18);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(peta);
                        const penanda = L.marker([lat, lng], { draggable: true }).addTo(peta);
                        const lingkar = L.circle([lat, lng], { color: '#0e2a55', fillColor: '#c9a227', fillOpacity: 0.18, weight: 2, radius: parseInt($wire.radius_absen) || 50 }).addTo(peta);

                        const pindah = (ll, kirim = true) => {
                            penanda.setLatLng(ll); lingkar.setLatLng(ll);
                            if (kirim) { $wire.set('lat_sekolah', ll.lat.toFixed(7)); $wire.set('lng_sekolah', ll.lng.toFixed(7)); }
                        };
                        penanda.on('dragend', () => pindah(penanda.getLatLng()));
                        peta.on('click', e => pindah(e.latlng));
                        window.addEventListener('peta-ke', e => { const ll = L.latLng(e.detail.lat, e.detail.lng); pindah(ll, false); peta.setView(ll, 18); });
                        $wire.$watch('radius_absen', v => lingkar.setRadius(parseInt(v) || 50));
                        const dariInput = () => { const a = parseFloat($wire.lat_sekolah), b = parseFloat($wire.lng_sekolah); if (!isNaN(a) && !isNaN(b)) { pindah(L.latLng(a, b), false); peta.panTo([a, b]); } };
                        $wire.$watch('lat_sekolah', dariInput);
                        $wire.$watch('lng_sekolah', dariInput);
                        setTimeout(() => peta.invalidateSize(), 200);
                    }).catch(() => {
                        const el = document.getElementById('peta-lokasi');
                        if (el) el.innerHTML = '<p class="p-6 text-center text-[13px] text-navy-400">Peta tidak dapat dimuat. Isi koordinat secara manual.</p>';
                    });
                </script>
                @endscript
            @endif

            {{-- ============ NOTIFIKASI ============ --}}
            @if ($tab === 'notifikasi')
                <form wire:submit="simpanNotifikasi" class="space-y-5">
                    <x-ui.panel judul="Pengingat jurnal mengajar" keterangan="Dikirim ke guru yang masih punya jurnal belum terisi hari itu (web push)">
                        <div class="space-y-4">
                            <label class="flex items-start justify-between gap-4">
                                <span>
                                    <span class="block text-[14px] font-semibold text-navy-900">Kirim pengingat harian</span>
                                    <span class="block text-[12px] text-navy-500">Hari libur nasional otomatis dilewati. Guru tetap bisa mematikannya di aplikasi.</span>
                                </span>
                                <input type="checkbox" wire:model.live="reminder_jurnal_aktif" class="peer sr-only">
                                <span class="relative mt-1 h-6 w-11 shrink-0 rounded-full bg-ivory-300 transition peer-checked:bg-navy-900 peer-focus-visible:ring-2 peer-focus-visible:ring-navy-600 after:absolute after:top-0.5 after:left-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-5" aria-hidden="true"></span>
                            </label>
                            <div class="max-w-[12rem]">
                                <label for="jam-reminder" class="{{ $label }}">Jam kirim (WITA)</label>
                                <input id="jam-reminder" type="text" inputmode="numeric" maxlength="5" placeholder="JJ:MM" pattern="[0-2][0-9]:[0-5][0-9]" wire:model="reminder_jurnal_jam" class="{{ $input }} tabular-nums" @disabled(! $reminder_jurnal_aktif)>
                                @error('reminder_jurnal_jam') <p class="mt-1 text-[12px] text-bad-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </x-ui.panel>

                    <x-ui.panel judul="Peringatan siswa alfa" keterangan="Siswa dengan jumlah hari alfa sebanyak ini dalam sebulan ditandai di dashboard kepala sekolah">
                        <div class="flex items-center gap-3">
                            <input id="ambang" type="number" min="1" max="31" wire:model="ambang_alfa_siswa" class="{{ $input }} w-24 tabular-nums" aria-label="Ambang hari alfa">
                            <span class="text-[13px] text-navy-600">hari alfa per bulan</span>
                        </div>
                        @error('ambang_alfa_siswa') <p class="mt-1 text-[12px] text-bad-500">{{ $message }}</p> @enderror
                    </x-ui.panel>

                    <x-ui.panel judul="Status kanal" keterangan="Kanal pengiriman notifikasi">
                        <ul class="space-y-2 text-[13px]">
                            <li class="flex items-center justify-between gap-2 rounded-xl bg-ivory-100 px-3 py-2.5">
                                <span class="font-semibold text-navy-900">Web push (PWA)</span>
                                <span class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $pushSiap ? 'bg-ok-50 text-ok-500' : 'bg-warn-50 text-warn-500' }}">{{ $pushSiap ? 'Aktif' : 'Kunci VAPID belum diatur' }}</span>
                            </li>
                            <li class="flex items-center justify-between gap-2 rounded-xl bg-ivory-100 px-3 py-2.5">
                                <span class="font-semibold text-navy-900">WhatsApp (Fonnte)</span>
                                <span class="rounded-full bg-ivory-200 px-2.5 py-0.5 text-[11px] font-semibold text-navy-500">Nonaktif</span>
                            </li>
                        </ul>
                    </x-ui.panel>

                    <div class="flex justify-end"><button type="submit" class="{{ $tombol }}" wire:loading.attr="disabled" wire:target="simpanNotifikasi">Simpan notifikasi</button></div>
                </form>
            @endif
        </div>
    </div>
</div>
