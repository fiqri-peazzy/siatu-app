{{--
    Layar penuh saat absen tidak bisa dilakukan sekarang.
    Variabel: $cekWaktu (AturanAbsensiService::evaluasi), $jendela (::jendela), $jenisAbsen, $urlBeranda, $urlRiwayat
--}}
@php
    $kode = $cekWaktu['kode'] ?? 'sudah_tutup';
    $jenisLabel = $jenisAbsen === 'pulang' ? 'pulang' : 'masuk';

    [$chip, $chipClass, $judul, $bantuan] = match ($kode) {
        'belum_buka' => ['Belum dibuka', 'bg-info-500/15 text-sky-200 ring-sky-300/25', "Absen {$jenisLabel} belum dibuka", null],
        'sudah_absen' => ['Sudah tercatat', 'bg-ok-500/20 text-emerald-200 ring-emerald-300/25', "Absen {$jenisLabel} sudah tercatat", 'Tidak perlu absen lagi. Lihat detailnya di riwayat absen.'],
        'belum_masuk' => ['Perlu absen masuk', 'bg-warn-500/20 text-amber-200 ring-amber-300/25', 'Anda belum absen masuk', 'Absen pulang baru bisa dilakukan setelah absen masuk.'],
        'libur' => ['Hari libur', 'bg-white/10 text-white/80 ring-white/15', 'Hari ini libur', 'Absensi tidak dibuka pada hari libur nasional.'],
        'bukan_hari_kerja' => ['Bukan hari kerja', 'bg-white/10 text-white/80 ring-white/15', 'Bukan hari kerja', 'Absensi hanya dibuka pada hari kerja sekolah.'],
        default => ['Sudah ditutup', 'bg-bad-500/20 text-rose-200 ring-rose-300/25', "Absen {$jenisLabel} sudah ditutup", 'Hubungi Admin TU agar ketidakhadiran Anda dicatat sebagai izin, sakit, atau dinas luar.'],
    };

    // Jam dibuka berikutnya (untuk hitung mundur)
    $jamBuka = $kode === 'belum_buka'
        ? ($jenisAbsen === 'pulang' ? $jendela['pulang_buka'] : $jendela['masuk_buka'])
        : null;
    $tampilkanGaris = ($jendela['hari_kerja'] ?? false) && ! in_array($kode, ['libur', 'bukan_hari_kerja'], true);

    // Timestamp (ms) dari server → hitung mundur akurat walau zona waktu HP berbeda dari sekolah
    $bukaMs = $jamBuka ? now()->setTimeFromTimeString($jamBuka)->getTimestampMs() : null;
@endphp

