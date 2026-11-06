// Komponen Alpine absen TENDIK. Global agar tetap tersedia saat dibuka lewat wire:navigate.
window.absensiSelfieTendik = (wire, cfg = {}) => ({
            // Dipanggil Alpine saat komponen dilepas (mis. pindah halaman via wire:navigate): matikan kamera
            destroy() {
                this.hentikanDeteksi?.();
                this.stream?.getTracks().forEach((t) => t.stop());
                if (this.detectionInterval) clearInterval(this.detectionInterval);
            },
            lokasiDitemukan: false,
            loadingLokasi: false,
            alamatDetail: 'Sedang melacak alamat...',
            
            // Modal States
            showLivenessModal: false,
            
            // Face AI States
            modelLoading: true,
            faceDetected: false,
            blinkDetected: false,
            verifikasiBerhasil: false,
            stream: null,
            videoEl: null,
            detectionInterval: null,
            blinkHistory: [],
            isCapturing: false,
            
            init() {
                this.lokasiDitemukan = wire.get('latitude') ? true : false;
                if (this.lokasiDitemukan) {
                    // Fetch address
                    this.fetchAddress(wire.get('latitude'), wire.get('longitude'));
                    wire.call('cekLokasi');
                } else {
                    this.mulaiLacakLokasi();
                }
            },
            
            openModal() {
                this.showLivenessModal = true;
                this.initCamera();
            },
            
            closeModal() {
                this.showLivenessModal = false;
                if (this.stream) {
                    this.hentikanDeteksi?.();
                    this.stream.getTracks().forEach(track => track.stop());
                }
                if (this.detectionInterval) clearInterval(this.detectionInterval);
            },
            
            mulaiLacakLokasi() {
                this.loadingLokasi = true;
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        async (position) => {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;
                            wire.set('latitude', lat);
                            wire.set('longitude', lon);
                            this.lokasiDitemukan = true;
                            this.loadingLokasi = false;
                            
                            this.fetchAddress(lat, lon);
                            
                            // Validasi Jarak di Backend
                            await wire.call('cekLokasi');
                        },
                        (error) => {
                            this.loadingLokasi = false;
                        let msg = 'Gagal mendapatkan lokasi.';
                        if (error.code == 1) msg = 'Akses lokasi ditolak. Aktifkan GPS dan izinkan browser mengakses lokasi.';
                        else if (error.code == 2) msg = 'Posisi tidak tersedia.';
                        else if (error.code == 3) msg = 'Waktu habis.';
                        window.showAppAlert.error(msg);
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                this.loadingLokasi = false;
                window.showAppAlert.error('Perangkat tidak mendukung GPS.');
            }
        },

        async fetchAddress(lat, lon) {
            if (!lat || !lon) return;
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                const data = await response.json();
                if (data && data.display_name) {
                    this.alamatDetail = data.display_name;
                }
            } catch (e) {
                this.alamatDetail = `Koordinat: ${lat.toFixed(5)}, ${lon.toFixed(5)} (Gagal memuat alamat)`;
            }
        },

        async initCamera() {
            this.videoEl = document.getElementById('webcam-video');
            this.modelLoading = false; // We don't load AI anymore
            this.startVideo();
        },

        async startVideo() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
                });
                this.videoEl.srcObject = this.stream;
                await this.videoEl.play().catch(() => {});
                this.mulaiDeteksi();
            } catch (err) {
                console.error(err);
                window.showAppAlert.error('Gagal mengakses kamera depan.');
            }
        },

        // Deteksi wajah (deteksi keberadaan saja — resources/js/deteksi-wajah.js)
        jumlahWajah: null,
        deteksiTersedia: true,
        hentikanDeteksi: null,
        get bolehFoto() { return ! this.deteksiTersedia || this.jumlahWajah >= 1 },
        get statusWajah() {
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

        triggerSuccessCapture() {
            if (! this.bolehFoto || this.isCapturing) return;
            this.hentikanDeteksi?.();
            this.isCapturing = true;
            this.verifikasiBerhasil = true;
            
            // Capture frame from video
            const canvas = document.createElement('canvas');
            canvas.width = this.videoEl.videoWidth;
            canvas.height = this.videoEl.videoHeight;
            const ctx = canvas.getContext('2d');
            
            // Flip horizontal so it acts like a mirror
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(this.videoEl, 0, 0, canvas.width, canvas.height);
            
            const base64Image = canvas.toDataURL('image/jpeg', 0.8);
            
            // Send to Livewire
            wire.set('foto', base64Image);
            
            // Wait a tiny bit for UI update, then submit
            setTimeout(() => {
                wire.call('submitAbsen').catch(err => {
                    this.isCapturing = false;
                    this.verifikasiBerhasil = false;
                });
            }, 500);
        }
});
