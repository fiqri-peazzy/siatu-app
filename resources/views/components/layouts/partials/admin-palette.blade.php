{{-- Command palette (Ctrl/Cmd + K): lompat ke halaman mana pun sesuai hak akses --}}
@php
    $u = auth()->user();
    $perintah = collect(require resource_path('views/components/layouts/partials/admin-menu-data.php'))
        ->flatMap(fn ($items, $grup) => collect($items)
            ->filter(fn ($i) => $i[1] && Route::has($i[1]) && (! $i[4] || $u?->hasAnyRole($i[4])))
            ->map(fn ($i) => ['label' => $i[0], 'grup' => $grup ?: 'Umum', 'url' => route($i[1]), 'ikon' => $i[3]]))
        ->values();
@endphp

<div x-data="{
        open: false, q: '', aktif: 0,
        semua: @js($perintah),
        get hasil() {
            const q = this.q.toLowerCase().trim();
            return q ? this.semua.filter((p) => (p.label + ' ' + p.grup).toLowerCase().includes(q)) : this.semua;
        },
        buka() { this.open = true; this.q = ''; this.aktif = 0; this.$nextTick(() => this.$refs.cari.focus()) },
        pilih(p) { if (p) window.location.href = p.url },
     }"
     @keydown.window.prevent.ctrl.k="buka()" @keydown.window.prevent.meta.k="buka()" @palette:buka.window="buka()">

    <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-[70] flex items-start justify-center bg-navy-950/50 p-4 pt-[12vh] backdrop-blur-[2px]"
         @click.self="open = false" @keydown.escape="open = false">
        <div role="dialog" aria-modal="true" aria-label="Cari halaman" class="w-full max-w-lg overflow-hidden rounded-2xl bg-ivory-50 font-body shadow-float ring-1 ring-ivory-300">
            <div class="flex items-center gap-3 border-b border-ivory-300 px-4">
                <svg class="h-5 w-5 shrink-0 text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                <input x-ref="cari" x-model="q" @input="aktif = 0" type="search" placeholder="Cari halaman atau fitur…" aria-label="Cari"
                       @keydown.arrow-down.prevent="aktif = Math.min(aktif + 1, hasil.length - 1)"
                       @keydown.arrow-up.prevent="aktif = Math.max(aktif - 1, 0)"
                       @keydown.enter.prevent="pilih(hasil[aktif])"
                       class="h-14 flex-1 border-0 bg-transparent text-[15px] text-navy-900 placeholder:text-navy-300 focus:ring-0">
                <kbd class="hidden rounded-md bg-ivory-200 px-1.5 py-0.5 text-[11px] font-semibold text-navy-500 sm:block">Esc</kbd>
            </div>
            <ul class="max-h-[50vh] overflow-y-auto p-2" role="listbox">
                <template x-for="(p, i) in hasil" :key="p.url">
                    <li role="option" :aria-selected="i === aktif">
                        <a :href="p.url" @mouseenter="aktif = i"
                           :class="i === aktif ? 'bg-navy-900 text-white' : 'text-navy-800'"
                           class="flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-[14px]">
                            <span class="font-semibold" x-text="p.label"></span>
                            <span class="text-[12px]" :class="i === aktif ? 'text-white/60' : 'text-navy-400'" x-text="p.grup"></span>
                        </a>
                    </li>
                </template>
                <li x-show="! hasil.length" class="px-3 py-6 text-center text-[13px] text-navy-400">Tidak ada hasil untuk “<span x-text="q"></span>”</li>
            </ul>
            <div class="flex items-center gap-4 border-t border-ivory-300 px-4 py-2 text-[11px] text-navy-400">
                <span><kbd class="font-semibold">↑↓</kbd> pilih</span><span><kbd class="font-semibold">Enter</kbd> buka</span><span><kbd class="font-semibold">Ctrl/⌘ K</kbd> buka cepat</span>
            </div>
        </div>
    </div>
</div>
