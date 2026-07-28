<div>
    <svg class="w-6 h-6 mb-1 {{ request()->routeIs('guru.dashboard') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-400 transition-colors' }}" fill="{{ request()->routeIs('guru.dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
    </svg>
    @if($unfilledCount > 0)
        <div class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 rounded-full flex items-center justify-center border-2 border-white text-[9px] font-bold text-white shadow-sm">
            {{ $unfilledCount }}
        </div>
    @endif
</div>
