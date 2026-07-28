<div wire:poll.60s="refreshComponent" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Supervisi Jurnal Mengajar</h2>
            <p class="text-slate-500 text-sm mt-1">Pemantauan kegiatan belajar mengajar secara real-time.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl font-bold text-sm">
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} - <span id="clock">{{ \Carbon\Carbon::now()->format('H:i') }}</span>
            </span>
            <a href="{{ route('supervisi.jurnal.export', ['bulan' => date('m'), 'tahun' => date('Y')]) }}" target="_blank" class="px-4 py-2 bg-emerald-500 text-white rounded-xl font-bold text-sm hover:bg-emerald-600 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Ringkasan Status -->
    @php
        $totalAktif = collect($jadwalAktif)->where('status_kbm', 'aktif')->count();
        $totalPerluPerhatian = collect($jadwalAktif)->where('perlu_perhatian', true)->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">KBM Sedang Berjalan</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $totalAktif }} Kelas</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 animate-pulse">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Perlu Perhatian</p>
                <h3 class="text-2xl font-bold text-rose-600">{{ $totalPerluPerhatian }} Kelas</h3>
                <p class="text-[10px] text-slate-400">Jurnal kosong > {{ $ambangWaktu }} menit</p>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-4">Waktu</th>
                        <th class="px-5 py-4">Guru</th>
                        <th class="px-5 py-4">Kelas & Mapel</th>
                        <th class="px-5 py-4">Status KBM</th>
                        <th class="px-5 py-4 text-center">Jurnal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jadwalAktif as $jadwal)
                        <tr class="transition-colors hover:bg-slate-50/50 {{ $jadwal['perlu_perhatian'] ? 'bg-rose-50/50' : '' }}">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-bold text-slate-800 block">Jam ke-{{ $jadwal['jam_ke'] }}</span>
                                <span class="text-xs text-slate-500">{{ $jadwal['jam_mulai'] }} - {{ $jadwal['jam_selesai'] }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-800">{{ $jadwal['guru'] }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-semibold text-slate-800 block">{{ $jadwal['kelas'] }}</span>
                                <span class="text-xs text-slate-500">{{ $jadwal['mapel'] }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @if($jadwal['status_kbm'] === 'aktif')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Sedang Berjalan
                                    </span>
                                @elseif($jadwal['status_kbm'] === 'selesai')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600">
                                        Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($jadwal['is_filled'])
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600" title="Sudah Diisi">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </span>
                                @else
                                    @if($jadwal['perlu_perhatian'])
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200" title="Belum Diisi">
                                            Perlu Perhatian!
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-400" title="Belum Diisi">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-slate-500">
                                Tidak ada jadwal mengajar pada hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Simple client-side clock update
        setInterval(function() {
            var date = new Date();
            var hours = String(date.getHours()).padStart(2, '0');
            var minutes = String(date.getMinutes()).padStart(2, '0');
            var clockEl = document.getElementById('clock');
            if (clockEl) {
                clockEl.innerHTML = hours + ':' + minutes;
            }
        }, 10000);
    </script>
</div>
