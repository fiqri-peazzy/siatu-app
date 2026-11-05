{{--
    Kartu info saat absen tendik di luar jam / perlu alasan pulang cepat.
    Variabel: $cekWaktu, $jendela. Fokus ke textarea #keterangan-tendik.
--}}
@if (! ($cekWaktu['boleh'] ?? true))
    @php $perluAlasan = $cekWaktu['butuh_alasan'] ?? false; @endphp
    <div role="status" class="mb-6 overflow-hidden rounded-3xl bg-ivory-50 font-body ring-1 ring-ivory-400">
        <div class="flex gap-3 p-4 sm:p-5 {{ $perluAlasan ? 'bg-warn-50' : 'bg-bad-50' }}">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $perluAlasan ? 'bg-white text-warn-500 ring-1 ring-warn-400/30' : 'bg-white text-bad-500 ring-1 ring-bad-400/30' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <div class="min-w-0">
                <p class="font-heading text-[15px] font-semibold leading-snug text-navy-900">
                    {{ $perluAlasan ? 'Pulang lebih awal' : 'Di luar jam absen' }}
                </p>
                <p class="mt-0.5 text-sm leading-relaxed text-navy-600">{{ $cekWaktu['pesan'] }}</p>
                @if ($perluAlasan)
                    <button type="button" x-data @click="document.getElementById('keterangan-tendik')?.focus()"
                            class="mt-2.5 inline-flex min-h-9 items-center gap-1.5 rounded-full bg-navy-800 px-3.5 text-[13px] font-semibold text-white active:scale-95">
                        Tulis alasan
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                @endif
            </div>
        </div>
        @if ($jendela['hari_kerja'] ?? false)
            <details class="group border-t border-ivory-300">
                <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3 text-[13px] font-semibold text-navy-700 sm:px-5">
                    Lihat jam absen hari ini
                    <svg class="h-4 w-4 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-4 pb-4 sm:px-5">
                    <x-absensi.jendela-waktu :jendela="$jendela" :sekarang="now()->format('H:i')" tone="light" />
                </div>
            </details>
        @endif
    </div>
@endif
