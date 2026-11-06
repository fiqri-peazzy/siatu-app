@php
    $select = 'h-10 w-full rounded-xl border-0 bg-white px-3 text-[14px] font-semibold text-navy-900 ring-1 ring-ivory-300 focus:ring-2 focus:ring-navy-600';
    $rombelAsal = $asal === 'tanpa' ? null : $this->rombel->firstWhere('id', (int) $asal);
    $rombelTujuan = $this->rombel->firstWhere('id', (int) $tujuan);
@endphp

{{-- Pilihan siswa dikelola di browser (bukan wire:model.live) agar klik cepat tidak saling menimpa; dikirim saat Pindahkan --}}
<div class="space-y-5 pb-28 font-body lg:pb-0"
     x-data="{
        dipilih: $wire.entangle('dipilih'),
        pilihSemua() { this.dipilih = [...this.$root.querySelectorAll('[data-siswa-asal]')].map((c) => c.value) },
        pindahkan() {
            const tujuan = $wire.tujuan && document.getElementById('tujuan')?.selectedOptions[0]?.text;
            if (! tujuan || ! this.dipilih.length) return;
            if (confirm(`Pindahkan ${this.dipilih.length} siswa ke ${tujuan}?`)) $wire.pindahkan();
        },
     }">
    <x-slot name="header">Pindah Rombel</x-slot>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-heading text-2xl font-semibold text-navy-900">Pindah rombel massal</h1>
            <p class="mt-1 text-[13px] text-navy-500">Pilih rombel asal, centang siswa, lalu pilih rombel tujuan. Riwayat absensi & jurnal siswa tetap tersimpan.</p>
        </div>
        <a href="{{ route('master.siswa') }}" class="text-[13px] font-semibold text-navy-500 hover:text-navy-900">‹ Kembali ke data siswa</a>
    </div>

    {{-- Peta rombel --}}
    <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:flex-wrap sm:px-0" role="list" aria-label="Rombel tahun ajaran aktif">
        @foreach ($this->rombel as $k)
            <button type="button" role="listitem" wire:click="$set('asal', '{{ $k->id }}')" wire:key="peta-{{ $k->id }}" @class([
                'flex shrink-0 items-center gap-2 rounded-xl px-3 py-2 text-left ring-1 transition',
                'bg-navy-900 text-white ring-navy-900' => (string) $k->id === $asal,
                'bg-white text-navy-800 ring-ivory-300 hover:bg-ivory-100' => (string) $k->id !== $asal,
            ])>
                <span class="text-[13px] font-semibold">{{ $k->nama }}</span>
                <span @class([
                    'rounded-full px-2 py-0.5 text-[11px] font-bold tabular-nums',
                    'bg-white/15 text-white' => (string) $k->id === $asal,
                    'bg-warn-50 text-warn-500' => (string) $k->id !== $asal && $k->siswa_count === 0,
                    'bg-ivory-200 text-navy-600' => (string) $k->id !== $asal && $k->siswa_count > 0,
                ])>{{ $k->siswa_count ?: 'kosong' }}</span>
            </button>
        @endforeach
        @if ($this->tanpaRombel)
            <button type="button" wire:click="$set('asal', 'tanpa')" @class([
                'flex shrink-0 items-center gap-2 rounded-xl px-3 py-2 ring-1',
                'bg-navy-900 text-white ring-navy-900' => $asal === 'tanpa',
                'bg-white text-bad-500 ring-bad-400/40 hover:bg-bad-50' => $asal !== 'tanpa',
            ])>
                <span class="text-[13px] font-semibold">Belum punya rombel</span>
                <span class="rounded-full bg-bad-50 px-2 py-0.5 text-[11px] font-bold text-bad-500">{{ $this->tanpaRombel }}</span>
            </button>
        @endif
    </div>

    <div class="grid gap-5 lg:grid-cols-[1fr_auto_1fr] lg:items-start">
        {{-- ASAL --}}
        <x-ui.panel judul="Rombel asal" :keterangan="$rombelAsal ? $rombelAsal->siswa_count.' siswa aktif · wali: '.($rombelAsal->waliKelas?->nama ?? 'belum diatur') : ($asal === 'tanpa' ? 'Siswa aktif tanpa rombel di tahun ajaran aktif' : 'Pilih rombel di atas')">
            <x-slot:aksi>
                @if ($this->siswaAsal->isNotEmpty())
                    <button type="button" x-show="dipilih.length" x-cloak @click="dipilih = []" class="text-[12px] font-semibold text-navy-500 hover:text-navy-900">Batal pilih</button>
                    <button type="button" @click="pilihSemua()" class="text-[12px] font-semibold text-navy-600 hover:text-navy-900">Pilih semua ({{ $this->siswaAsal->count() }})</button>
                @endif
            </x-slot:aksi>

            <div class="relative mb-3">
                <input type="search" wire:model.live.debounce.250ms="cari" placeholder="Cari nama / NIS…" aria-label="Cari siswa di rombel asal"
                       class="h-10 w-full rounded-xl border-0 bg-ivory-100 px-3 text-[14px] text-navy-900 ring-1 ring-ivory-300 placeholder:text-navy-300 focus:ring-2 focus:ring-navy-600">
            </div>

            @if ($this->siswaAsal->isEmpty())
                <p class="py-10 text-center text-[13px] text-navy-400">{{ $asal === '' ? 'Pilih rombel asal.' : ($cari ? 'Tidak ada siswa yang cocok.' : 'Rombel ini belum berisi siswa.') }}</p>
            @else
                <ul class="divide-y divide-ivory-300 rounded-xl ring-1 ring-ivory-300 lg:max-h-[28rem] lg:overflow-y-auto">
                    @foreach ($this->siswaAsal as $s)
                        <li wire:key="s-{{ $s->id }}">
                            <label class="flex cursor-pointer items-center gap-3 px-3 py-2.5 hover:bg-ivory-100 has-[:checked]:bg-gold-50">
                                <input type="checkbox" value="{{ $s->id }}" x-model="dipilih" data-siswa-asal class="h-4 w-4 rounded border-ivory-300 text-navy-900 focus:ring-navy-600">
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-[14px] font-semibold text-navy-900">{{ $s->nama }}</span>
                                    <span class="text-[12px] text-navy-400">{{ $s->nis ?: $s->nisn ?: '—' }}{{ $s->jk ? ' · '.$s->jk : '' }}</span>
                                </span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.panel>

        {{-- AKSI --}}
        {{-- Mobile: bar tetap di bawah layar; desktop: kolom tengah lengket --}}
        <div class="fixed inset-x-0 bottom-0 z-30 grid grid-cols-[1fr_auto] items-end gap-2 border-t border-ivory-300 bg-white/95 px-4 pt-3 pb-safe shadow-[0_-8px_24px_rgb(14_42_85/0.08)] backdrop-blur lg:sticky lg:inset-auto lg:top-24 lg:z-auto lg:flex lg:w-56 lg:flex-col lg:items-stretch lg:gap-3 lg:border-0 lg:bg-transparent lg:p-0 lg:pt-14 lg:shadow-none lg:backdrop-blur-none">
            <div>
                <label for="tujuan" class="mb-1 block text-[11px] lg:mb-1.5 lg:text-[12px] font-semibold text-navy-600">Pindahkan ke</label>
                <select id="tujuan" wire:model.live="tujuan" class="{{ $select }}">
                    <option value="">— Pilih rombel tujuan —</option>
                    @foreach ($this->rombel as $k)
                        @if ((string) $k->id !== $asal)
                            <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->siswa_count }})</option>
                        @endif
                    @endforeach
                </select>
                @error('tujuan') <p class="mt-1 text-[12px] text-bad-500">{{ $message }}</p> @enderror
                @error('dipilih') <p class="mt-1 text-[12px] text-bad-500">{{ $message }}</p> @enderror
            </div>
            <button type="button" @click="pindahkan()"
                    :disabled="! dipilih.length || ! $wire.tujuan"
                    wire:loading.attr="disabled" wire:target="pindahkan"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-navy-900 px-4 text-[13px] lg:h-11 lg:text-[14px] font-semibold text-white hover:bg-navy-800 disabled:cursor-not-allowed disabled:opacity-40">
                <span x-text="`Pindahkan ${dipilih.length || ''} siswa`">Pindahkan siswa</span>
                <svg class="hidden h-4 w-4 sm:block" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </button>
            <p class="hidden text-center text-[12px] text-navy-400 lg:block"><span x-text="dipilih.length">0</span> dipilih</p>
        </div>

        {{-- TUJUAN --}}
        <x-ui.panel judul="Rombel tujuan" :keterangan="$rombelTujuan ? $rombelTujuan->siswa_count.' siswa saat ini · wali: '.($rombelTujuan->waliKelas?->nama ?? 'belum diatur') : 'Belum dipilih'">
            @if (! $rombelTujuan)
                <p class="py-10 text-center text-[13px] text-navy-400">Pilih rombel tujuan untuk melihat anggotanya.</p>
            @elseif ($this->siswaTujuan->isEmpty())
                <div class="py-10 text-center">
                    <p class="text-[14px] font-semibold text-navy-800">{{ $rombelTujuan->nama }} masih kosong</p>
                    <p class="mt-0.5 text-[12px] text-navy-400">Siswa yang dipindahkan akan muncul di sini.</p>
                </div>
            @else
                <ul class="divide-y divide-ivory-300 rounded-xl ring-1 ring-ivory-300 lg:max-h-[28rem] lg:overflow-y-auto">
                    @foreach ($this->siswaTujuan as $s)
                        <li class="px-3 py-2.5" wire:key="t-{{ $s->id }}">
                            <span class="block truncate text-[14px] font-semibold text-navy-900">{{ $s->nama }}</span>
                            <span class="text-[12px] text-navy-400">{{ $s->nis ?: '—' }}{{ $s->jk ? ' · '.$s->jk : '' }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.panel>
    </div>
</div>
