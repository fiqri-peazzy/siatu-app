<div class="space-y-6 animate-fade-in pb-10">
    <!-- Header -->
    <div class="mt-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Profil Saya</h2>
        <a href="{{ route('guru.pengaturan-notifikasi') }}" class="w-10 h-10 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </a>
    </div>

    <!-- Profil Card -->
    <div class="bg-indigo-600 rounded-3xl p-5 text-white shadow-lg shadow-indigo-200 relative overflow-hidden flex items-center gap-4">
        <div class="absolute -right-10 -bottom-10 opacity-10">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"></path></svg>
        </div>
        <div class="w-16 h-16 rounded-full bg-indigo-200 border-2 border-white flex items-center justify-center overflow-hidden shrink-0 z-10">
            @if($guru->foto)
                <img src="{{ asset('storage/' . $guru->foto) }}" class="w-full h-full object-cover">
            @else
                <svg class="w-8 h-8 text-indigo-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
            @endif
        </div>
        <div class="z-10">
            <h3 class="font-bold text-lg leading-tight">{{ $guru->nama_guru }}</h3>
            <p class="text-indigo-200 text-xs mt-0.5">NIP: {{ $guru->nip ?? '-' }}</p>
            <span class="inline-block px-2 py-0.5 bg-indigo-500/50 rounded text-[10px] uppercase font-bold tracking-wider mt-2 border border-indigo-400/30">GURU</span>
        </div>
    </div>

    <!-- Kepatuhan Jurnal -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h4 class="font-bold text-slate-800">Kepatuhan Jurnal</h4>
                <p class="text-xs text-slate-400">Bulan {{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM YYYY') }}</p>
            </div>
            <div class="text-right">
                <span class="text-2xl font-black {{ $persentase >= 80 ? 'text-emerald-500' : ($persentase >= 50 ? 'text-amber-500' : 'text-rose-500') }}">
                    {{ $persentase }}%
                </span>
            </div>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
            <div class="{{ $persentase >= 80 ? 'bg-emerald-500' : ($persentase >= 50 ? 'bg-amber-500' : 'bg-rose-500') }} h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $persentase }}%"></div>
        </div>
        <p class="text-[10px] text-slate-400 mt-2 italic text-center">Persentase ini hanya untuk pengingat pribadi Anda.</p>
    </div>

    <!-- Edit Profil -->
    <form wire:submit.prevent="updateProfil" class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 space-y-4">
        <h4 class="font-bold text-slate-800 mb-2">Data Pribadi</h4>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">No. HP / WhatsApp</label>
            <input type="text" wire:model.defer="no_hp" class="w-full bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-2xl text-sm transition-all p-3">
            @error('no_hp') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat</label>
            <textarea wire:model.defer="alamat" rows="2" class="w-full bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-2xl text-sm transition-all p-3"></textarea>
            @error('alamat') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="w-full bg-slate-800 text-white font-bold py-3 rounded-2xl hover:bg-slate-900 transition-all text-sm">Update Profil</button>
    </form>

    <!-- Ubah Password -->
    <form wire:submit.prevent="updatePassword" class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 space-y-4">
        <h4 class="font-bold text-slate-800 mb-2">Ubah Password</h4>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Password Lama</label>
            <input type="password" wire:model.defer="password_lama" class="w-full bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-2xl text-sm transition-all p-3">
            @error('password_lama') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Password Baru</label>
            <input type="password" wire:model.defer="password_baru" class="w-full bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-2xl text-sm transition-all p-3">
            @error('password_baru') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
            <input type="password" wire:model.defer="password_baru_confirmation" class="w-full bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-2xl text-sm transition-all p-3">
        </div>
        <button type="submit" class="w-full bg-rose-50 text-rose-600 font-bold py-3 rounded-2xl hover:bg-rose-600 hover:text-white transition-all text-sm">Ganti Password</button>
    </form>
</div>
