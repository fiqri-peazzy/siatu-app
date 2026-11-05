@php
    $statusOpsi = [
        'hadir' => ['H', 'Hadir', 'bg-ok-500 text-white', 'text-ok-500'],
        'izin' => ['I', 'Izin', 'bg-info-500 text-white', 'text-info-500'],
        'sakit' => ['S', 'Sakit', 'bg-warn-500 text-white', 'text-warn-500'],
        'alfa' => ['A', 'Alfa', 'bg-bad-500 text-white', 'text-bad-500'],
    ];
    $siswaPerKelas = collect($siswaList)->groupBy('kelas_id');
    $namaKelas = collect($kelasList)->pluck('nama', 'id');
    $hitung = array_count_values($absensi) + ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0];
    $metode = ['Ceramah', 'Diskusi kelompok', 'Tanya jawab', 'Praktikum', 'Presentasi siswa', 'Latihan soal', 'Penugasan'];
@endphp

<div class="font-body"
     x-data="{
        absensi: $wire.entangle('absensi'),
        cari: '',
        hitung(s) { return Object.values(this.absensi ?? {}).filter((v) => v === s).length },
        semua(s) { Object.keys(this.absensi).forEach((k) => this.absensi[k] = s) },
        cocok(nama) { return ! this.cari || nama.toLowerCase().includes(this.cari.toLowerCase()) },
        tambahMetode(t) {
            const el = this.$refs.kegiatan; const v = el.value.trim();
            el.value = v ? `${v}${v.endsWith('.') ? '' : ','} ${t.toLowerCase()}` : t;
            el.dispatchEvent(new Event('input'));
        },
     }">

    {{-- Kartu sesi --}}
    <section class="rounded-3xl bg-navy-900 p-5 text-white">
        <p class="text-[12px] text-white/55">{{ $info['tanggal'] }}</p>
        <h1 class="mt-1 font-heading text-xl font-semibold leading-snug">{{ $info['mapel'] }}</h1>
        <div class="mt-3 flex flex-wrap gap-1.5">
            @foreach ($kelasList as $k)
                <span class="rounded-full bg-white/10 px-2.5 py-1 text-[12px] font-semibold">{{ $k['nama'] }}</span>
            @endforeach
            <span class="rounded-full bg-gold-400/15 px-2.5 py-1 text-[12px] font-semibold text-gold-300">
                {{ $info['mulai'] }}–{{ $info['selesai'] }} · {{ count($info['jam_ke']) }} JP
            </span>
        </div>
        @if ($modeEdit)
            <p class="mt-3 inline-flex items-center gap-1.5 text-[12px] text-emerald-300"><x-ui.icon name="cek" class="h-3.5 w-3.5" /> Jurnal sudah pernah diisi — perubahan akan menimpa data sebelumnya.</p>
        @endif
    </section>

    {{-- Stepper --}}
    <ol class="mt-4 grid grid-cols-3 gap-2" aria-label="Langkah pengisian">
        @foreach ([1 => 'Kehadiran', 2 => 'Materi', 3 => 'Simpan'] as $n => $label)
            <li>
                <button type="button" wire:click="keLangkah({{ $n }})" @if ($langkah === $n) aria-current="step" @endif
                        class="flex w-full flex-col gap-1.5 text-left">
                    <span class="h-1 rounded-full {{ $langkah >= $n ? 'bg-navy-900' : 'bg-ivory-400' }}"></span>
                    <span class="text-[12px] font-semibold {{ $langkah === $n ? 'text-navy-900' : 'text-navy-400' }}">{{ $n }}. {{ $label }}</span>
                </button>
            </li>
        @endforeach
    </ol>

    {{-- ===== Langkah 1: Kehadiran ===== --}}
    @if ($langkah === 1)
    <section class="mt-5" aria-labelledby="judul-kehadiran">
        <div class="flex items-center justify-between">
            <h2 id="judul-kehadiran" class="font-heading text-[17px] font-semibold text-navy-900">Kehadiran siswa</h2>
            <span class="text-[13px] text-navy-400">{{ count($siswaList) }} siswa</span>
        </div>

        <div class="mt-3 grid grid-cols-4 gap-2">
            @foreach ($statusOpsi as $kode => [$h, $label, , $warna])
                <div class="rounded-2xl bg-white px-2 py-2.5 text-center ring-1 ring-ivory-300">
                    <p class="font-heading text-lg font-semibold tabular-nums {{ $warna }}" x-text="hitung('{{ $kode }}')">{{ $hitung[$kode] }}</p>
                    <p class="text-[11px] text-navy-400">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-3 flex gap-2">
            <label class="relative flex-1">
                <span class="sr-only">Cari siswa</span>
                <input type="search" x-model="cari" placeholder="Cari nama siswa…"
                       class="h-11 w-full rounded-2xl border-0 bg-white pl-10 pr-3 text-[14px] ring-1 ring-ivory-300 placeholder:text-navy-300 focus:ring-2 focus:ring-gold-500">
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
            </label>
            <button type="button" @click="semua('hadir')" class="h-11 shrink-0 rounded-2xl bg-white px-3.5 text-[13px] font-semibold text-ok-500 ring-1 ring-ivory-300 active:scale-95">Semua hadir</button>
        </div>

        @forelse ($siswaPerKelas as $kelasId => $daftar)
            @if (count($kelasList) > 1)
                <h3 class="mt-5 mb-2 text-[12px] font-semibold uppercase tracking-wider text-navy-400">{{ $namaKelas[$kelasId] ?? 'Rombel' }} · {{ count($daftar) }}</h3>
            @endif
            <ul class="mt-3 divide-y divide-ivory-300 overflow-hidden rounded-2xl bg-white ring-1 ring-ivory-300">
                @foreach ($daftar as $i => $s)
                    <li x-show="cocok(@js($s['nama']))" class="px-3.5 py-3">
                        <div class="flex items-center gap-3">
                            <span class="w-6 shrink-0 text-right text-[12px] tabular-nums text-navy-300">{{ $loop->iteration }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[14px] font-semibold text-navy-900">{{ $s['nama'] }}</p>
                                <p class="text-[11px] tabular-nums text-navy-400">{{ $s['nis'] ?: '—' }}</p>
                            </div>
                            <div class="flex shrink-0 gap-1" role="radiogroup" aria-label="Status {{ $s['nama'] }}">
                                @foreach ($statusOpsi as $kode => [$h, $label, $aktif])
                                    <button type="button" role="radio" :aria-checked="absensi[{{ $s['id'] }}] === '{{ $kode }}'"
                                            @click="absensi[{{ $s['id'] }}] = '{{ $kode }}'" title="{{ $label }}"
                                            :class="absensi[{{ $s['id'] }}] === '{{ $kode }}' ? '{{ $aktif }}' : 'bg-ivory-200 text-navy-400'"
                                            class="h-9 w-9 rounded-xl text-[13px] font-bold transition active:scale-90">{{ $h }}</button>
                                @endforeach
                            </div>
                        </div>
                        <div x-show="['izin','sakit'].includes(absensi[{{ $s['id'] }}])" x-cloak class="mt-2 pl-9">
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl bg-ivory-100 px-3 py-2 text-[12px] text-navy-600 ring-1 ring-dashed ring-ivory-400">
                                <x-ui.icon name="salin" class="h-4 w-4 text-navy-400" />
                                <span wire:loading.remove wire:target="bukti_izin.{{ $s['id'] }}">{{ isset($bukti_izin[$s['id']]) ? 'Bukti terlampir ✓' : 'Lampirkan surat izin (opsional)' }}</span>
                                <span wire:loading wire:target="bukti_izin.{{ $s['id'] }}">Mengunggah…</span>
                                <input type="file" class="sr-only" accept=".pdf,image/*" wire:model="bukti_izin.{{ $s['id'] }}">
                            </label>
                        </div>
                    </li>
                @endforeach
            </ul>
        @empty
            <div class="mt-4 rounded-2xl border border-dashed border-ivory-400 bg-ivory-50 p-6 text-center text-[13px] text-navy-500">
                Belum ada siswa di rombel ini{{ count($kelasList) ? '' : '' }}. Hubungi Admin TU untuk melengkapi data siswa.
            </div>
        @endforelse
    </section>

    {{-- ===== Langkah 2: Materi ===== --}}
    @elseif ($langkah === 2)
    <section class="mt-5 space-y-4" aria-labelledby="judul-materi">
        <h2 id="judul-materi" class="font-heading text-[17px] font-semibold text-navy-900">Materi & kegiatan</h2>

        @if ($sebelumnya && ! $modeEdit)
            <div class="flex items-center gap-3 rounded-2xl bg-info-50 p-3.5 ring-1 ring-info-500/15">
                <x-ui.icon name="salin" class="h-5 w-5 text-info-500" />
                <div class="min-w-0 flex-1">
                    <p class="text-[13px] font-semibold text-navy-900">Lanjutan pertemuan {{ $sebelumnya['tanggal'] }}?</p>
                    <p class="truncate text-[12px] text-navy-500">{{ $sebelumnya['materi'] }}</p>
                </div>
                <button type="button" wire:click="salinSebelumnya" class="h-9 shrink-0 rounded-full bg-white px-3 text-[13px] font-semibold text-info-500 ring-1 ring-info-500/20">Salin</button>
            </div>
        @endif

        <div>
            <label for="kd_cp" class="text-[13px] font-semibold text-navy-700">KD / Capaian Pembelajaran <span class="font-normal text-navy-400">(opsional)</span></label>
            <input id="kd_cp" type="text" wire:model.blur="kd_cp" placeholder="Contoh: 3.2 Menganalisis struktur teks…"
                   class="mt-1.5 h-12 w-full rounded-2xl border-0 bg-white px-4 text-[15px] ring-1 ring-ivory-300 placeholder:text-navy-300 focus:ring-2 focus:ring-gold-500">
        </div>

        <div>
            <label for="materi" class="text-[13px] font-semibold text-navy-700">Materi yang diajarkan</label>
            <textarea id="materi" rows="3" wire:model.blur="materi" placeholder="Pokok bahasan hari ini…"
                      class="mt-1.5 w-full resize-none rounded-2xl border-0 bg-white px-4 py-3 text-[15px] leading-relaxed ring-1 placeholder:text-navy-300 focus:ring-2 focus:ring-gold-500 {{ $errors->has('materi') ? 'ring-bad-400' : 'ring-ivory-300' }}"></textarea>
            @error('materi') <p class="mt-1 text-[12px] font-medium text-bad-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="kegiatan" class="text-[13px] font-semibold text-navy-700">Kegiatan pembelajaran</label>
            <div class="mt-1.5 flex flex-wrap gap-1.5">
                @foreach ($metode as $m)
                    <button type="button" @click="tambahMetode(@js($m))" class="h-8 rounded-full bg-white px-3 text-[12px] font-medium text-navy-700 ring-1 ring-ivory-400 active:scale-95">+ {{ $m }}</button>
                @endforeach
            </div>
            <textarea id="kegiatan" x-ref="kegiatan" rows="3" wire:model.blur="kegiatan" placeholder="Metode & aktivitas di kelas…"
                      class="mt-2 w-full resize-none rounded-2xl border-0 bg-white px-4 py-3 text-[15px] leading-relaxed ring-1 placeholder:text-navy-300 focus:ring-2 focus:ring-gold-500 {{ $errors->has('kegiatan') ? 'ring-bad-400' : 'ring-ivory-300' }}"></textarea>
            @error('kegiatan') <p class="mt-1 text-[12px] font-medium text-bad-500">{{ $message }}</p> @enderror
        </div>
    </section>

    {{-- ===== Langkah 3: Dokumentasi & ringkasan ===== --}}
    @else
    <section class="mt-5 space-y-4" aria-labelledby="judul-simpan">
        <h2 id="judul-simpan" class="font-heading text-[17px] font-semibold text-navy-900">Dokumentasi & ringkasan</h2>

        <div>
            <p class="text-[13px] font-semibold text-navy-700">Foto kegiatan <span class="font-normal text-navy-400">(opsional, maks. 4)</span></p>
            <div class="mt-2 grid grid-cols-4 gap-2">
                @foreach ($dokumentasi as $i => $foto)
                    <div class="relative aspect-square overflow-hidden rounded-xl ring-1 ring-ivory-300">
                        <img src="{{ $foto->temporaryUrl() }}" alt="Foto {{ $i + 1 }}" class="h-full w-full object-cover">
                        <button type="button" wire:click="hapusFoto({{ $i }})" aria-label="Hapus foto"
                                class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-navy-950/70 text-white"><x-ui.icon name="tutup" class="h-3.5 w-3.5" /></button>
                    </div>
                @endforeach
                @if (count($dokumentasi) < 4)
                    <label class="flex aspect-square cursor-pointer flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-ivory-400 bg-white text-navy-400 active:scale-95">
                        <x-ui.icon name="kamera" class="h-6 w-6" />
                        <span class="text-[11px] font-medium" wire:loading.remove wire:target="dokumentasi">Tambah</span>
                        <span class="text-[11px] font-medium" wire:loading wire:target="dokumentasi">…</span>
                        <input type="file" accept="image/*" multiple class="sr-only" wire:model="dokumentasi">
                    </label>
                @endif
            </div>
            @error('dokumentasi') <p class="mt-1 text-[12px] font-medium text-bad-500">{{ $message }}</p> @enderror
            @error('dokumentasi.*') <p class="mt-1 text-[12px] font-medium text-bad-500">{{ $message }}</p> @enderror
        </div>

        <dl class="divide-y divide-ivory-300 rounded-2xl bg-white ring-1 ring-ivory-300">
            <div class="flex justify-between gap-4 px-4 py-3 text-[14px]"><dt class="text-navy-500">Kehadiran</dt>
                <dd class="text-right font-semibold text-navy-900">{{ $hitung['hadir'] }} hadir · {{ $hitung['izin'] + $hitung['sakit'] }} izin/sakit · <span class="text-bad-500">{{ $hitung['alfa'] }}</span> alfa</dd></div>
            <div class="px-4 py-3 text-[14px]"><dt class="text-navy-500">Materi</dt><dd class="mt-0.5 font-semibold text-navy-900">{{ $materi ?: '—' }}</dd></div>
            <div class="px-4 py-3 text-[14px]"><dt class="text-navy-500">Kegiatan</dt><dd class="mt-0.5 text-navy-800">{{ $kegiatan ?: '—' }}</dd></div>
        </dl>
    </section>

    @endif

    {{-- Bar aksi --}}
    <div class="sticky bottom-0 -mx-4 mt-6 border-t border-ivory-300 bg-ivory-50/95 px-4 pt-3 pb-safe backdrop-blur-md">
        <div class="flex gap-2.5">
            @if ($langkah > 1)
                <button type="button" wire:click="keLangkah({{ $langkah - 1 }})" class="flex h-12 w-14 shrink-0 items-center justify-center rounded-2xl text-navy-700 ring-1 ring-ivory-400" aria-label="Langkah sebelumnya">
                    <x-ui.icon name="chevron-kanan" class="h-5 w-5 rotate-180" />
                </button>
            @else
                <a wire:navigate href="{{ route('guru.jadwal') }}" class="flex h-12 shrink-0 items-center justify-center rounded-2xl px-4 text-[15px] font-semibold text-navy-700 ring-1 ring-ivory-400">Batal</a>
            @endif

            @if ($langkah < 3)
                <button type="button" wire:click="keLangkah({{ $langkah + 1 }})" wire:loading.attr="disabled"
                        class="flex h-12 flex-1 items-center justify-center gap-2 rounded-2xl bg-navy-900 text-[15px] font-semibold text-white active:scale-[0.98] disabled:opacity-60">
                    Lanjut ke {{ $langkah === 1 ? 'materi' : 'ringkasan' }} <x-ui.icon name="chevron-kanan" class="h-4 w-4" />
                </button>
            @else
                <button type="button" wire:click="simpan" wire:loading.attr="disabled" wire:target="simpan,dokumentasi"
                        class="flex h-12 flex-1 items-center justify-center gap-2 rounded-2xl bg-gold-400 text-[15px] font-semibold text-navy-950 shadow-lg shadow-gold-500/20 active:scale-[0.98] disabled:opacity-60">
                    <span wire:loading.remove wire:target="simpan">{{ $modeEdit ? 'Simpan perubahan' : 'Simpan jurnal' }}</span>
                    <span wire:loading wire:target="simpan">Menyimpan…</span>
                </button>
            @endif
        </div>
    </div>
</div>
