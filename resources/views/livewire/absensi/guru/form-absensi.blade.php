<div x-data="absensiLiveness($wire)" 
     @jarak-updated.window="jarakTeks = Math.round($event.detail.jarak ?? $event.detail[0].jarak) + 'm'"
     @lokasi-error.window="handleLokasiError($event)"
     @validation-error.window="handleValidationError($event)"
     @swal:error.window="handleSwalError($event)"
     @absensi:success.window="handleSuccess()"
     class="fixed inset-0 z-[9999] flex flex-col bg-navy-950 animate-fade-in" style="height: 100dvh;">

    @php
        $waktuDitolak = ! ($cekWaktu['boleh'] ?? true) && ! ($cekWaktu['butuh_alasan'] ?? false);
        $perluAlasan = $cekWaktu['butuh_alasan'] ?? false;
    @endphp

    {{-- Di luar jendela waktu absen: hentikan alur sebelum kamera/GPS dipakai --}}
    @if ($waktuDitolak)
        @include('livewire.absensi.partials.di-luar-jam', [
            'cekWaktu' => $cekWaktu,
            'jendela' => $jendela,
            'jenisAbsen' => $jenisAbsen,
            'urlBeranda' => route('guru.dashboard'),
            'urlRiwayat' => route('akademik.absensi.guru.riwayat'),
        ])
    @endif

    {{-- Pulang sebelum waktunya: minta alasan dulu, baru kamera --}}
    @if ($perluAlasan)
        @include('livewire.absensi.partials.sheet-pulang-cepat', [
            'cekWaktu' => $cekWaktu,
            'urlBatal' => route('guru.dashboard'),
        ])
    @endif

    {{-- Kamera layar penuh; semua kontrol melayang di atasnya --}}
    <div class="relative flex-1 overflow-hidden bg-navy-950 font-body">
        <video x-ref="videoEl" autoplay muted playsinline class="absolute inset-0 z-0 h-full w-full -scale-x-100 object-cover"></video>

        <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 z-10 h-36 bg-linear-to-b from-navy-950/85 to-transparent"></div>
        <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 bottom-0 z-10 h-72 bg-linear-to-t from-navy-950 via-navy-950/75 to-transparent"></div>

        {{-- Bar atas --}}
        <header class="absolute inset-x-0 top-0 z-30 flex h-[calc(3.5rem+env(safe-area-inset-top))] items-end justify-between gap-3 px-3 pb-1 pt-[env(safe-area-inset-top)] text-white"
                x-data="{ jam: '', tick() { this.jam = new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: @js(config('app.timezone')) }).format(Date.now()).replace('.', ':') } }"
                x-init="tick(); setInterval(() => tick(), 15000)">
            <button type="button" @click="closeModalAndGoBack()" aria-label="Tutup kamera"
                    class="flex h-11 w-11 items-center justify-center rounded-full bg-white/10 backdrop-blur-md transition hover:bg-white/20">
                <x-ui.icon name="tutup" class="h-5 w-5" />
            </button>
            <div class="text-center leading-tight">
                <p class="font-heading text-[15px] font-semibold">Absen {{ $absenMasuk ? 'pulang' : 'masuk' }}</p>
                <p class="text-[12px] tabular-nums text-white/65" x-text="jam + ' WITA'"></p>
            </div>
            <div class="flex h-11 min-w-11 items-center gap-1.5 rounded-full bg-white/10 px-3 backdrop-blur-md" aria-live="polite">
                <x-ui.icon name="lokasi" class="h-4 w-4 text-gold-300" />
                <span class="text-[12px] font-semibold tabular-nums" x-text="jarakTeks">…</span>
            </div>
        </header>

        {{-- Panel bawah --}}
        <div x-show="!loadingLokasi && !errorLokasi && !verifikasiBerhasil"
             class="absolute inset-x-0 bottom-0 z-30 flex flex-col items-center px-6 pb-[max(env(safe-area-inset-bottom),1.75rem)] text-white">
            {{-- Status deteksi wajah (hanya ada/tidaknya wajah, bukan pencocokan) --}}
            <p class="mb-5 inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-[13px] font-semibold backdrop-blur-md" aria-live="polite"
               :class="bolehFoto ? 'bg-ok-500/90 text-white' : 'bg-white/15 text-white'">
                <span class="h-2 w-2 rounded-full" :class="bolehFoto ? 'bg-white' : 'animate-pulse bg-gold-300'"></span>
                <span x-text="statusWajah">Menyiapkan kamera…</span>
            </p>
            <button type="button" @click="triggerSuccessCapture()" :disabled="isCapturing || ! bolehFoto" aria-label="Ambil swafoto & absen"
                    class="group relative flex h-[4.75rem] w-[4.75rem] items-center justify-center rounded-full ring-4 ring-white/90 transition active:scale-95 focus-visible:outline-none focus-visible:ring-gold-400 disabled:opacity-60">
                <span class="h-[3.9rem] w-[3.9rem] rounded-full bg-white transition group-hover:scale-95 group-disabled:bg-white/70"></span>
                <span x-show="isCapturing" x-cloak class="absolute inset-0 animate-spin rounded-full border-4 border-transparent border-t-gold-400"></span>
            </button>
            <p class="mt-3 text-[13px] font-semibold" x-text="isCapturing ? 'Memproses…' : 'Ambil swafoto'">Ambil swafoto</p>
        </div>

        {{-- Memuat GPS / kamera --}}
        <div x-show="loadingLokasi" class="absolute inset-0 z-40 flex flex-col items-center justify-center bg-navy-950/92 p-8 text-center text-white backdrop-blur-md" role="status">
            <div class="relative mb-6 flex h-20 w-20 items-center justify-center">
                <span class="absolute inset-0 animate-ping rounded-full bg-gold-400/20 motion-reduce:animate-none"></span>
                <span class="relative flex h-16 w-16 items-center justify-center rounded-full bg-gold-400/15 text-gold-300 ring-1 ring-gold-400/30"><x-ui.icon name="lokasi" class="h-8 w-8" /></span>
            </div>
            <p class="font-heading text-xl font-semibold" x-text="loadingTitle"></p>
            <p class="mt-1.5 max-w-[16rem] text-[14px] text-white/60" x-text="loadingSubtitle"></p>
        </div>

        {{-- Lokasi tidak valid --}}
        <div x-show="errorLokasi" x-cloak x-transition.opacity role="alertdialog" aria-labelledby="judul-lokasi-error"
             class="absolute inset-0 z-40 flex flex-col items-center justify-center bg-navy-950/95 p-8 text-center text-white backdrop-blur-md">
            <span class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-bad-500/20 text-rose-300 ring-1 ring-rose-300/30"><x-ui.icon name="lokasi" class="h-8 w-8" /></span>
            <h3 id="judul-lokasi-error" class="font-heading text-xl font-semibold">Di luar area absen</h3>
            <p class="mt-2 max-w-[18rem] text-[14px] leading-relaxed text-white/70" x-text="pesanErrorLokasi"></p>
            <p class="mt-3 max-w-[18rem] text-[12px] text-white/45">Pastikan GPS aktif & mode akurasi tinggi, lalu coba lagi dari dalam lingkungan sekolah.</p>
            <div class="mt-7 grid w-full max-w-xs grid-cols-2 gap-2.5">
                <button type="button" @click="closeModalAndGoBack()" class="h-12 rounded-2xl text-[15px] font-semibold ring-1 ring-white/25">Kembali</button>
                <button type="button" @click="window.location.reload()" class="h-12 rounded-2xl bg-gold-400 text-[15px] font-semibold text-navy-950">Coba lagi</button>
            </div>
        </div>

        {{-- Berhasil --}}
        <div x-show="verifikasiBerhasil" x-cloak x-transition.opacity role="status"
             class="absolute inset-0 z-40 flex flex-col items-center justify-center bg-navy-950/95 p-8 text-center text-white backdrop-blur-md">
            <span class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gold-400 text-navy-950 shadow-[0_0_0_10px_rgb(212_175_55/0.18)]"><x-ui.icon name="cek" class="h-10 w-10" /></span>
            <h3 class="font-heading text-xl font-semibold">Absen {{ $absenMasuk ? 'pulang' : 'masuk' }} tercatat</h3>
            <p class="mt-1.5 text-[14px] text-white/60">Menyimpan data kehadiran…</p>
        </div>
    </div>

    @foreach (['foto' => null, 'latitude' => 'Lokasi GPS tidak valid. Pastikan GPS aktif.', 'keterangan' => null] as $field => $pesanTetap)
        @error($field)
            <div role="alert" class="absolute inset-x-4 top-[calc(4rem+env(safe-area-inset-top))] z-50 flex gap-2.5 rounded-2xl bg-bad-500/95 p-3.5 text-[13px] font-medium text-white shadow-float backdrop-blur-sm">
                <x-ui.icon name="peringatan" class="h-5 w-5 shrink-0" /> <span>{{ $pesanTetap ?? $message }}</span>
            </div>
        @enderror
    @endforeach

    <!-- Load face-api.js from CDN -->
    @assets
    @endassets

    <script type="module">
        document.addEventListener('alpine:init', () => {
        Alpine.data('absensiLiveness', (wire) => ({
            loadingLokasi: false, // overlay hanya saat menyimpan; GPS dicari di latar belakang
            lokasiSiap: false,
            errorLokasi: false,
            pesanErrorLokasi: '',
            jarakTeks: 'Mencari...',
            
            // Dynamic texts for loading screen
            loadingTitle: 'Melacak Lokasi...',
            loadingSubtitle: 'Mencari sinyal GPS akurasi tinggi.',
            
            // Face AI States
            modelLoading: true,
            stream: null,
            videoEl: null,
            detectionInterval: null,
            
            // Liveness State Machine
            livenessState: 'no_face', // no_face, steady_face, ready_to_blink, blink_closing, success
            livenessText: 'Mencari Wajah...',
            blinkHistory: [],
            baselineEAR: 0,
            blinkStartTime: 0,
            
            isCapturing: false,
            verifikasiBerhasil: false,

            // Deteksi wajah (deteksi keberadaan saja — resources/js/deteksi-wajah.js)
            jumlahWajah: null,      // null = memuat, 0 = tidak ada, n = jumlah wajah
            deteksiTersedia: true,  // false bila model gagal dimuat → swafoto tetap diizinkan
            hentikanDeteksi: null,
            get bolehFoto() { return this.lokasiSiap && (! this.deteksiTersedia || this.jumlahWajah >= 1) },
            get statusWajah() {
                if (! this.lokasiSiap) return 'Mencari lokasi Anda…';
                if (! this.deteksiTersedia) return 'Ambil swafoto dengan wajah terlihat jelas';
                if (this.jumlahWajah === null) return 'Menyiapkan deteksi wajah…';
                if (this.jumlahWajah === 0) return 'Arahkan wajah ke kamera';
                return this.jumlahWajah > 1 ? 'Wajah terdeteksi — pastikan hanya Anda' : 'Wajah terdeteksi ✓';
            },
            mulaiDeteksi() {
                this.hentikanDeteksi?.();
                this.hentikanDeteksi = window.siatuPantauWajah?.(this.videoEl, ({ tersedia, wajah }) => {
                    this.deteksiTersedia = tersedia;
                    this.jumlahWajah = wajah;
                }) ?? null;
                if (! window.siatuPantauWajah) this.deteksiTersedia = false;
            },

            async init() {
                // Jangan nyalakan kamera/GPS bila di luar jendela waktu absen
                if (@js($waktuDitolak)) return;

                this.loadingTitle = 'Menyiapkan Sistem...';
                this.loadingSubtitle = 'Mohon tunggu sebentar.';
                
                this.initCamera();
                this.mulaiLacakLokasi();
            },
            
            closeModalAndGoBack() {
                this.hentikanDeteksi?.();
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                }
                if (this.detectionInterval) clearInterval(this.detectionInterval);
                window.location.href = "{{ route('guru.dashboard') }}";
            },
            
            mulaiLacakLokasi() {
                // Tidak memblokir layar: kamera tetap tampil, status lokasi ada di chip jarak
                this.lokasiSiap = false;
                this.jarakTeks = 'Mencari…';
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        async (position) => {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;
                            wire.set('latitude', lat);
                            wire.set('longitude', lon);

                            try {
                                const valid = await wire.cekLokasi();
                                this.loadingLokasi = false;
                                this.lokasiSiap = valid !== false; // di luar radius → overlay "Di luar area" (event lokasi-error)
                            } catch (e) {
                                this.loadingLokasi = false;
                                this.errorLokasi = true;
                                this.pesanErrorLokasi = "Gagal memvalidasi lokasi dengan server.";
                            }
                        },
                        (error) => {
                            this.loadingLokasi = false;
                            this.errorLokasi = true;
                            let msg = 'Gagal mendapatkan lokasi.';
                            if (error.code == 1) msg = 'Akses lokasi ditolak. Aktifkan GPS dan izinkan browser mengakses lokasi.';
                            else if (error.code == 2) msg = 'Posisi tidak tersedia.';
                            else if (error.code == 3) msg = 'Waktu habis.';
                            this.pesanErrorLokasi = msg;
                        },
                        { enableHighAccuracy: true, timeout: 15000, maximumAge: 30000 }
                    );
                } else {
                    this.loadingLokasi = false;
                    this.errorLokasi = true;
                    this.pesanErrorLokasi = 'Perangkat tidak mendukung GPS.';
                }
            },

            async initCamera() {
                // Determine initial loading text (GPS loading takes precedence)
                this.loadingTitle = 'Menyiapkan Kamera...';
                this.loadingSubtitle = 'Mohon tunggu sebentar.';
                
                await this.startVideo();
            },

            async startVideo() {
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
                    });
                    this.videoEl = this.$refs.videoEl;
                    this.videoEl.srcObject = this.stream;
                    
                    await this.videoEl.play();
                    this.modelLoading = false;
                    this.mulaiDeteksi(); // We use modelLoading flag for initial camera load now
                } catch (err) {
                    console.warn("Kamera depan (user) gagal, mencoba fallback ke kamera default...", err);
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
                        this.videoEl = this.$refs.videoEl;
                        this.videoEl.srcObject = this.stream;
                        
                        await this.videoEl.play();
                        this.modelLoading = false;
                    this.mulaiDeteksi();
                    } catch (fallbackErr) {
                        console.error("Kamera tetap gagal diakses:", fallbackErr);
                        Swal.fire({
                            title: 'Kamera Diblokir/Tidak Ditemukan',
                            html: 'Gagal mengakses kamera. Silakan cek:<br>1. <b>Izin kamera</b> di browser (klik ikon gembok dekat URL)<br>2. Pastikan kamera <b>tidak sedang dipakai</b> oleh aplikasi lain.',
                            icon: 'error',
                            confirmButtonText: 'Kembali'
                        }).then(() => {
                            this.closeModalAndGoBack();
                        });
                    }
                }
            },

            triggerSuccessCapture() {
                if (! this.bolehFoto || this.isCapturing) return;
                this.hentikanDeteksi?.();
                // Show loading state while submitting to backend
                this.isCapturing = true;
                this.loadingLokasi = true;
                this.loadingTitle = 'Menyimpan Absensi...';
                this.loadingSubtitle = 'Mohon tunggu, sedang mengirim data...';
                
                clearInterval(this.detectionInterval);
                
                const canvas = document.createElement('canvas');
                canvas.width = this.videoEl.videoWidth;
                canvas.height = this.videoEl.videoHeight;
                const ctx = canvas.getContext('2d');
                
                // Flip horizontally so the captured photo matches the mirror preview
                ctx.translate(canvas.width, 0);
                ctx.scale(-1, 1);
                ctx.drawImage(this.videoEl, 0, 0, canvas.width, canvas.height);
                
                const base64Image = canvas.toDataURL('image/jpeg', 0.8);
                
                wire.set('foto', base64Image);
                wire.call('submitAbsen').catch(err => {
                    // Catch Livewire JS exceptions (like validation failure)
                    this.loadingLokasi = false;
                    this.errorLokasi = true;
                    this.pesanErrorLokasi = "Validasi gagal. Pastikan GPS aktif dan wajah terlihat jelas.";
                    this.isCapturing = false;
                });
            },
            
            handleLokasiError(event) {
                let errorMsg = event.detail.message ?? (event.detail[0] && event.detail[0].message);
                if (errorMsg) {
                    this.loadingLokasi = false;
                    this.errorLokasi = true;
                    this.pesanErrorLokasi = errorMsg;
                }
            },
            
            handleValidationError(event) {
                let messages = event.detail.messages ?? (event.detail[0] && event.detail[0].messages);
                let errorMsg = Array.isArray(messages) ? messages.join(', ') : "Data absen tidak lengkap atau tidak valid.";
                this.loadingLokasi = false;
                this.errorLokasi = true;
                this.pesanErrorLokasi = errorMsg;
                this.isCapturing = false;
            },
            
            handleSwalError(event) {
                this.loadingLokasi = false;
                this.isCapturing = false;
            },
            
            handleSuccess() {
                this.loadingLokasi = false;
                this.verifikasiBerhasil = true;
                setTimeout(() => {
                    window.location.href = "{{ route('guru.dashboard') }}";
                }, 1500);
            }
        }));
    });
    </script>
</div>
