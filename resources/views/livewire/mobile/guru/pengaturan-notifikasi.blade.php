<div class="space-y-6 animate-fade-in pb-10">
    <div class="mt-4 flex items-center gap-3">
        <a href="{{ route('guru.profil') }}" class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-slate-500 shadow-sm border border-slate-100 hover:text-indigo-600 hover:border-indigo-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Notifikasi</h2>
    </div>

    <form wire:submit.prevent="simpan" class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <h4 class="font-bold text-slate-800">Reminder Jurnal</h4>
                <p class="text-xs text-slate-500 mt-0.5">Ingatkan jam 14:00 jika ada jurnal yang belum diisi.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="reminder_jurnal_aktif" class="sr-only peer">
                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
        </div>

        @if($reminder_jurnal_aktif)
            <div class="animate-fade-in border-t border-slate-100 pt-4">
                <h4 class="font-bold text-slate-800 mb-3">Kanal Notifikasi</h4>
                
                <div class="space-y-3">
                    <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition-colors {{ $kanal_notifikasi === 'wa_saja' ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 hover:bg-slate-50' }}">
                        <input type="radio" wire:model="kanal_notifikasi" value="wa_saja" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <div class="ml-3">
                            <span class="block text-sm font-semibold text-slate-800">WhatsApp Saja</span>
                            <span class="block text-xs text-slate-500">Kirim pesan WhatsApp otomatis</span>
                        </div>
                    </label>

                    <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition-colors {{ $kanal_notifikasi === 'wa_dan_inapp' ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 hover:bg-slate-50' }}">
                        <input type="radio" wire:model="kanal_notifikasi" value="wa_dan_inapp" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <div class="ml-3">
                            <span class="block text-sm font-semibold text-slate-800">WhatsApp + In-App</span>
                            <span class="block text-xs text-slate-500">Notifikasi WA & aplikasi web</span>
                        </div>
                    </label>
                </div>
            </div>
        @endif

        <div class="pt-2">
            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all text-sm">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
