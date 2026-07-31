<div class="space-y-6 animate-fade-in pb-10">
    <div class="mt-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('guru.dashboard') }}" class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-slate-500 shadow-sm border border-slate-100 hover:text-indigo-600 hover:border-indigo-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-2xl font-bold text-slate-800">Notifikasi</h2>
        </div>
        <a href="{{ route('guru.pengaturan-notifikasi') }}" class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center text-slate-500 shadow-sm border border-slate-100 hover:text-indigo-600 hover:border-indigo-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </a>
    </div>

    @if(count($notifications) > 0)
        <div class="space-y-4">
            @foreach($notifications as $notif)
                <div class="bg-white rounded-3xl p-5 shadow-sm border {{ is_null($notif->read_at) ? 'border-indigo-100 bg-indigo-50/30' : 'border-slate-100' }} flex gap-4 transition-all">
                    <div class="flex-shrink-0 w-12 h-12 rounded-2xl {{ is_null($notif->read_at) ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            @if(isset($notif->data['icon']) && $notif->data['icon'] == 'bell')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @endif
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm mb-1">{{ $notif->data['title'] ?? 'Pemberitahuan' }}</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $notif->data['body'] ?? '' }}</p>
                        <span class="block mt-2 text-[10px] font-semibold text-slate-400">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="flex flex-col items-center justify-center text-center py-20 animate-fade-in-up">
            <div class="w-48 h-48 mb-6 relative">
                <div class="absolute inset-0 bg-indigo-50 rounded-full blur-2xl opacity-60"></div>
                <img src="{{ asset('images/mute.png') }}" alt="Tidak ada notifikasi" class="relative z-10 w-full h-full object-contain drop-shadow-md">
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Belum Ada Notifikasi</h3>
            <p class="text-sm text-slate-500 max-w-[250px] mx-auto">Anda belum memiliki notifikasi baru saat ini. Bersantailah sejenak!</p>
            
            <a href="{{ route('guru.dashboard') }}" class="mt-8 bg-white border border-slate-200 text-slate-700 font-bold py-3 px-6 rounded-2xl shadow-sm hover:bg-slate-50 hover:text-indigo-600 transition-all text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Beranda
            </a>
        </div>
    @endif
</div>