<section role="alertdialog" aria-labelledby="judul-di-luar-jam" aria-describedby="pesan-di-luar-jam"
         x-data="{
            now: Date.now(),
            bukaMs: @js($bukaMs),
            get jam() {
                return new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: @js(config('app.timezone')) })
                    .format(this.now).replace('.', ':');
            },
            get sisaMenit() {
                return this.bukaMs ? Math.max(0, Math.ceil((this.bukaMs - this.now) / 60000)) : null;
            },
            get sisaTeks() {
                const s = this.sisaMenit; if (s === null) return '';
                return s >= 60 ? `${Math.floor(s / 60)} jam ${s % 60} menit` : `${s} menit`;
            },
            init() {
                setInterval(() => {
                    this.now = Date.now();
                    if (this.bukaMs && this.sisaMenit === 0) window.location.reload();
                }, 15000);
            }
         }"
         class="absolute inset-0 z-70 overflow-y-auto overscroll-contain bg-console font-body text-white">
    <div class="flex min-h-full flex-col px-5 pt-safe sm:px-8">

        {{-- Header --}}
        <header class="flex h-12 items-center justify-between">
            <a href="{{ $urlBeranda }}" aria-label="Kembali ke beranda"
               class="-ml-2 flex h-11 w-11 items-center justify-center rounded-full text-white/80 transition hover:bg-white/10 hover:text-white focus-visible:outline-2 focus-visible:outline-gold-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <p class="text-xs font-medium text-white/55">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </header>

        {{-- Isi (dapat di-scroll; aksi tetap terlihat di footer) --}}
        <div class="mx-auto my-auto grid w-full max-w-md gap-4 py-4 sm:gap-5 sm:py-6 short:max-w-4xl short:grid-cols-2 short:items-start short:gap-8 short:py-2">

            <div class="short:pr-2">
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 {{ $chipClass }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $chip }}
                </span>

                <h1 id="judul-di-luar-jam" class="mt-3 font-heading text-[1.6rem] font-semibold leading-tight tracking-tight text-balance sm:mt-4 sm:text-3xl">
                    {{ $judul }}
                </h1>
                <p id="pesan-di-luar-jam" class="mt-2 text-[15px] leading-relaxed text-white/70 text-pretty">{{ $cekWaktu['pesan'] }}</p>

                {{-- Hitung mundur hanya saat absen akan dibuka (kasus lain: posisi "Sekarang" ada di garis waktu) --}}
                @if ($jamBuka)
                    <div class="mt-4 flex items-stretch gap-3">
                        <div class="rounded-2xl bg-white/4 px-4 py-3 ring-1 ring-white/10">
                            <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-white/45">Sekarang</p>
                            <p class="mt-1 font-heading text-3xl font-semibold tabular-nums leading-none text-white" x-text="jam">{{ now()->format('H:i') }}</p>
                        </div>
                        <div class="flex-1 rounded-2xl bg-gold-400/10 px-4 py-3 ring-1 ring-gold-400/25">
                            <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-gold-200/80">Dibuka dalam</p>
                            <p class="mt-1 font-heading text-lg font-semibold leading-tight text-gold-300" x-text="sisaTeks">—</p>
                            <p class="mt-0.5 text-[11px] text-gold-200/60">Halaman memuat ulang otomatis</p>
                        </div>
                    </div>
                @endif

                @if ($bantuan)
                    <p class="mt-4 flex gap-2.5 rounded-2xl bg-white/4 p-3.5 text-[13px] leading-relaxed text-white/70 ring-1 ring-white/10">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $bantuan }}</span>
                    </p>
                @endif
            </div>

            @if ($tampilkanGaris)
                <div class="rounded-3xl bg-white/4 p-4 ring-1 ring-white/10 backdrop-blur-sm sm:p-5">
                    <div class="mb-3.5 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-white">Jam absen hari ini</h2>
                        <span class="text-[11px] text-white/45">WITA</span>
                    </div>
                    <x-absensi.jendela-waktu :jendela="$jendela" :sekarang="now()->format('H:i')" tone="dark" />
                </div>
            @endif
        </div>

        {{-- Aksi: menempel di bawah layar agar tidak pernah tersembunyi --}}
        <footer class="sticky bottom-0 -mx-5 mt-auto bg-linear-to-t from-navy-950 from-60% to-transparent px-5 pt-6 pb-safe-lg sm:-mx-8 sm:px-8">
            <div class="mx-auto grid w-full max-w-md grid-cols-2 gap-2.5 short:max-w-4xl">
                <a href="{{ $urlRiwayat }}"
                   class="flex h-12 items-center justify-center rounded-2xl px-3 text-sm font-semibold text-white ring-1 ring-white/20 transition hover:bg-white/10 active:scale-[0.98] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold-300">
                    Riwayat
                </a>
                <a href="{{ $urlBeranda }}"
                   class="flex h-12 items-center justify-center rounded-2xl bg-gold-400 px-3 text-sm font-semibold text-navy-950 shadow-lg shadow-gold-500/20 transition hover:bg-gold-300 active:scale-[0.98] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold-300">
                    Ke Beranda
                </a>
            </div>
        </footer>
    </div>
</section>
