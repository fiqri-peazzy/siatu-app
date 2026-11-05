<div class="fixed inset-0 z-[60] overflow-y-auto bg-ivory-200 font-body text-navy-900">
    <div class="flex min-h-dvh flex-col lg:flex-row">

        {{-- Panel identitas: atas (mobile) / kiri (desktop) --}}
        <section class="relative overflow-hidden bg-console px-6 pt-[max(env(safe-area-inset-top),2.5rem)] pb-16 text-white lg:flex lg:w-[46%] lg:flex-col lg:justify-between lg:px-14 lg:py-14">
            <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="" aria-hidden="true"
                 class="pointer-events-none absolute -right-16 -bottom-16 h-72 w-72 object-contain opacity-[0.06] lg:h-[28rem] lg:w-[28rem]">

            <div class="relative flex items-center gap-3">
                <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="Logo SMA Muhammadiyah Kota Gorontalo" class="h-12 w-12 object-contain">
                <div class="leading-tight">
                    <p class="font-heading text-lg font-semibold">SIATU</p>
                    <p class="text-[12px] text-white/60">SMA Muhammadiyah Kota Gorontalo</p>
                </div>
            </div>

            <div class="relative mt-8 lg:mt-0">
                <h1 class="font-heading text-[1.9rem] font-semibold leading-tight lg:text-[2.6rem]">Tata usaha sekolah,<br><span class="text-gold-300">dalam satu genggaman.</span></h1>
                <p class="mt-3 max-w-md text-[15px] leading-relaxed text-white/65">Absensi, jadwal & jurnal mengajar, persuratan, dan rekap sekolah — untuk guru, tendik, dan pimpinan.</p>
            </div>

            <ul class="relative mt-10 hidden space-y-3 lg:block">
                @foreach (['Absen dengan lokasi & swafoto', 'Jurnal mengajar langsung dari kelas', 'Pantau kehadiran secara real-time'] as $poin)
                    <li class="flex items-center gap-3 text-[14px] text-white/80">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gold-400/15 text-gold-300"><x-ui.icon name="cek" class="h-3.5 w-3.5" /></span>{{ $poin }}
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Form --}}
        <section class="relative -mt-8 flex flex-1 items-start justify-center rounded-t-[2rem] bg-ivory-200 px-6 pt-8 pb-[max(env(safe-area-inset-bottom),2rem)] lg:mt-0 lg:items-center lg:rounded-none lg:px-14">
            <div class="w-full max-w-sm">
                <h2 class="font-heading text-2xl font-semibold text-navy-900">Masuk</h2>
                <p class="mt-1 text-[14px] text-navy-500">Gunakan email yang terdaftar di sekolah.</p>

                @if ($errors->any())
                    <div role="alert" class="mt-5 flex gap-2.5 rounded-2xl bg-bad-50 p-3.5 text-[13px] font-medium text-bad-500 ring-1 ring-bad-400/25">
                        <x-ui.icon name="peringatan" class="h-5 w-5 shrink-0" />
                        <span>{{ $errors->first('email') ?: $errors->first() }}</span>
                    </div>
                @endif

                <form wire:submit="login" class="mt-6 space-y-4" novalidate>
                    <div>
                        <label for="email" class="text-[13px] font-semibold text-navy-700">Email</label>
                        <div class="relative mt-1.5">
                            <x-ui.icon name="pengguna" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-navy-300" />
                            <input wire:model="email" type="email" id="email" inputmode="email" autocomplete="username" autofocus
                                   placeholder="nama@sekolah.sch.id" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                   class="h-12 w-full rounded-2xl border-0 bg-white pl-12 pr-4 text-[15px] ring-1 placeholder:text-navy-300 focus:ring-2 focus:ring-gold-500 {{ $errors->has('email') ? 'ring-bad-400' : 'ring-ivory-400' }}">
                        </div>
                    </div>

                    <div x-data="{ lihat: false, caps: false }">
                        <label for="password" class="text-[13px] font-semibold text-navy-700">Password</label>
                        <div class="relative mt-1.5">
                            <x-ui.icon name="kunci" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-navy-300" />
                            <input wire:model="password" id="password" :type="lihat ? 'text' : 'password'" autocomplete="current-password"
                                   @keyup="caps = $event.getModifierState && $event.getModifierState('CapsLock')"
                                   class="h-12 w-full rounded-2xl border-0 bg-white pl-12 pr-20 text-[15px] ring-1 focus:ring-2 focus:ring-gold-500 {{ $errors->has('password') ? 'ring-bad-400' : 'ring-ivory-400' }}">
                            <button type="button" @click="lihat = ! lihat" :aria-pressed="lihat" class="absolute right-1.5 top-1.5 h-9 rounded-xl px-3 text-[12px] font-semibold text-navy-500 hover:bg-ivory-200"
                                    x-text="lihat ? 'Sembunyi' : 'Lihat'">Lihat</button>
                        </div>
                        <p x-show="caps" x-cloak class="mt-1.5 text-[12px] font-medium text-warn-500">Caps Lock aktif</p>
                        @error('password') <p class="mt-1.5 text-[12px] font-medium text-bad-500">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex cursor-pointer items-center gap-2.5 text-[14px] text-navy-600">
                        <input wire:model="remember" type="checkbox" class="h-5 w-5 rounded-md border-ivory-400 text-navy-900 focus:ring-gold-500">
                        Ingat saya di perangkat ini
                    </label>

                    <button type="submit" wire:loading.attr="disabled" wire:target="login"
                            class="flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-navy-900 text-[15px] font-semibold text-white shadow-lg shadow-navy-900/20 transition hover:bg-navy-800 active:scale-[0.98] disabled:opacity-70">
                        <span wire:loading.remove wire:target="login">Masuk</span>
                        <span wire:loading wire:target="login" class="inline-flex items-center gap-2">
                            <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span> Memeriksa…
                        </span>
                    </button>
                </form>

                <p class="mt-6 text-center text-[13px] text-navy-400">Lupa password atau belum punya akun?<br>Hubungi <span class="font-semibold text-navy-700">Admin Tata Usaha</span>.</p>
            </div>
        </section>
    </div>
</div>
