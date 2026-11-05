<div class="animate-fade-in-up pb-8">
    
    <!-- User Greeting Card -->
    <div class="bg-white rounded-[2rem] p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-6 border border-slate-100/50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-[4rem] -mr-10 -mt-10 opacity-70 z-0"></div>
        
        <div class="relative z-10 flex items-start justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Selamat datang kembali</p>
                <h2 class="text-xl font-extrabold text-slate-800">{{ auth()->user()->name }}</h2>
                <p class="text-xs font-semibold text-indigo-600 mt-1 bg-indigo-50 inline-block px-2.5 py-1 rounded-full">{{ auth()->user()->masterTendik?->jabatan?->label() ?? 'Tendik' }}</p>
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
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ \Carbon\Carbon::parse($absenMasuk->waktu_absen)->format('H:i') }}</p>
                <div class="mt-2 flex items-center">
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2.5 py-1 rounded-full {{ $absenMasuk->status === 'hadir' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $absenMasuk->status === 'hadir' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        {{ strtoupper($absenMasuk->status) }}
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
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ \Carbon\Carbon::parse($absenPulang->waktu_absen)->format('H:i') }}</p>
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
                </div>

                @include('livewire.absensi.partials.banner-jam-absen', ['cekWaktu' => $cekWaktu, 'jendela' => $jendela])
                
                <div x-data="absensiLiveness($wire)" class="space-y-6 relative z-10">

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
                        @else
                            @if($dalamRadius)
                            <div class="mb-4">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 pl-1">Catatan Tambahan (Opsional)</label>
                                <textarea id="keterangan-tendik" wire:model="keterangan" rows="2" placeholder="Cth: Shift pagi, lembur dll..." class="w-full rounded-2xl border-0 ring-1 ring-slate-200 bg-white px-5 py-4 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500 transition-shadow resize-none shadow-sm placeholder:text-slate-300"></textarea>
                                @error('keterangan') <span class="text-rose-500 font-bold text-xs mt-2 block pl-1">{{ $message }}</span> @enderror
                            </div>
                            <button type="button" @click="openModal()" class="w-full bg-indigo-600 text-white rounded-full py-4 font-bold text-sm shadow-[0_8px_20px_rgba(99,102,241,0.25)] hover:bg-indigo-700 active:scale-95 transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Lanjutkan ke Presensi Wajah
                            </button>
                            @endif
                        @endif
                        
                        <!-- Face Recognition Liveness Modal (Mobile Responsive) -->
                        <template x-teleport="body">
                            <div x-show="showLivenessModal" style="display: none;" class="fixed inset-0 z-[9999] flex flex-col bg-slate-900 animate-fade-in">
                                <div class="px-6 pt-12 pb-4 flex items-center justify-between z-20 relative bg-gradient-to-b from-slate-900 to-transparent">
                                    <button type="button" @click="closeModal()" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white backdrop-blur-md">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                    <span class="text-white font-bold tracking-widest uppercase text-xs">Verifikasi Liveness</span>
                                    <div class="w-10"></div> <!-- spacer -->
                                </div>
                                
                                <div class="flex-1 relative w-full h-full bg-slate-900 overflow-hidden rounded-t-[3rem] shadow-[0_-10px_40px_rgb(0,0,0,0.5)]">
                                    
                                    <video id="webcam-video" autoplay muted playsinline class="w-full h-full object-cover transform scale-x-[-1]"></video>
                                    <canvas id="overlay-canvas" class="absolute inset-0 w-full h-full z-10"></canvas>
                                    
                                    <!-- Loading AI Overlay -->
                                    <div x-show="modelLoading" class="absolute inset-0 bg-slate-900/90 backdrop-blur-md z-20 flex flex-col items-center justify-center text-white p-6 text-center">
                                        <svg class="animate-spin w-12 h-12 text-indigo-500 mb-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <p class="font-extrabold text-xl mb-2">Memuat AI Model...</p>
                                        <p class="text-sm text-slate-400 max-w-[250px]">Sistem sedang mengkalibrasi neural network wajah Anda.</p>
                                    </div>
    
                                    <!-- Manual Capture Button -->
                                    <div x-show="!modelLoading && !verifikasiBerhasil" class="absolute inset-x-0 bottom-12 z-20 flex flex-col items-center justify-center">
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
    
                                    <!-- Success Overlay -->
                                    <div x-show="verifikasiBerhasil" class="absolute inset-0 bg-emerald-500/90 backdrop-blur-md z-30 flex flex-col items-center justify-center text-white p-6 text-center" x-transition>
                                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-2xl mb-6 scale-up-center">
                                            <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <h3 class="font-extrabold text-3xl drop-shadow-md mb-2">Sempurna!</h3>
                                        <p class="text-emerald-100 font-semibold drop-shadow-md text-lg">Kehadiran terenkripsi dan terkirim.</p>
                                    </div>
                                </div>
                                @error('foto') <span class="absolute top-20 inset-x-0 text-center text-rose-500 font-bold text-sm bg-rose-100/90 py-2">{{ $message }}</span> @enderror
                            </div>
                        </template>
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

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('absensiLiveness', (wire) => ({
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
            } catch (err) {
                console.error(err);
                window.showAppAlert.error('Gagal mengakses kamera depan.');
            }
        },

        triggerSuccessCapture() {
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
    }));
});
</script>
</div>
