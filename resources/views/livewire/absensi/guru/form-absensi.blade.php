<div class="animate-fade-in-up pb-8">
    
    <!-- User Greeting Card -->
    <div class="bg-white rounded-[2rem] p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-6 border border-slate-100/50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-[4rem] -mr-10 -mt-10 opacity-70 z-0"></div>
        
        <div class="relative z-10 flex items-start justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Selamat datang kembali</p>
                <h2 class="text-xl font-extrabold text-slate-800">{{ auth()->user()->name }}</h2>
                <p class="text-xs font-semibold text-indigo-600 mt-1 bg-indigo-50 inline-block px-2.5 py-1 rounded-full">{{ auth()->user()->guru?->jabatan ?? 'Guru' }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-100 to-violet-100 flex items-center justify-center shadow-inner">
                <span class="text-xl font-extrabold text-indigo-600">{{ substr(auth()->user()->name, 0, 1) }}</span>
            </div>
        </div>
        
        <div class="mt-6 bg-slate-50/80 rounded-[1.25rem] p-4 flex items-center justify-between border border-slate-100/80 backdrop-blur-sm">
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Hari Ini</p>
                <p class="text-sm font-bold text-slate-700">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Jam Aktif</p>
                <p class="text-sm font-extrabold text-indigo-600" x-data="{ time: new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit', second:'2-digit'}) }" x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit', second:'2-digit'}), 1000)" x-text="time"></p>
            </div>
        </div>
    </div>

    <!-- Status Absensi Hari Ini -->
    <div class="grid grid-cols-2 gap-4 mb-8">
        <!-- Card Absen Masuk -->
        <div class="bg-white rounded-[2rem] p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/50 flex flex-col relative overflow-hidden transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] {{ $absenMasuk ? 'border-indigo-100 bg-gradient-to-b from-white to-indigo-50/40' : '' }}">
            @if($absenMasuk) <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-emerald-50 opacity-80 z-0"></div> @endif
            
            <div class="relative z-10 w-10 h-10 rounded-[1.15rem] flex items-center justify-center mb-4 {{ $absenMasuk ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-50 text-slate-400 border border-slate-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Absen Masuk</p>
            
            @if($absenMasuk)
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ \Carbon\Carbon::parse($absenMasuk->jam)->format('H:i') }}</p>
                <div class="mt-2 flex items-center">
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2.5 py-1 rounded-full {{ $absenMasuk->status->value === 'hadir' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $absenMasuk->status->value === 'hadir' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        {{ strtoupper($absenMasuk->status->value) }}
                    </span>
                </div>
            @else
                <p class="text-lg font-bold text-slate-300 italic mt-auto tracking-tight">Belum Absen</p>
            @endif
        </div>

        <!-- Card Absen Pulang -->
        <div class="bg-white rounded-[2rem] p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/50 flex flex-col relative overflow-hidden transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] {{ $absenPulang ? 'border-emerald-100 bg-gradient-to-b from-white to-emerald-50/40' : '' }}">
            <div class="relative z-10 w-10 h-10 rounded-[1.15rem] flex items-center justify-center mb-4 {{ $absenPulang ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-50 text-slate-400 border border-slate-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Absen Pulang</p>
            
            @if($absenPulang)
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ \Carbon\Carbon::parse($absenPulang->jam)->format('H:i') }}</p>
                <div class="mt-2 flex items-center">
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        SELESAI
                    </span>
                </div>
            @else
                <p class="text-lg font-bold text-slate-300 italic mt-auto tracking-tight">Belum Absen</p>
            @endif
        </div>
    </div>

    <!-- Live Liveness & Geolocation Section -->
    <div class="bg-white rounded-[2.5rem] p-2 ring-1 ring-slate-100 shadow-[0_8px_40px_rgb(0,0,0,0.06)]">
        <div class="bg-slate-50/50 rounded-[calc(2.5rem-8px)] p-6 sm:p-8 relative overflow-hidden">
            
            @if(!$absenMasuk || !$absenPulang)
                <div class="text-center mb-8">
                    <span class="rounded-full px-3 py-1 text-[9px] uppercase tracking-[0.2em] font-bold text-indigo-500 bg-indigo-50 ring-1 ring-indigo-500/20 mb-3 inline-block">Proses Autentikasi</span>
                    <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight mb-2">
                        {{ !$absenMasuk ? 'Absensi Masuk' : 'Absensi Pulang' }}
                    </h3>
                    <p class="text-sm text-slate-500 font-medium">Sistem memerlukan akses lokasi akurat dan verifikasi wajah (*liveness detection*).</p>
                </div>
                
                <div x-data="absensiLiveness()" x-init="initData()" class="space-y-6 relative z-10">

                    <!-- Location Tracking State -->
                    <div x-show="!lokasiDitemukan" class="flex flex-col items-center justify-center p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm text-center">
                        <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mb-4 text-indigo-500 relative">
                            <span x-show="loadingLokasi" class="absolute inset-0 rounded-full border-2 border-indigo-200 border-t-indigo-500 animate-spin"></span>
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        </div>
                        <h4 class="font-bold text-slate-800 text-lg mb-1" x-text="loadingLokasi ? 'Mencari Sinyal GPS...' : 'GPS Belum Aktif'"></h4>
                        <p class="text-xs text-slate-500 mb-6" x-text="loadingLokasi ? 'Mohon tunggu, memastikan Anda di area sekolah.' : 'Tekan tombol di bawah untuk melacak lokasi Anda.'"></p>
                        
                        <button type="button" @click="mulaiLacakLokasi()" x-show="!loadingLokasi"
                                class="w-full bg-slate-900 text-white rounded-full py-3.5 font-bold text-sm shadow-[0_8px_20px_rgb(0,0,0,0.15)] active:scale-95 transition-all">
                            Lacak Lokasi Sekarang
                        </button>
                    </div>

                    <!-- Geolocation Success -->
                    <div x-show="lokasiDitemukan" style="display: none;" class="animate-fade-in-up">
                        <div class="bg-white rounded-[1.5rem] p-4 mb-6 border border-emerald-100 shadow-sm flex flex-col gap-3">
                            <div class="flex items-start gap-3 border-b border-slate-100 pb-3">
                                <div class="bg-emerald-100 text-emerald-600 rounded-[1rem] w-10 h-10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-emerald-800">Sinyal GPS Terkunci</p>
                                    <p class="text-[11px] text-emerald-600/80 font-medium mt-0.5 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Akurasi Tinggi
                                    </p>
                                </div>
                            </div>
                            <div class="px-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Estimasi Lokasi Anda</p>
                                <p class="text-xs font-semibold text-slate-700 leading-relaxed" x-text="alamatDetail">Memuat alamat rinci...</p>
                            </div>
                        </div>
                        
                        @if($pesanErrorLokasi)
                            <div class="bg-rose-50 border border-rose-100 rounded-[1.5rem] p-4 mb-6 flex items-start gap-3">
                                <div class="bg-rose-100 text-rose-600 rounded-[1rem] w-10 h-10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-rose-800">Di Luar Area</p>
                                    <p class="text-xs text-rose-600 mt-0.5 font-medium">{{ $pesanErrorLokasi }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Face Recognition Liveness Camera -->
                        <div class="mb-6 relative">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 pl-1">Verifikasi Wajah (Liveness)</label>
                            
                            <div class="relative w-full aspect-[3/4] max-h-[400px] bg-slate-900 rounded-[2rem] overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.15)] ring-1 ring-slate-900/5">
                                
                                <video id="webcam-video" autoplay muted playsinline class="w-full h-full object-cover transform scale-x-[-1]"></video>
                                <canvas id="overlay-canvas" class="absolute inset-0 w-full h-full z-10"></canvas>
                                
                                <!-- Loading AI Overlay -->
                                <div x-show="modelLoading" class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center text-white p-6 text-center">
                                    <svg class="animate-spin w-10 h-10 text-indigo-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <p class="font-bold text-lg">Memuat AI Model...</p>
                                    <p class="text-xs text-slate-300 mt-2 opacity-80">Harap tunggu, sistem sedang memuat neural network untuk deteksi kedipan mata.</p>
                                </div>

                                <!-- UI Guidance Overlay -->
                                <div x-show="!modelLoading && !verifikasiBerhasil" class="absolute inset-x-0 bottom-8 z-20 flex flex-col items-center justify-center pointer-events-none">
                                    <div class="bg-black/50 backdrop-blur-md text-white px-5 py-2.5 rounded-full font-semibold text-sm border border-white/10 shadow-lg text-center mx-4" x-transition>
                                        <span x-show="!faceDetected">Arahkan wajah ke kamera</span>
                                        <span x-show="faceDetected && !blinkDetected" class="text-indigo-200">Wajah terdeteksi. Silakan berkedip!</span>
                                    </div>
                                    <!-- Scan Line -->
                                    <div x-show="faceDetected && !blinkDetected" class="w-3/4 h-0.5 bg-indigo-500 mt-4 rounded-full shadow-[0_0_10px_#6366f1] animate-[pulse_1s_ease-in-out_infinite]"></div>
                                </div>

                                <!-- Success Overlay -->
                                <div x-show="verifikasiBerhasil" class="absolute inset-0 bg-emerald-500/20 backdrop-blur-sm z-30 flex flex-col items-center justify-center text-white p-6 text-center" x-transition>
                                    <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg mb-4">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <h3 class="font-bold text-xl text-emerald-50 drop-shadow-md">Verifikasi Berhasil</h3>
                                    <p class="text-sm text-emerald-100 font-medium drop-shadow-md mt-1">Mengirim data kehadiran...</p>
                                </div>
                            </div>
                            @error('foto') <span class="text-rose-500 font-bold text-xs mt-3 block pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 pl-1">Catatan Tambahan (Opsional)</label>
                            <textarea wire:model="keterangan" rows="2" placeholder="Cth: Lupa bawa jas lab..." class="w-full rounded-2xl border-0 ring-1 ring-slate-200 bg-white px-5 py-4 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500 transition-shadow resize-none shadow-sm placeholder:text-slate-300"></textarea>
                            @error('keterangan') <span class="text-rose-500 font-bold text-xs mt-2 block pl-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                
            @else
                <div class="text-center py-10">
                    <div class="w-24 h-24 bg-emerald-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-inner ring-1 ring-emerald-100/50">
                        <svg class="w-10 h-10 text-emerald-500 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-slate-800 mb-2 tracking-tight">Tugas Selesai!</h3>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed max-w-[250px] mx-auto">Anda sudah melengkapi absensi masuk dan pulang hari ini. Selamat beristirahat.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Load face-api.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.min.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('absensiLiveness', () => ({
        lokasiDitemukan: @entangle('latitude').live ? true : false,
        loadingLokasi: false,
        alamatDetail: 'Sedang melacak alamat...',
        
        // Face AI States
        modelLoading: true,
        faceDetected: false,
        blinkDetected: false,
        verifikasiBerhasil: false,
        stream: null,
        videoEl: null,
        detectionInterval: null,
        blinkHistory: [],

        initData() {
            if (this.lokasiDitemukan) {
                // If livewire already has lat/lon, fetch address and start camera
                this.fetchAddress(@this.get('latitude'), @this.get('longitude'));
                this.initFaceApi();
            }
        },
        
        mulaiLacakLokasi() {
            this.loadingLokasi = true;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        @this.set('latitude', lat);
                        @this.set('longitude', lon);
                        this.lokasiDitemukan = true;
                        this.loadingLokasi = false;
                        
                        this.fetchAddress(lat, lon);
                        this.initFaceApi();
                    },
                    (error) => {
                        this.loadingLokasi = false;
                        let msg = 'Gagal mendapatkan lokasi.';
                        if (error.code == 1) msg = 'Akses lokasi ditolak. Aktifkan GPS dan izinkan browser mengakses lokasi.';
                        else if (error.code == 2) msg = 'Posisi tidak tersedia.';
                        else if (error.code == 3) msg = 'Waktu habis.';
                        Swal.fire('Error GPS', msg, 'error');
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                this.loadingLokasi = false;
                Swal.fire('Error', 'Perangkat tidak mendukung GPS.', 'error');
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

        async initFaceApi() {
            this.videoEl = document.getElementById('webcam-video');
            try {
                // Load tiny face detector and landmarks models from CDN
                const modelUrl = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(modelUrl),
                    faceapi.nets.faceLandmark68Net.loadFromUri(modelUrl)
                ]);
                this.modelLoading = false;
                this.startVideo();
            } catch (err) {
                console.error(err);
                Swal.fire('Error AI', 'Gagal memuat model deteksi wajah. Periksa koneksi internet.', 'error');
            }
        },

        async startVideo() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
                });
                this.videoEl.srcObject = this.stream;
                
                this.videoEl.addEventListener('play', () => {
                    this.startDetection();
                });
            } catch (err) {
                console.error(err);
                Swal.fire('Error Kamera', 'Gagal mengakses kamera depan.', 'error');
            }
        },

        startDetection() {
            const canvas = document.getElementById('overlay-canvas');
            const displaySize = { width: this.videoEl.clientWidth, height: this.videoEl.clientHeight };
            faceapi.matchDimensions(canvas, displaySize);

            this.detectionInterval = setInterval(async () => {
                if (this.verifikasiBerhasil) return clearInterval(this.detectionInterval);

                const detections = await faceapi.detectSingleFace(this.videoEl, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks();

                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                if (detections) {
                    this.faceDetected = true;
                    
                    // Draw box slightly to show it's tracking
                    const resizedDetections = faceapi.resizeResults(detections, displaySize);
                    
                    // Optional: draw mesh. faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
                    // Draw a custom box
                    const box = resizedDetections.detection.box;
                    ctx.strokeStyle = '#6366f1'; // Indigo-500
                    ctx.lineWidth = 2;
                    ctx.strokeRect(box.x, box.y, box.width, box.height);

                    // Liveness Detection (Blink - Eye Aspect Ratio)
                    const landmarks = detections.landmarks;
                    const leftEye = landmarks.getLeftEye();
                    const rightEye = landmarks.getRightEye();
                    
                    const leftEAR = this.calculateEAR(leftEye);
                    const rightEAR = this.calculateEAR(rightEye);
                    const avgEAR = (leftEAR + rightEAR) / 2;

                    // Push to history
                    this.blinkHistory.push(avgEAR);
                    if (this.blinkHistory.length > 5) this.blinkHistory.shift();

                    // Detect blink (drops below 0.28 and then goes up)
                    // Loosened threshold for easier detection on mobile cameras
                    const isBlinking = avgEAR < 0.28;
                    const wasNotBlinking = this.blinkHistory[0] > 0.30;

                    if (isBlinking && wasNotBlinking) {
                        this.triggerSuccessCapture();
                    }

                } else {
                    this.faceDetected = false;
                    // Reset history if face is lost
                    this.blinkHistory = [];
                }
            }, 80);
        },

        calculateEAR(eye) {
            // Euclidean distances
            const v1 = Math.hypot(eye[1].x - eye[5].x, eye[1].y - eye[5].y);
            const v2 = Math.hypot(eye[2].x - eye[4].x, eye[2].y - eye[4].y);
            const h = Math.hypot(eye[0].x - eye[3].x, eye[0].y - eye[3].y);
            return (v1 + v2) / (2.0 * h);
        },

        triggerSuccessCapture() {
            this.blinkDetected = true;
            this.verifikasiBerhasil = true;
            clearInterval(this.detectionInterval);
            
            // Capture frame from video
            const canvas = document.createElement('canvas');
            canvas.width = this.videoEl.videoWidth;
            canvas.height = this.videoEl.videoHeight;
            const ctx = canvas.getContext('2d');
            
            // Draw flipped because stream is mirrored
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(this.videoEl, 0, 0, canvas.width, canvas.height);
            
            const base64Image = canvas.toDataURL('image/jpeg', 0.8);
            
            // Stop camera
            this.stream.getTracks().forEach(track => track.stop());
            
            // Send to Livewire
            @this.set('foto', base64Image);
            
            // Wait a tiny bit for UI update, then submit
            setTimeout(() => {
                @this.call('submitAbsen', '{{ !$absenMasuk ? 'masuk' : 'pulang' }}');
            }, 500);
        }
    }));
});
</script>
