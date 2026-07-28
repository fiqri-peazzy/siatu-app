<div class="space-y-6 pb-12">

    {{-- ── Back Header ── --}}
    <div class="flex items-center gap-3 mt-2">
        <a href="{{ route('guru.dashboard') }}"
           class="w-9 h-9 rounded-full bg-white border border-line-200 shadow-sm flex items-center justify-center text-slate-500 hover:text-navy-deep transition-colors active:scale-95 flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h2 class="text-xl font-bold text-ink-900">Isi Jurnal</h2>
    </div>

    {{-- ── Jadwal Pill ── --}}
    <div class="inline-flex items-center gap-2 bg-white border border-line-200 rounded-full px-4 py-2 shadow-sm">
        <svg class="w-4 h-4 text-signal-blue flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <span class="text-sm font-bold text-ink-900 font-mono">
            {{ $jadwal->kelas->nama ?? '-' }} &middot; {{ $jadwal->mapel->nama ?? '-' }} &middot; 
            {{ \Carbon\Carbon::parse($jadwalList->first()->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalList->last()->jam_selesai)->format('H:i') }}
        </span>
    </div>

    {{-- ── Form ── --}}
    <form wire:submit.prevent="submit" class="space-y-4">

        {{-- Materi Pembelajaran --}}
        <div class="bg-white rounded-2xl border border-line-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-4 pt-4 pb-2">
                <svg class="w-4 h-4 text-signal-blue flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <label class="text-[10px] font-bold text-signal-blue uppercase tracking-[0.1em]">Materi Pembelajaran</label>
            </div>
            <textarea wire:model.defer="materi" rows="3"
                      class="w-full px-4 pb-4 text-sm text-ink-900 bg-transparent outline-none resize-none placeholder:text-slate-400"
                      placeholder="Tuliskan topik atau KD yang dibahas hari ini..."></textarea>
            @error('materi') <p class="px-4 pb-3 text-xs text-danger-coral">{{ $message }}</p> @enderror
        </div>

        {{-- Kegiatan Pembelajaran --}}
        <div class="bg-white rounded-2xl border border-line-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-4 pt-4 pb-2">
                <svg class="w-4 h-4 text-signal-blue flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <label class="text-[10px] font-bold text-signal-blue uppercase tracking-[0.1em]">Kegiatan Pembelajaran</label>
            </div>
            <textarea wire:model.defer="kegiatan" rows="4"
                      class="w-full px-4 pb-4 text-sm text-ink-900 bg-transparent outline-none resize-none placeholder:text-slate-400"
                      placeholder="Deskripsikan metode, aktivitas, dan interaksi di kelas..."></textarea>
            @error('kegiatan') <p class="px-4 pb-3 text-xs text-danger-coral">{{ $message }}</p> @enderror
        </div>

        {{-- Dokumentasi (Opsional) --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em]">Dokumentasi (Opsional)</span>
            </div>
            
            <!-- File Input & Previews -->
            <div class="flex flex-wrap gap-3">
                @if($dokumentasi)
                    @foreach($dokumentasi as $foto)
                        <div class="w-24 h-24 rounded-xl overflow-hidden border border-line-200 relative group">
                            <img src="{{ $foto->temporaryUrl() }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                @endif
                
                <label class="w-24 h-24 border-2 border-dashed border-line-200 rounded-xl flex flex-col items-center justify-center gap-1 text-slate-400 bg-paper hover:border-signal-blue/30 hover:bg-signal-blue/5 transition-colors cursor-pointer relative overflow-hidden">
                    <input type="file" wire:model="dokumentasi" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div wire:loading wire:target="dokumentasi" class="absolute inset-0 bg-white/80 flex items-center justify-center z-20">
                        <svg class="animate-spin w-5 h-5 text-signal-blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="text-[10px] font-semibold text-center leading-tight mt-0.5">Tambah<br>Foto</span>
                </label>
            </div>
            @error('dokumentasi.*') <p class="mt-2 text-xs text-danger-coral">{{ $message }}</p> @enderror
        </div>

        {{-- Submit Button (Inline) --}}
        <div class="pt-4 pb-8">
            <button type="submit"
                    class="w-full bg-signal-blue text-white font-bold py-4 rounded-2xl shadow-lg shadow-signal-blue/25 hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <svg wire:loading.remove wire:target="submit" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                <span wire:loading.remove wire:target="submit">Simpan Jurnal</span>
                <span wire:loading wire:target="submit">Menyimpan...</span>
            </button>
        </div>
    </form>

</div>
