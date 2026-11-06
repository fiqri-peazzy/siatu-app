// Komponen Alpine layar swafoto absen GURU. Global (bukan Alpine.data di alpine:init) agar tetap
// tersedia saat halaman dibuka lewat wire:navigate — alpine:init hanya terjadi sekali per muat penuh.
window.absensiSelfieGuru = (wire, cfg = {}) => ({
            // Dipanggil Alpine saat komponen dilepas (mis. pindah halaman via wire:navigate): matikan kamera
            destroy() {
                this.hentikanDeteksi?.();
                this.stream?.getTracks().forEach((t) => t.stop());
                if (this.detectionInterval) clearInterval(this.detectionInterval);
            },
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
                if (cfg.waktuDitolak) return;

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
                window.location.href = cfg.kembali ?? '/guru';
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
                    window.location.href = cfg.kembali ?? '/guru';
                }, 1500);
            }
});
