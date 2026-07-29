<div class="space-y-6 pb-12" x-data="{ modalDataPribadi: false, modalPassword: false }" @swal:success.window="modalDataPribadi = false; modalPassword = false">

    {{-- ── Profile Hero ── --}}
    <div class="mt-2 flex flex-col items-center text-center">
        {{-- Avatar with badge --}}
        <div class="relative mb-4">
            <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg overflow-hidden bg-sky-mist">
                @if($guru->foto)
                    <img src="{{ asset('storage/' . $guru->foto) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-navy-deep">
                        <svg class="w-12 h-12 text-sky-mist" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                @endif
            </div>
            {{-- Verified badge --}}
            <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-signal-blue rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
        <h2 class="text-xl font-extrabold text-ink-900 tracking-tight leading-tight">{{ $guru->nama_guru }}</h2>
        <p class="text-sm text-slate-500 font-medium mt-1 font-mono">NIP. {{ $guru->nip ?? '-' }}</p>
    </div>

    {{-- ── Ringkasan Bulanan Card ── --}}
    <div class="bg-navy-deep rounded-[1.5rem] p-5 shadow-[0_8px_30px_rgba(18,42,82,0.15)] relative overflow-hidden">
        <div class="absolute inset-0 border border-white/10 rounded-[1.5rem] pointer-events-none"></div>
        <div class="flex items-start justify-between mb-3">
            <div>
                <p class="text-[10px] font-bold text-sky-mist uppercase tracking-[0.1em] opacity-80">Ringkasan Bulanan</p>
                <p class="text-white font-bold text-base mt-0.5">Kepatuhan Jurnal</p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
        </div>
        <div class="flex items-baseline gap-2 mb-2">
            <span class="text-4xl font-extrabold text-white font-mono">{{ $persentase }}%</span>
            <span class="text-sm text-sky-mist opacity-80">Bulan ini</span>
        </div>
        <div class="w-full bg-white/20 rounded-full h-2 overflow-hidden mb-2">
            <div class="h-full rounded-full transition-all duration-1000 ease-out
                {{ $persentase >= 80 ? 'bg-success-teal' : ($persentase >= 50 ? 'bg-warning-amber' : 'bg-danger-coral') }}"
                 style="width: {{ $persentase }}%"></div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-[11px] text-sky-mist opacity-70 font-mono">{{ $totalTerisi ?? 0 }}/{{ $totalJam ?? 0 }} Sesi Terisi</span>
            <span class="text-[11px] font-bold text-warning-amber">Target: 100%</span>
        </div>
    </div>

    {{-- ── Menu List ── --}}
    <div class="bg-white rounded-2xl border border-line-200 shadow-sm overflow-hidden divide-y divide-line-200">

        {{-- Data Pribadi --}}
        <button @click="modalDataPribadi = true" type="button"
                class="w-full flex items-center gap-4 p-4 hover:bg-paper transition-colors active:bg-paper group">
            <div class="w-9 h-9 rounded-xl bg-signal-blue/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-signal-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
            </div>
            <span class="flex-1 text-sm font-medium text-ink-900 text-left">Data Pribadi</span>
            <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        {{-- Notifikasi --}}
        <a href="{{ route('guru.pengaturan-notifikasi') }}"
           class="flex items-center gap-4 p-4 hover:bg-paper transition-colors active:bg-paper group">
            <div class="w-9 h-9 rounded-xl bg-signal-blue/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-signal-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <span class="flex-1 text-sm font-medium text-ink-900">Notifikasi</span>
            <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        {{-- Ganti Password --}}
        <button @click="modalPassword = true" type="button"
                class="w-full flex items-center gap-4 p-4 hover:bg-paper transition-colors active:bg-paper group">
            <div class="w-9 h-9 rounded-xl bg-signal-blue/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-signal-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <span class="flex-1 text-sm font-medium text-ink-900 text-left">Ganti Password</span>
            <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        {{-- Keluar --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-4 p-4 hover:bg-danger-coral/5 transition-colors active:bg-danger-coral/5 group">
                <div class="w-9 h-9 rounded-xl bg-danger-coral/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-danger-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </div>
                <span class="flex-1 text-sm font-medium text-danger-coral text-left">Keluar</span>
                <svg class="w-4 h-4 text-danger-coral/50 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </form>
    </div>

    {{-- Version --}}
    <p class="text-center text-[11px] text-slate-400 font-mono">Versi Aplikasi 2.4.0-build.82</p>

    {{-- ── Modal: Data Pribadi ── --}}
    <div x-show="modalDataPribadi" style="display: none;" class="fixed inset-0 z-[70] flex items-end justify-center bg-black/40 backdrop-blur-sm" @click="modalDataPribadi = false" x-transition.opacity>
        <div class="bg-white rounded-t-3xl w-full max-w-md p-6 shadow-2xl" @click.stop 
             x-show="modalDataPribadi" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="translate-y-full" 
             x-transition:enter-end="translate-y-0" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="translate-y-0" 
             x-transition:leave-end="translate-y-full">
            <div class="w-10 h-1 bg-line-200 rounded-full mx-auto mb-6"></div>
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-ink-900 text-lg">Data Pribadi</h3>
                <button type="button" @click="modalDataPribadi = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form wire:submit.prevent="updateProfil" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">No. HP / WhatsApp</label>
                    <input type="text" wire:model.defer="no_hp"
                           class="w-full bg-paper border border-line-200 focus:bg-white focus:border-navy-mid focus:ring-2 focus:ring-navy-mid/20 rounded-xl text-sm p-3 outline-none transition-all">
                    @error('no_hp') <span class="text-xs text-danger-coral mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Alamat</label>
                    <textarea wire:model.defer="alamat" rows="2"
                              class="w-full bg-paper border border-line-200 focus:bg-white focus:border-navy-mid focus:ring-2 focus:ring-navy-mid/20 rounded-xl text-sm p-3 outline-none transition-all resize-none"></textarea>
                    @error('alamat') <span class="text-xs text-danger-coral mt-1 block">{{ $message }}</span> @enderror
                </div>
                <button type="submit"
                        class="w-full bg-navy-deep text-white font-bold py-3.5 rounded-xl hover:bg-navy-mid transition-colors active:scale-95 text-sm">
                    Update Profil
                </button>
            </form>
        </div>
    </div>

    {{-- ── Modal: Ganti Password ── --}}
    <div x-show="modalPassword" style="display: none;" class="fixed inset-0 z-[70] flex items-end justify-center bg-black/40 backdrop-blur-sm" @click="modalPassword = false" x-transition.opacity>
        <div class="bg-white rounded-t-3xl w-full max-w-md p-6 shadow-2xl" @click.stop 
             x-show="modalPassword" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="translate-y-full" 
             x-transition:enter-end="translate-y-0" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="translate-y-0" 
             x-transition:leave-end="translate-y-full">
            <div class="w-10 h-1 bg-line-200 rounded-full mx-auto mb-6"></div>
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-ink-900 text-lg">Ganti Password</h3>
                <button type="button" @click="modalPassword = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form wire:submit.prevent="updatePassword" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Password Lama</label>
                    <input type="password" wire:model.defer="password_lama"
                           class="w-full bg-paper border border-line-200 focus:bg-white focus:border-navy-mid focus:ring-2 focus:ring-navy-mid/20 rounded-xl text-sm p-3 outline-none transition-all">
                    @error('password_lama') <span class="text-xs text-danger-coral mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Password Baru</label>
                    <input type="password" wire:model.defer="password_baru"
                           class="w-full bg-paper border border-line-200 focus:bg-white focus:border-navy-mid focus:ring-2 focus:ring-navy-mid/20 rounded-xl text-sm p-3 outline-none transition-all">
                    @error('password_baru') <span class="text-xs text-danger-coral mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" wire:model.defer="password_baru_confirmation"
                           class="w-full bg-paper border border-line-200 focus:bg-white focus:border-navy-mid focus:ring-2 focus:ring-navy-mid/20 rounded-xl text-sm p-3 outline-none transition-all">
                </div>
                <button type="submit"
                        class="w-full bg-danger-coral text-white font-bold py-3.5 rounded-xl hover:opacity-90 transition-opacity active:scale-95 text-sm">
                    Ganti Password
                </button>
            </form>
        </div>
    </div>

</div>
