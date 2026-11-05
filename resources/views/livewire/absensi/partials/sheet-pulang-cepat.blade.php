{{--
    Bottom sheet alasan pulang lebih awal (sebelum kamera dibuka).
    Mobile: sheet dari bawah · ≥sm: dialog di tengah · landscape pendek: dapat di-scroll.
    Variabel: $cekWaktu, $urlBatal. Mengikat ke properti Livewire `keterangan`.
--}}
@php
    $pilihan = ['Dinas luar', 'Sakit', 'Urusan keluarga', 'Kegiatan sekolah di luar'];
@endphp

<div x-data="{
        open: true,
        alasan: $wire.entangle('keterangan'),
        min: 5,
        get valid() { return (this.alasan ?? '').trim().length >= this.min },
        pilih(teks) {
            this.alasan = teks + ' — ';
            this.$nextTick(() => { const el = this.$refs.alasan; el.focus(); el.setSelectionRange(el.value.length, el.value.length) });
        },
     }"
     x-show="open" x-transition.opacity
     class="absolute inset-0 z-70 flex items-end justify-center bg-navy-950/75 backdrop-blur-[2px] sm:items-center sm:p-6 font-body">

    <div role="dialog" aria-modal="true" aria-labelledby="judul-pulang-cepat"
         x-show="open"
         x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-y-full sm:translate-y-4 sm:opacity-0"
         class="flex max-h-[92dvh] w-full max-w-lg flex-col overflow-hidden rounded-t-[1.75rem] bg-ivory-50 text-navy-900 shadow-sheet sm:rounded-[1.75rem]">

        {{-- Handle (mobile) --}}
        <div class="flex justify-center pt-3 sm:hidden" aria-hidden="true">
            <span class="h-1.5 w-10 rounded-full bg-navy-200"></span>
        </div>

        <div class="overflow-y-auto overscroll-contain px-5 pt-4 pb-2 sm:px-7 sm:pt-7">
            <div class="flex items-start gap-3.5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-warn-50 text-warn-500 ring-1 ring-warn-400/30">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div class="min-w-0">
                    <h2 id="judul-pulang-cepat" class="font-heading text-lg font-semibold leading-snug">Pulang lebih awal?</h2>
                    <p class="mt-1 text-sm leading-relaxed text-navy-500">{{ $cekWaktu['pesan'] }}</p>
                </div>
            </div>

            <fieldset class="mt-5">
                <legend class="text-xs font-semibold text-navy-600">Pilih cepat</legend>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($pilihan as $p)
                        <button type="button" @click="pilih(@js($p))"
                                :class="(alasan ?? '').startsWith(@js($p)) ? 'bg-navy-800 text-white ring-navy-800' : 'bg-white text-navy-700 ring-ivory-400 hover:ring-navy-300'"
                                class="min-h-9 rounded-full px-3.5 text-[13px] font-medium ring-1 transition active:scale-95 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold-500">
                            {{ $p }}
                        </button>
                    @endforeach
                </div>
            </fieldset>

            <label for="alasan-pulang" class="mt-5 block text-xs font-semibold text-navy-600">Alasan</label>
            <textarea id="alasan-pulang" x-ref="alasan" x-model="alasan" rows="3" maxlength="255"
                      placeholder="Tulis alasan singkat…"
                      class="mt-2 w-full resize-none rounded-2xl border-0 bg-white px-4 py-3 text-[15px] leading-relaxed text-navy-900 ring-1 ring-ivory-400 placeholder:text-navy-300 focus:ring-2 focus:ring-gold-500"></textarea>
            <div class="mt-1.5 flex items-center justify-between text-[12px]">
                <span class="text-navy-400">Dilihat Admin TU &amp; Kepsek</span>
                <span class="tabular-nums" :class="valid ? 'text-ok-500' : 'text-navy-400'"
                      x-text="valid ? '✓ Cukup' : `${Math.max(0, min - (alasan ?? '').trim().length)} karakter lagi`"></span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2.5 border-t border-ivory-300 bg-ivory-50 px-5 pt-4 pb-safe sm:px-7 sm:pb-7">
            <a href="{{ $urlBatal }}"
               class="flex h-12 items-center justify-center rounded-2xl bg-ivory-200 text-sm font-semibold text-navy-700 transition hover:bg-ivory-300 active:scale-[0.98]">
                Batal
            </a>
            <button type="button" @click="if (valid) open = false" :disabled="! valid"
                    class="flex h-12 items-center justify-center gap-2 rounded-2xl bg-navy-800 text-sm font-semibold text-white transition hover:bg-navy-700 active:scale-[0.98] disabled:cursor-not-allowed disabled:bg-navy-100 disabled:text-navy-400">
                Lanjut foto
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</div>
