@php
    $inisial = collect(explode(' ', trim($guru->nama)))->filter()->take(2)->map(fn ($k) => mb_substr($k, 0, 1))->implode('');
    $menuAkun = [
        ['pengguna', 'Data pribadi', 'No. HP & alamat', "sheet = 'data'"],
        ['kunci', 'Ganti password', 'Amankan akun Anda', "sheet = 'password'"],
    ];
@endphp

<div class="font-body" x-data="{ sheet: null }" @swal:success.window="sheet = null">

    {{-- Identitas --}}
    <section class="relative overflow-hidden rounded-3xl bg-navy-900 p-5 text-white">
        <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="" aria-hidden="true" class="pointer-events-none absolute -right-8 -bottom-10 h-40 w-40 object-contain opacity-[0.06]">
        <div class="relative flex items-center gap-4">
            <label for="foto-upload" class="group relative block shrink-0 cursor-pointer" aria-label="Ganti foto profil">
                <span class="block h-20 w-20 overflow-hidden rounded-full ring-4 ring-white/10">
                    @if ($foto)
                        <img src="{{ $foto->temporaryUrl() }}" alt="" class="h-full w-full object-cover">
                    @elseif ($guru->foto_path)
                        <img src="{{ Storage::disk('r2')->url($guru->foto_path) }}" alt="" class="h-full w-full object-cover">
                    @else
                        <span class="flex h-full w-full items-center justify-center bg-gold-400 font-heading text-2xl font-semibold text-navy-950">{{ $inisial }}</span>
                    @endif
                </span>
                <span class="absolute -bottom-0.5 -right-0.5 flex h-7 w-7 items-center justify-center rounded-full bg-white text-navy-900 ring-2 ring-navy-900">
                    <x-ui.icon name="kamera" class="h-3.5 w-3.5" />
                </span>
                <span wire:loading wire:target="foto" class="absolute inset-0 flex items-center justify-center rounded-full bg-navy-950/70 text-[11px] font-semibold">…</span>
            </label>
            <input type="file" id="foto-upload" wire:model="foto" accept="image/*" class="sr-only">

            <div class="min-w-0">
                <h1 class="font-heading text-lg font-semibold leading-snug">{{ $guru->nama }}</h1>
                <p class="mt-0.5 text-[13px] text-white/65">{{ $guru->jabatan ?: 'Guru' }}{{ $guru->mapel ? ' · '.$guru->mapel->nama : '' }}</p>
                <p class="mt-1.5 text-[12px] tabular-nums text-white/50">
                    @if ($guru->nip) NIP {{ $guru->nip }} @elseif ($guru->nuptk) NUPTK {{ $guru->nuptk }} @else NIP/NUPTK belum diisi @endif
                </p>
            </div>
        </div>
        @error('foto') <p class="relative mt-3 text-[12px] text-rose-300">{{ $message }}</p> @enderror
    </section>

    {{-- Kepatuhan jurnal --}}
    <section class="mt-3 rounded-2xl bg-white p-4 ring-1 ring-ivory-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[12px] font-medium text-navy-400">Kepatuhan jurnal bulan ini</p>
                <p class="mt-0.5 font-heading text-2xl font-semibold text-navy-900">{{ $persentase }}<span class="text-base text-navy-300">%</span></p>
            </div>
            <a wire:navigate href="{{ route('guru.jurnal.riwayat') }}" class="-my-2 -mr-2 inline-flex min-h-10 items-center px-2 text-[13px] font-semibold text-navy-600">Riwayat</a>
        </div>
        <div class="mt-3 h-2 overflow-hidden rounded-full bg-ivory-300">
            <div class="h-full rounded-full {{ $persentase >= 80 ? 'bg-ok-500' : ($persentase >= 50 ? 'bg-warn-400' : 'bg-bad-400') }}" style="width: {{ $persentase }}%"></div>
        </div>
        <p class="mt-2 text-[12px] text-navy-400">Target sekolah 100% — isi jurnal setiap selesai mengajar.</p>
    </section>

    {{-- Menu --}}
    @foreach ([
        'Akun' => $menuAkun,
        'Aplikasi' => [
            ['lonceng', 'Notifikasi & pengingat', 'Atur pengingat jurnal', null, route('guru.pengaturan-notifikasi')],
            ['riwayat', 'Riwayat absensi', 'Masuk & pulang', null, route('akademik.absensi.guru.riwayat')],
            ['info', 'Panduan aplikasi', 'Izin, notifikasi, pasang aplikasi', "window.dispatchEvent(new CustomEvent('siatu:onboarding'))"],
        ],
    ] as $grup => $menu)
        <h2 class="mt-6 mb-2 px-1 text-[12px] font-semibold uppercase tracking-wider text-navy-400">{{ $grup }}</h2>
        <ul class="divide-y divide-ivory-300 overflow-hidden rounded-2xl bg-white ring-1 ring-ivory-300">
            @foreach ($menu as $m)
                @php [$ikon, $label, $ket, $aksi] = $m; $href = $m[4] ?? null; @endphp
                <li>
                    <{{ $href ? 'a' : 'button' }} @if ($href) wire:navigate href="{{ $href }}" @else type="button" @click="{{ $aksi }}" @endif
                        class="flex w-full items-center gap-3.5 px-4 py-3.5 text-left transition hover:bg-ivory-100">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-ivory-200 text-navy-700"><x-ui.icon :name="$ikon" /></span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-[15px] font-semibold text-navy-900">{{ $label }}</span>
                            <span class="block text-[12px] text-navy-400">{{ $ket }}</span>
                        </span>
                        <x-ui.icon name="chevron-kanan" class="h-4 w-4 text-navy-300" />
                    </{{ $href ? 'a' : 'button' }}>
                </li>
            @endforeach
        </ul>
    @endforeach

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-white text-[15px] font-semibold text-bad-500 ring-1 ring-ivory-300 active:scale-[0.98]">
            <x-ui.icon name="keluar" class="h-5 w-5" /> Keluar
        </button>
    </form>
    <p class="mt-4 text-center text-[11px] text-navy-300">SIATU · SMA Muhammadiyah Kota Gorontalo</p>

    {{-- Sheet: data pribadi & password --}}
    <template x-teleport="body">
        <div x-show="sheet" x-cloak x-transition.opacity @keydown.escape.window="sheet = null" @click.self="sheet = null"
             class="fixed inset-0 z-50 flex items-end justify-center bg-navy-950/60 backdrop-blur-[2px] sm:items-center sm:p-6">
            <div role="dialog" aria-modal="true" class="w-full max-w-md rounded-t-[1.75rem] bg-ivory-50 px-5 pt-3 pb-safe-lg font-body shadow-sheet sm:rounded-[1.75rem] sm:pb-6"
                 x-show="sheet" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-y-full">
                <div class="mb-3 flex justify-center sm:hidden"><span class="h-1.5 w-10 rounded-full bg-navy-200"></span></div>

                <form x-show="sheet === 'data'" wire:submit="updateProfil" class="space-y-4">
                    <h2 class="font-heading text-lg font-semibold text-navy-900">Data pribadi</h2>
                    <div>
                        <label for="no_hp" class="text-[13px] font-semibold text-navy-700">No. HP / WhatsApp</label>
                        <input id="no_hp" type="tel" inputmode="tel" wire:model="no_hp" autocomplete="tel"
                               class="mt-1.5 h-12 w-full rounded-2xl border-0 bg-white px-4 text-[15px] ring-1 ring-ivory-300 focus:ring-2 focus:ring-gold-500">
                        @error('no_hp') <p class="mt-1 text-[12px] text-bad-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="alamat" class="text-[13px] font-semibold text-navy-700">Alamat</label>
                        <textarea id="alamat" rows="3" wire:model="alamat" autocomplete="street-address"
                                  class="mt-1.5 w-full resize-none rounded-2xl border-0 bg-white px-4 py-3 text-[15px] ring-1 ring-ivory-300 focus:ring-2 focus:ring-gold-500"></textarea>
                        @error('alamat') <p class="mt-1 text-[12px] text-bad-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-2.5 pt-1">
                        <button type="button" @click="sheet = null" class="h-12 rounded-2xl text-[15px] font-semibold text-navy-700 ring-1 ring-ivory-400">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" class="h-12 rounded-2xl bg-navy-900 text-[15px] font-semibold text-white disabled:opacity-60">Simpan</button>
                    </div>
                </form>

                <form x-show="sheet === 'password'" wire:submit="updatePassword" class="space-y-4">
                    <h2 class="font-heading text-lg font-semibold text-navy-900">Ganti password</h2>
                    @foreach ([['password_lama', 'Password saat ini', 'current-password'], ['password_baru', 'Password baru', 'new-password'], ['password_baru_confirmation', 'Ulangi password baru', 'new-password']] as [$f, $label, $ac])
                        <div x-data="{ lihat: false }">
                            <label for="{{ $f }}" class="text-[13px] font-semibold text-navy-700">{{ $label }}</label>
                            <div class="relative mt-1.5">
                                <input id="{{ $f }}" :type="lihat ? 'text' : 'password'" wire:model="{{ $f }}" autocomplete="{{ $ac }}"
                                       class="h-12 w-full rounded-2xl border-0 bg-white pl-4 pr-12 text-[15px] ring-1 ring-ivory-300 focus:ring-2 focus:ring-gold-500">
                                <button type="button" @click="lihat = ! lihat" class="absolute right-1 top-1 h-10 rounded-xl px-3 text-[12px] font-semibold text-navy-500" x-text="lihat ? 'Sembunyi' : 'Lihat'"></button>
                            </div>
                            @error($f) <p class="mt-1 text-[12px] text-bad-500">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                    <div class="grid grid-cols-2 gap-2.5 pt-1">
                        <button type="button" @click="sheet = null" class="h-12 rounded-2xl text-[15px] font-semibold text-navy-700 ring-1 ring-ivory-400">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" class="h-12 rounded-2xl bg-navy-900 text-[15px] font-semibold text-white disabled:opacity-60">Ganti password</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
