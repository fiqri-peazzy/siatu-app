@php
    $hariIni = $dipilih->isToday();
    $urlJurnal = route('guru.jurnal.form', ['sesi' => '__SESI__', 'tgl' => $dipilih->toDateString()]);
@endphp

<div class="pb-4 font-body"
     x-data="{
        detail: null,
        urlJurnal: @js($urlJurnal),
        hariIni: @js($hariIni),
        // Hari ini (kelas sudah mulai) atau hari yang sudah lewat → boleh isi; hari mendatang → belum
        bisaIsi(d) { return d && d.fase !== 'mendatang' && d.jurnal !== 'tidak_wajib' },
        link(d) { return this.urlJurnal.replace('__SESI__', d.jurnal_sesi_ids.join(',')) },
     }"
     @jadwal:detail.window="detail = $event.detail">

    {{-- Judul + navigasi minggu --}}
    <div class="flex items-end justify-between gap-3">
        <div>
            <p class="text-[12px] font-medium text-navy-400">{{ $dipilih->locale('id')->isoFormat('MMMM YYYY') }}</p>
            <h1 class="font-heading text-[1.35rem] font-semibold leading-tight text-navy-900">{{ $dipilih->locale('id')->isoFormat('dddd, D MMM') }}</h1>
        </div>
        <div class="flex items-center gap-1">
            <button type="button" wire:click="pilihHari('{{ $mingguLalu }}')" aria-label="Minggu sebelumnya"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-navy-600 ring-1 ring-ivory-300 hover:bg-white"><x-ui.icon name="chevron-kanan" class="h-4 w-4 rotate-180" /></button>
            @unless ($hariIni)
                <button type="button" wire:click="pilihHari('{{ now()->toDateString() }}')" class="h-10 rounded-full px-3 text-[13px] font-semibold text-navy-700 ring-1 ring-ivory-300 hover:bg-white">Hari ini</button>
            @endunless
            <button type="button" wire:click="pilihHari('{{ $mingguDepan }}')" aria-label="Minggu berikutnya"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-navy-600 ring-1 ring-ivory-300 hover:bg-white"><x-ui.icon name="chevron-kanan" class="h-4 w-4" /></button>
        </div>
    </div>

    {{-- Strip hari --}}
    <div class="mt-4 grid gap-1.5" style="grid-template-columns: repeat({{ $hari->count() }}, minmax(0, 1fr))" role="tablist" aria-label="Pilih hari">
        @foreach ($hari as $h)
            <button type="button" role="tab" aria-selected="{{ $h['dipilih'] ? 'true' : 'false' }}"
                    wire:click="pilihHari('{{ $h['tanggal'] }}')"
                    @class([
                        'flex flex-col items-center rounded-2xl py-2.5 transition active:scale-95',
                        'bg-navy-900 text-white shadow-float' => $h['dipilih'],
                        'bg-white text-navy-800 ring-1 ring-ivory-300' => ! $h['dipilih'],
                    ])>
                <span class="text-[11px] font-medium {{ $h['dipilih'] ? 'text-white/60' : 'text-navy-400' }}">{{ $h['singkat'] }}</span>
                <span class="font-heading text-lg font-semibold leading-tight">{{ $h['tgl'] }}</span>
                <span class="mt-0.5 h-1.5 w-1.5 rounded-full {{ $h['jam'] > 0 ? ($h['dipilih'] ? 'bg-gold-300' : 'bg-navy-800') : 'bg-transparent' }} {{ $h['hari_ini'] && ! $h['dipilih'] ? 'ring-2 ring-gold-400' : '' }}"></span>
            </button>
        @endforeach
    </div>

    {{-- Ringkasan hari --}}
    <div class="mt-4 flex items-center gap-2 text-[13px]" wire:loading.class="opacity-50">
        @if ($data['jumlah_blok'] > 0)
            <span class="rounded-full bg-white px-3 py-1 font-semibold text-navy-800 ring-1 ring-ivory-300">{{ $data['jumlah_blok'] }} kelas</span>
            @if ($data['belum_jurnal'] > 0)
                <span class="rounded-full bg-warn-50 px-3 py-1 font-semibold text-warn-500">{{ $data['belum_jurnal'] }} jurnal belum diisi</span>
            @elseif ($hariIni)
                <span class="rounded-full bg-ok-50 px-3 py-1 font-semibold text-ok-500">Jurnal aman</span>
            @endif
        @endif
    </div>

    {{-- Timeline --}}
    <div class="mt-3" wire:loading.class="opacity-50">
        @if (! $data['hari_kerja'])
            <div class="rounded-3xl border border-dashed border-ivory-400 bg-ivory-50 px-6 py-12 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-navy-300 ring-1 ring-ivory-300"><x-ui.icon name="kegiatan" /></span>
                <h3 class="mt-3 font-heading text-[15px] font-semibold text-navy-800">Tidak ada jadwal sekolah</h3>
                <p class="mt-1 text-[13px] text-navy-400">Hari ini bukan hari belajar.</p>
            </div>
        @elseif ($data['jumlah_blok'] === 0)
            <div class="mb-3 rounded-2xl bg-white p-4 text-[13px] text-navy-500 ring-1 ring-ivory-300">
                Anda tidak mengajar pada hari ini. Jadwal sekolah tetap ditampilkan di bawah.
            </div>
            <ol class="mt-2">@foreach ($data['items'] as $item)<x-jadwal.item :item="$item" />@endforeach</ol>
        @else
            <ol>@foreach ($data['items'] as $item)<x-jadwal.item :item="$item" />@endforeach</ol>
        @endif
    </div>

    {{-- Detail sesi (bottom sheet) --}}
    <template x-teleport="body">
        <div x-show="detail" x-cloak class="fixed inset-0 z-50 flex items-end justify-center bg-navy-950/60 backdrop-blur-[2px] sm:items-center sm:p-6"
             @keydown.escape.window="detail = null" @click.self="detail = null" x-transition.opacity>
            <div role="dialog" aria-modal="true" class="w-full max-w-md rounded-t-[1.75rem] bg-ivory-50 px-5 pt-3 pb-safe-lg font-body shadow-sheet sm:rounded-[1.75rem] sm:pb-6"
                 x-show="detail" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-y-full">
                <div class="mb-3 flex justify-center sm:hidden"><span class="h-1.5 w-10 rounded-full bg-navy-200"></span></div>
                <template x-if="detail">
                    <div>
                        <p class="text-[12px] font-medium text-navy-400" x-text="`${detail.mulai}–${detail.selesai} · Jam ke-${detail.jam_ke.join(', ')}`"></p>
                        <h2 class="mt-1 font-heading text-xl font-semibold text-navy-900" x-text="detail.mapel"></h2>

                        <dl class="mt-4 divide-y divide-ivory-300 rounded-2xl bg-white ring-1 ring-ivory-300">
                            <div class="flex justify-between gap-4 px-4 py-3 text-[14px]"><dt class="text-navy-500">Rombel</dt><dd class="text-right font-semibold text-navy-900" x-text="detail.kelas.join(', ')"></dd></div>
                            <div class="flex justify-between gap-4 px-4 py-3 text-[14px]"><dt class="text-navy-500">Durasi</dt><dd class="font-semibold text-navy-900" x-text="`${detail.jam_ke.length} JP`"></dd></div>
                            <div class="flex justify-between gap-4 px-4 py-3 text-[14px]" x-show="detail.grup_pilihan"><dt class="text-navy-500">Kelompok pilihan</dt><dd class="font-semibold text-navy-900" x-text="detail.grup_pilihan"></dd></div>
                            <div class="flex justify-between gap-4 px-4 py-3 text-[14px]">
                                <dt class="text-navy-500">Jurnal</dt>
                                <dd class="font-semibold" :class="{ 'text-ok-500': detail.jurnal === 'diisi', 'text-warn-500': ['belum','sebagian'].includes(detail.jurnal), 'text-navy-500': detail.jurnal === 'tidak_wajib' }"
                                    x-text="{ diisi: 'Sudah diisi', sebagian: 'Sebagian', belum: 'Belum diisi', tidak_wajib: 'Tidak wajib (tergeser)' }[detail.jurnal]"></dd>
                            </div>
                        </dl>

                        <p x-show="detail.catatan" class="mt-3 rounded-xl bg-warn-50 px-3 py-2 text-[13px] text-warn-500" x-text="detail.catatan"></p>

                        <div class="mt-5 grid gap-2.5">
                            <a x-show="bisaIsi(detail)" :href="link(detail)" wire:navigate
                               class="flex h-12 items-center justify-center gap-2 rounded-2xl bg-navy-900 text-[15px] font-semibold text-white active:scale-[0.98]">
                                <x-ui.icon name="jurnal" class="h-5 w-5 text-gold-300" />
                                <span x-text="detail.jurnal === 'diisi' ? 'Lihat / ubah jurnal' : 'Isi jurnal & absensi'"></span>
                            </a>
                            <p x-show="! bisaIsi(detail) && detail.jurnal !== 'tidak_wajib'" class="rounded-xl bg-ivory-200 px-3 py-2.5 text-center text-[13px] text-navy-500"
                               x-text="hariIni ? 'Jurnal dapat diisi setelah kelas dimulai.' : 'Jurnal dapat diisi setelah hari mengajar tiba.'"></p>
                            <button type="button" @click="detail = null" class="h-12 rounded-2xl text-[15px] font-semibold text-navy-700 ring-1 ring-ivory-400">Tutup</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>
