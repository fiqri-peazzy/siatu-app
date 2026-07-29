<div x-data="absensiLiveness($wire)" 
     @jarak-updated.window="jarakTeks = Math.round($event.detail.jarak ?? $event.detail[0].jarak) + 'm'"
     @lokasi-error.window="handleLokasiError($event)"
     @validation-error.window="handleValidationError($event)"
     @swal:error.window="handleSwalError($event)"
     @absensi:success.window="handleSuccess()"
     class="fixed inset-0 z-[9999] flex flex-col bg-[#121A2F] animate-fade-in" style="height: 100dvh;">
    
    <!-- Top Bar -->
    <div class="px-5 pt-10 pb-4 flex items-center justify-between z-20 relative">
        <div class="flex items-center gap-3">
            <button type="button" @click="closeModalAndGoBack()" class="w-8 h-8 flex items-center justify-center text-white hover:bg-white/10 rounded-full transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </button>
            <div class="flex flex-col">
                <span class="text-white font-bold text-[18px] leading-tight">Ambil Foto</span>
                <span class="text-white font-bold text-[18px] leading-tight">Absen</span>
            </div>
        </div>
        <div class="bg-white/10 rounded-full px-3 py-1.5 flex items-center gap-1.5 backdrop-blur-sm border border-white/5">
            <svg class="w-3.5 h-3.5 text-rose-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/></svg>
            <span class="text-[10px] font-medium text-white tracking-wide" x-text="jarakTeks">...</span>
        </div>
    </div>
    
    <!-- Stepper Dots -->
    <div class="flex justify-center items-center gap-3 mb-6 z-20 relative">
        <div class="w-1.5 h-1.5 rounded-full bg-indigo-400"></div>
        <div class="w-10 h-[1px] bg-indigo-400/30"></div>
        <div class="w-2.5 h-2.5 rounded-full bg-indigo-400 ring-4 ring-indigo-400/30"></div>
        <div class="w-10 h-[1px] bg-indigo-400/30"></div>
        <div class="w-1.5 h-1.5 rounded-full bg-indigo-400/30"></div>
    </div>
    
    <!-- Camera Area -->
    <div class="flex-1 relative w-full h-full bg-[#121A2F] overflow-hidden flex flex-col justify-end">
        
        <video x-ref="videoEl" autoplay muted playsinline class="absolute inset-0 z-0 w-full h-full object-cover transform scale-x-[-1]"></video>
        
        <!-- Loading GPS Overlay -->
        <div x-show="loadingLokasi" class="absolute inset-0 bg-slate-900/90 backdrop-blur-md z-20 flex flex-col items-center justify-center text-white p-6 text-center">
            <svg class="animate-spin w-12 h-12 text-indigo-500 mb-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <p class="font-extrabold text-xl mb-2" x-text="loadingTitle"></p>
            <p class="text-sm text-slate-400 max-w-[250px]" x-text="loadingSubtitle"></p>
        </div>

        <!-- Manual Capture Button -->
        <div x-show="!loadingLokasi && !errorLokasi && !verifikasiBerhasil" class="relative z-30 pb-12 pt-8 w-full flex justify-center bg-gradient-to-t from-[#121A2F] to-transparent">
            <button type="button" 
                    @click="triggerSuccessCapture()" 
                    :disabled="isCapturing"
                    class="relative group focus:outline-none focus:ring-4 focus:ring-indigo-500/30 rounded-full transition-all duration-300 transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                <!-- Outer ring -->
                <div class="absolute inset-0 bg-white/20 rounded-full backdrop-blur-sm group-hover:bg-white/30 transition-colors"></div>
                <div class="w-20 h-20 border-[3px] border-white rounded-full flex items-center justify-center p-1.5">
                    <!-- Inner button -->
                    <div class="w-full h-full bg-white rounded-full group-hover:scale-95 transition-transform duration-300"></div>
                </div>
            </button>
        </div>

        <!-- Error GPS Overlay -->
        <div x-show="errorLokasi" style="display: none;" class="absolute inset-0 bg-rose-900/95 backdrop-blur-md z-40 flex flex-col items-center justify-center text-white p-6 text-center" x-transition>
            <div class="w-20 h-20 bg-rose-500 rounded-full flex items-center justify-center shadow-2xl mb-6 ring-8 ring-rose-500/30">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="font-extrabold text-2xl drop-shadow-md mb-2">Lokasi Tidak Valid</h3>
            <p class="text-rose-200 text-sm max-w-[280px] mb-8" x-text="pesanErrorLokasi"></p>
            <button type="button" @click="closeModalAndGoBack()" class="bg-white text-rose-600 px-6 py-3 rounded-full font-bold text-sm shadow-lg active:scale-95 transition-all">
                Kembali
            </button>
        </div>

        <!-- Success Overlay -->
        <div x-show="verifikasiBerhasil" style="display: none;" class="absolute inset-0 bg-[#121A2F]/95 backdrop-blur-md z-30 flex flex-col items-center justify-center text-white p-6 text-center" x-transition>
            <div class="w-24 h-24 bg-[#3B65F3] rounded-full flex items-center justify-center shadow-2xl mb-6 scale-up-center ring-8 ring-[#3B65F3]/30">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="font-extrabold text-2xl drop-shadow-md mb-2">Absensi Berhasil!</h3>
            <p class="text-indigo-200 text-sm">Menyimpan data kehadiran Anda...</p>
        </div>
    </div>
    
    @error('foto') <div class="absolute top-24 inset-x-4 bg-rose-500/90 backdrop-blur-sm text-white rounded-xl p-4 shadow-xl z-50 text-center font-medium text-sm">{{ $message }}</div> @enderror
    @error('latitude') <div class="absolute top-24 inset-x-4 bg-rose-500/90 backdrop-blur-sm text-white rounded-xl p-4 shadow-xl z-50 text-center font-medium text-sm">Lokasi GPS tidak valid. Pastikan GPS aktif.</div> @enderror
    @error('keterangan') <div class="absolute top-24 inset-x-4 bg-rose-500/90 backdrop-blur-sm text-white rounded-xl p-4 shadow-xl z-50 text-center font-medium text-sm">{{ $message }}</div> @enderror

    <!-- Load face-api.js from CDN -->
    @assets
    @endassets

    <script type="module">
        document.addEventListener('alpine:init', () => {
        Alpine.data('absensiLiveness', (wire) => ({
            loadingLokasi: true,
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
            
            async init() {
                this.loadingTitle = 'Menyiapkan Sistem...';
                this.loadingSubtitle = 'Mohon tunggu sebentar.';
                
                this.initCamera();
                this.mulaiLacakLokasi();
            },
            
            closeModalAndGoBack() {
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                }
                if (this.detectionInterval) clearInterval(this.detectionInterval);
                window.location.href = "{{ route('guru.dashboard') }}";
            },
            
            mulaiLacakLokasi() {
                this.loadingLokasi = true;
                this.loadingTitle = 'Melacak Lokasi...';
                this.loadingSubtitle = 'Mencari sinyal GPS akurasi tinggi.';
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        async (position) => {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;
                            wire.set('latitude', lat);
                            wire.set('longitude', lon);
                            
                            try {
                                const result = await wire.cekLokasi();
                                this.loadingLokasi = false;
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
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
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
                    this.modelLoading = false; // We use modelLoading flag for initial camera load now
                } catch (err) {
                    console.warn("Kamera depan (user) gagal, mencoba fallback ke kamera default...", err);
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
                        this.videoEl = this.$refs.videoEl;
                        this.videoEl.srcObject = this.stream;
                        
                        await this.videoEl.play();
                        this.modelLoading = false;
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
