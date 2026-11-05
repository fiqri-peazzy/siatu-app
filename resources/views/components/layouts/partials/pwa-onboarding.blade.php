{{--
    Onboarding pertama kali (sekali per perangkat per akun):
      1. Sambutan  2. Izin lokasi & kamera  3. Notifikasi  4. Pasang di layar utama
    Izin selalu diminta lewat tombol (gestur pengguna) — tidak otomatis saat halaman dibuka.
    Buka ulang dari mana saja: window.dispatchEvent(new CustomEvent('siatu:onboarding'))
--}}
@php
    $namaDepan = \Illuminate\Support\Str::of($user?->name ?? '')->explode(' ')->first() ?: 'Bapak/Ibu';
    $kunci = 'siatu:onboarding:v1:'.($user?->id ?? 'tamu');
@endphp

<script>
    // Tangkap prompt instalasi sedini mungkin (Chrome/Android)
    window.addEventListener('beforeinstallprompt', (e) => { e.preventDefault(); window.__siatuInstall = e; });
</script>

<div x-data="{
        open: false,
        langkah: 0,
        total: 4,
        izin: { lokasi: 'belum', kamera: 'belum', notifikasi: 'belum' },
        bisaPasang: false,
        terpasang: false,
        ios: /iphone|ipad|ipod/i.test(navigator.userAgent),
        kunci: @js($kunci),

        init() {
            let sudah = false;
            try { sudah = localStorage.getItem(this.kunci) === '1' } catch (e) {}
            this.terpasang = matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
            this.bisaPasang = !! window.__siatuInstall;
            window.addEventListener('beforeinstallprompt', () => this.bisaPasang = true);
            window.addEventListener('siatu:onboarding', () => { this.langkah = 0; this.open = true });
            this.cekIzin();
            if (! sudah) this.$nextTick(() => this.open = true);
        },
        async cekIzin() {
            if (window.Notification) this.izin.notifikasi = { granted: 'ok', denied: 'ditolak' }[Notification.permission] ?? 'belum';
            if (! navigator.permissions?.query) return;
            for (const [kunci, nama] of [['lokasi', 'geolocation'], ['kamera', 'camera']]) {
                try {
                    const s = await navigator.permissions.query({ name: nama });
                    this.izin[kunci] = { granted: 'ok', denied: 'ditolak' }[s.state] ?? 'belum';
                } catch (e) {}
            }
        },
        mintaLokasi() {
            if (! navigator.geolocation) { this.izin.lokasi = 'tidak_didukung'; return }
            this.izin.lokasi = 'memproses';
            navigator.geolocation.getCurrentPosition(
                () => this.izin.lokasi = 'ok',
                (e) => this.izin.lokasi = e.code === 1 ? 'ditolak' : 'gagal',
                { enableHighAccuracy: true, timeout: 12000 });
        },
        async mintaKamera() {
            if (! navigator.mediaDevices?.getUserMedia) { this.izin.kamera = 'tidak_didukung'; return }
            this.izin.kamera = 'memproses';
            try {
                const s = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                s.getTracks().forEach((t) => t.stop());
                this.izin.kamera = 'ok';
            } catch (e) { this.izin.kamera = e.name === 'NotAllowedError' ? 'ditolak' : 'gagal' }
        },
        async mintaNotifikasi() {
            if (! window.Notification) { this.izin.notifikasi = 'tidak_didukung'; return }
            this.izin.notifikasi = 'memproses';
            const hasil = await Notification.requestPermission();
            this.izin.notifikasi = hasil === 'granted' ? 'ok' : (hasil === 'denied' ? 'ditolak' : 'belum');
            if (hasil === 'granted') window.siatuSubscribePush?.().catch(() => {});
        },
        async pasang() {
            const p = window.__siatuInstall; if (! p) return;
            p.prompt();
            const { outcome } = await p.userChoice;
            if (outcome === 'accepted') this.terpasang = true;
            window.__siatuInstall = null; this.bisaPasang = false;
        },
        lanjut() { this.langkah < this.total - 1 ? this.langkah++ : this.selesai() },
        selesai() { try { localStorage.setItem(this.kunci, '1') } catch (e) {} this.open = false },
     }"
     x-show="open" x-cloak
     x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0" x-transition:leave="transition duration-200" x-transition:leave-end="opacity-0"
     @keydown.escape.window="if (open) selesai()"
     role="dialog" aria-modal="true" aria-label="Pengenalan aplikasi"
     class="fixed inset-0 z-[60] overflow-y-auto bg-console font-body text-white">

    <div class="mx-auto flex min-h-dvh max-w-md flex-col px-6 pt-safe pb-safe-lg">

        {{-- Progres + lewati --}}
        <div class="flex h-14 items-center gap-4">
            <div class="flex flex-1 gap-1.5" aria-hidden="true">
                <template x-for="i in total" :key="i">
                    <span class="h-1 flex-1 rounded-full transition-colors duration-300" :class="i - 1 <= langkah ? 'bg-gold-400' : 'bg-white/15'"></span>
                </template>
            </div>
            <button type="button" @click="selesai()" class="-mr-2 h-11 px-2 text-sm font-semibold text-white/60 hover:text-white">Lewati</button>
        </div>

        {{-- Ilustrasi --}}
        <div class="flex justify-center pt-6 pb-8 short:pt-1 short:pb-3" aria-hidden="true">
            <div class="relative flex h-36 w-36 items-center justify-center short:h-24 short:w-24">
                <span class="absolute inset-0 rounded-full bg-gold-400/10 ring-1 ring-gold-400/20"></span>
                <span class="absolute inset-5 rounded-full bg-white/5 ring-1 ring-white/10"></span>
                <img x-show="langkah === 0" src="{{ asset('images/logo-removebg-preview.png') }}" alt="" class="relative h-20 w-20 object-contain short:h-14 short:w-14">
                <span x-show="langkah === 1" class="relative text-gold-300"><x-ui.icon name="lokasi" class="h-14 w-14" /></span>
                <span x-show="langkah === 2" class="relative text-gold-300"><x-ui.icon name="lonceng" class="h-14 w-14" /></span>
                <span x-show="langkah === 3" class="relative text-gold-300"><x-ui.icon name="dashboard" class="h-14 w-14" /></span>
            </div>
        </div>

        <div class="flex-1">
            {{-- 1. Sambutan --}}
            <section x-show="langkah === 0">
                <p class="text-sm font-semibold text-gold-300">Assalamu'alaikum, {{ $namaDepan }}</p>
                <h2 class="mt-2 font-heading text-[1.75rem] font-semibold leading-tight">Selamat datang di SIATU</h2>
                <p class="mt-2 text-[15px] leading-relaxed text-white/70">Portal {{ $isGuru ? 'guru' : 'tenaga kependidikan' }} SMA Muhammadiyah Kota Gorontalo.</p>
                <ul class="mt-6 space-y-3">
                    @foreach (array_filter([
                        ['absen', 'Absen masuk & pulang', 'Cukup dari HP di area sekolah'],
                        $isGuru ? ['jurnal', 'Jadwal & jurnal mengajar', 'Isi jurnal langsung setelah kelas'] : null,
                        ['lonceng', 'Pengingat otomatis', 'Tidak lupa absen atau jurnal'],
                    ]) as [$ikon, $judul, $ket])
                        <li class="flex items-center gap-3.5 rounded-2xl bg-white/5 p-3.5 ring-1 ring-white/10">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-400/15 text-gold-300"><x-ui.icon :name="$ikon" /></span>
                            <span><span class="block text-[15px] font-semibold">{{ $judul }}</span><span class="block text-[13px] text-white/55">{{ $ket }}</span></span>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- 2. Lokasi & kamera --}}
            <section x-show="langkah === 1" x-cloak>
                <h2 class="font-heading text-[1.75rem] font-semibold leading-tight">Izinkan lokasi & kamera</h2>
                <p class="mt-2 text-[15px] leading-relaxed text-white/70">Dipakai hanya saat absen: lokasi untuk memastikan Anda di area sekolah, kamera untuk swafoto.</p>
                <div class="mt-6 space-y-3">
                    @foreach ([['lokasi', 'Lokasi', 'mintaLokasi()'], ['kamera', 'Kamera', 'mintaKamera()']] as [$k, $label, $aksi])
                        <div class="flex items-center gap-3.5 rounded-2xl bg-white/5 p-3.5 ring-1 ring-white/10">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white"><x-ui.icon :name="$k" /></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[15px] font-semibold">{{ $label }}</p>
                                <p class="text-[13px]" :class="{ 'text-emerald-300': izin.{{ $k }} === 'ok', 'text-rose-300': ['ditolak','gagal','tidak_didukung'].includes(izin.{{ $k }}), 'text-white/55': ['belum','memproses'].includes(izin.{{ $k }}) }"
                                   x-text="{ ok: 'Diizinkan', ditolak: 'Ditolak — buka pengaturan browser untuk mengizinkan', gagal: 'Belum berhasil, coba lagi', tidak_didukung: 'Tidak didukung browser ini', memproses: 'Menunggu persetujuan…', belum: 'Belum diizinkan' }[izin.{{ $k }}]"></p>
                            </div>
                            <button type="button" @click="{{ $aksi }}" x-show="izin.{{ $k }} !== 'ok'" :disabled="izin.{{ $k }} === 'memproses'"
                                    class="h-10 shrink-0 rounded-full bg-white px-4 text-sm font-semibold text-navy-900 transition active:scale-95 disabled:opacity-60">Izinkan</button>
                            <span x-show="izin.{{ $k }} === 'ok'" class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-400/20 text-emerald-300"><x-ui.icon name="cek" class="h-4 w-4" /></span>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- 3. Notifikasi --}}
            <section x-show="langkah === 2" x-cloak>
                <h2 class="font-heading text-[1.75rem] font-semibold leading-tight">Aktifkan pengingat</h2>
                <p class="mt-2 text-[15px] leading-relaxed text-white/70">Kami kirim pengingat {{ $isGuru ? 'jurnal mengajar dan ' : '' }}info penting dari sekolah — tetap muncul walau aplikasi ditutup.</p>
                <div class="mt-6 flex items-center gap-3.5 rounded-2xl bg-white/5 p-3.5 ring-1 ring-white/10">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10"><x-ui.icon name="lonceng" /></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[15px] font-semibold">Notifikasi</p>
                        <p class="text-[13px]" :class="izin.notifikasi === 'ok' ? 'text-emerald-300' : (izin.notifikasi === 'ditolak' ? 'text-rose-300' : 'text-white/55')"
                           x-text="{ ok: 'Aktif', ditolak: 'Diblokir — aktifkan dari pengaturan browser', tidak_didukung: 'Tidak didukung browser ini', memproses: 'Menunggu persetujuan…', belum: 'Belum aktif' }[izin.notifikasi]"></p>
                    </div>
                    <button type="button" @click="mintaNotifikasi()" x-show="! ['ok', 'tidak_didukung'].includes(izin.notifikasi)" :disabled="izin.notifikasi === 'memproses'"
                            class="h-10 shrink-0 rounded-full bg-white px-4 text-sm font-semibold text-navy-900 transition active:scale-95 disabled:opacity-60">Aktifkan</button>
                    <span x-show="izin.notifikasi === 'ok'" class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-400/20 text-emerald-300"><x-ui.icon name="cek" class="h-4 w-4" /></span>
                </div>
                <p x-show="ios && ! terpasang" class="mt-3 text-[13px] leading-relaxed text-white/55">Di iPhone, notifikasi hanya berfungsi setelah aplikasi dipasang ke layar utama (langkah berikutnya).</p>
            </section>

            {{-- 4. Pasang --}}
            <section x-show="langkah === 3" x-cloak>
                <h2 class="font-heading text-[1.75rem] font-semibold leading-tight">Pasang di layar utama</h2>
                <p class="mt-2 text-[15px] leading-relaxed text-white/70">Buka SIATU seperti aplikasi biasa — lebih cepat, layar penuh, dan notifikasi lebih andal.</p>

                <div x-show="terpasang" class="mt-6 flex items-center gap-3 rounded-2xl bg-emerald-400/10 p-4 text-emerald-200 ring-1 ring-emerald-300/25">
                    <x-ui.icon name="cek" /> <span class="text-[15px] font-semibold">Aplikasi sudah terpasang</span>
                </div>

                <button x-show="! terpasang && bisaPasang" type="button" @click="pasang()"
                        class="mt-6 flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-white text-[15px] font-semibold text-navy-900 active:scale-[0.98]">
                    <x-ui.icon name="dashboard" /> Pasang aplikasi
                </button>

                <ol x-show="! terpasang && ! bisaPasang" class="mt-6 space-y-2.5 text-[14px] text-white/80">
                    <template x-if="ios">
                        <div class="space-y-2.5">
                            <li class="flex gap-3 rounded-2xl bg-white/5 p-3.5 ring-1 ring-white/10"><b class="text-gold-300">1</b> Buka halaman ini di <b>Safari</b></li>
                            <li class="flex gap-3 rounded-2xl bg-white/5 p-3.5 ring-1 ring-white/10"><b class="text-gold-300">2</b> Ketuk tombol <b>Bagikan</b> (kotak dengan panah ke atas)</li>
                            <li class="flex gap-3 rounded-2xl bg-white/5 p-3.5 ring-1 ring-white/10"><b class="text-gold-300">3</b> Pilih <b>Tambah ke Layar Utama</b></li>
                        </div>
                    </template>
                    <template x-if="! ios">
                        <div class="space-y-2.5">
                            <li class="flex gap-3 rounded-2xl bg-white/5 p-3.5 ring-1 ring-white/10"><b class="text-gold-300">1</b> Ketuk menu <b>⋮</b> di pojok kanan atas Chrome</li>
                            <li class="flex gap-3 rounded-2xl bg-white/5 p-3.5 ring-1 ring-white/10"><b class="text-gold-300">2</b> Pilih <b>Instal aplikasi</b> atau <b>Tambahkan ke layar utama</b></li>
                        </div>
                    </template>
                </ol>
            </section>
        </div>

        {{-- Aksi --}}
        <div class="mt-8 flex items-center gap-3 short:mt-4">
            <button type="button" x-show="langkah > 0" @click="langkah--"
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-white ring-1 ring-white/20 hover:bg-white/10" aria-label="Kembali">
                <x-ui.icon name="chevron-kanan" class="h-5 w-5 rotate-180" />
            </button>
            <button type="button" @click="lanjut()"
                    class="flex h-12 flex-1 items-center justify-center rounded-2xl bg-gold-400 text-[15px] font-semibold text-navy-950 shadow-lg shadow-gold-500/20 transition hover:bg-gold-300 active:scale-[0.98]"
                    x-text="langkah === total - 1 ? 'Mulai gunakan' : (langkah === 0 ? 'Mulai' : 'Lanjut')">Mulai</button>
        </div>
    </div>
</div>
