<div class="space-y-6 animate-fade-in pb-10">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6 mt-2">
        <a href="{{ route('guru.dashboard') }}" class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-slate-500 shadow-sm border border-slate-100 hover:text-indigo-600 hover:border-indigo-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">Isi Jurnal</h2>
    </div>

    <!-- Info Jadwal -->
    <div class="bg-indigo-600 rounded-3xl p-5 text-white shadow-lg shadow-indigo-200 relative overflow-hidden">
        <div class="absolute -right-4 -top-4 opacity-10">
            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg>
        </div>
        <div class="relative z-10 flex flex-col h-full justify-between">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-indigo-500/50 text-indigo-100 uppercase tracking-wider border border-indigo-400/30">
                        Jam ke-{{ $jadwal->jam_ke }}
                    </span>
                    <span class="text-xs font-semibold text-indigo-200">
                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                    </span>
                </div>
                <h3 class="font-bold text-xl leading-tight">{{ $jadwal->mapel->nama ?? '-' }}</h3>
                <p class="text-indigo-200 text-sm mt-1">Kelas {{ $jadwal->kelas->nama ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Form Input -->
    <form wire:submit.prevent="submit" class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 space-y-5">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Materi Pembelajaran</label>
            <textarea wire:model.defer="materi" rows="3" class="w-full bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-2xl text-sm transition-all p-3" placeholder="Tuliskan materi yang diajarkan..."></textarea>
            @error('materi') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kegiatan / Catatan</label>
            <textarea wire:model.defer="kegiatan" rows="3" class="w-full bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-2xl text-sm transition-all p-3" placeholder="Catatan kegiatan belajar mengajar..."></textarea>
            @error('kegiatan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
        
        <!-- Dokumentasi (Opsional) - UI Only for now, can be hooked to file upload if needed -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Dokumentasi (Opsional)</label>
            <div class="w-full h-24 border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center text-slate-400 bg-slate-50 hover:bg-slate-100 hover:border-indigo-300 transition-colors cursor-pointer">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="text-xs font-semibold">Ambil Foto</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-1.5 text-center">Fitur foto akan dikembangkan pada fase berikutnya.</p>
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all flex items-center justify-center gap-2 mt-4">
            <span wire:loading.remove wire:target="submit">Simpan Jurnal</span>
            <span wire:loading wire:target="submit">Menyimpan...</span>
            <svg wire:loading.remove wire:target="submit" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
        </button>
    </form>
</div>
