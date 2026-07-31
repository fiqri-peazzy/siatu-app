<div>
    <x-slot name="header">
        Riwayat Absensi
    </x-slot>

    <!-- Header Section -->
    <div class="mb-6 flex flex-col gap-2 relative z-10">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-800">Riwayat Kehadiran</h2>
        <p class="text-sm font-medium text-slate-500">Pantau catatan waktu absensi Anda di sini.</p>
    </div>

    <!-- Filters -->
    <div class="bg-white/80 backdrop-blur-md rounded-2xl p-4 shadow-sm border border-slate-100 mb-6 flex flex-row gap-3 relative z-10">
        <select wire:model.live="filterBulan" class="bg-slate-50 border-0 text-slate-700 text-sm font-bold rounded-xl focus:ring-2 focus:ring-indigo-500/50 block w-full p-3 shadow-inner">
            @foreach(range(1, 12) as $m)
                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterTahun" class="bg-slate-50 border-0 text-slate-700 text-sm font-bold rounded-xl focus:ring-2 focus:ring-indigo-500/50 block w-full p-3 shadow-inner">
            @foreach(range(date('Y')-2, date('Y')) as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
    </div>

    <!-- Data List (Card Based) -->
    <div class="space-y-4 relative z-10">
        @forelse($riwayat as $date => $data)
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col gap-4 relative">
                <!-- Date Banner -->
                <div class="flex items-center gap-2 border-b border-slate-50 pb-3">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="text-sm font-semibold text-slate-700">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</span>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <!-- Check In -->
                    <div class="flex flex-col">
                        <p class="text-xs font-semibold text-slate-500 mb-1">Masuk</p>
                        @if($data['masuk'])
                            <h4 class="text-2xl font-bold text-slate-800">{{ \Carbon\Carbon::parse($data['masuk']->waktu_absen)->format('H:i') }}</h4>
                            <div class="mt-1 flex items-center justify-between">
                                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-md {{ $data['masuk']->status === 'hadir' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ ucfirst($data['masuk']->status) }}
                                </span>
                                @if($data['masuk']->foto_path)
                                    <a href="{{ Storage::disk('r2')->url($data['masuk']->foto_path) }}" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-500 hover:bg-indigo-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </a>
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-slate-400 font-medium">Belum absen</p>
                        @endif
                    </div>
                    
                    <!-- Check Out -->
                    <div class="flex flex-col border-l border-slate-50 pl-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1">Pulang</p>
                        @if($data['pulang'])
                            <h4 class="text-2xl font-bold text-slate-800">{{ \Carbon\Carbon::parse($data['pulang']->waktu_absen)->format('H:i') }}</h4>
                            <div class="mt-1 flex items-center justify-between">
                                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-md {{ $data['pulang']->status === 'hadir' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ ucfirst($data['pulang']->status) }}
                                </span>
                                @if($data['pulang']->foto_path)
                                    <a href="{{ Storage::disk('r2')->url($data['pulang']->foto_path) }}" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-500 hover:bg-indigo-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </a>
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-slate-400 font-medium">Belum absen</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="font-bold text-slate-800 text-lg mb-1">Belum Ada Catatan</h4>
                <p class="text-sm text-slate-500 font-medium">Data absensi belum tersedia untuk bulan ini.</p>
            </div>
        @endforelse
    </div>
</div>
