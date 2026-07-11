<div class="animate-fade-in-up pb-8">
    <!-- User Greeting Card -->
    <div class="bg-white rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-6 border border-slate-100/50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-full -mr-10 -mt-10 opacity-70 z-0"></div>
        
        <div class="relative z-10 flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 mb-1">Selamat datang kembali,</p>
                <h2 class="text-xl font-bold text-slate-800">{{ auth()->user()->name }}</h2>
                <p class="text-xs font-semibold text-indigo-600 mt-1 bg-indigo-50 inline-block px-2 py-0.5 rounded-full">{{ auth()->user()->guru?->jabatan ?? 'Guru' }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-100 flex items-center justify-center shadow-sm">
                <span class="text-xl font-bold text-indigo-600">{{ substr(auth()->user()->name, 0, 1) }}</span>
            </div>
        </div>
        
        <div class="mt-5 bg-slate-50 rounded-2xl p-4 flex items-center justify-between border border-slate-100">
            <div>
                <p class="text-xs text-slate-500 font-medium mb-0.5">Tanggal Hari Ini</p>
                <p class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500 font-medium mb-0.5">Jam Sekarang</p>
                <p class="text-sm font-bold text-indigo-600" x-data="{ time: new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) }" x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}), 1000)" x-text="time"></p>
            </div>
        </div>
    </div>

    <!-- Status Absensi Hari Ini -->
    <div class="grid grid-cols-2 gap-4 mb-8">
        <!-- Card Absen Masuk -->
        <div class="bg-white rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/50 flex flex-col relative overflow-hidden group transition-all duration-300 {{ $absenMasuk ? 'border-indigo-100 bg-gradient-to-b from-white to-indigo-50/30' : '' }}">
            <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-emerald-50 opacity-0 {{ $absenMasuk ? 'opacity-100' : '' }} transition-opacity"></div>
            
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center mb-3 {{ $absenMasuk ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            </div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Absen Masuk</p>
            
            @if($absenMasuk)
                <p class="text-2xl font-bold text-slate-800">{{ \Carbon\Carbon::parse($absenMasuk->jam)->format('H:i') }}</p>
                <div class="mt-2 flex items-center">
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $absenMasuk->status->value === 'hadir' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $absenMasuk->status->value === 'hadir' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        {{ strtoupper($absenMasuk->status->value) }}
                    </span>
                </div>
            @else
                <p class="text-lg font-bold text-slate-400 italic mt-auto">Belum Absen</p>
            @endif
        </div>

        <!-- Card Absen Pulang -->
        <div class="bg-white rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/50 flex flex-col relative overflow-hidden transition-all duration-300 {{ $absenPulang ? 'border-emerald-100 bg-gradient-to-b from-white to-emerald-50/30' : '' }}">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center mb-3 {{ $absenPulang ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Absen Pulang</p>
            
            @if($absenPulang)
                <p class="text-2xl font-bold text-slate-800">{{ \Carbon\Carbon::parse($absenPulang->jam)->format('H:i') }}</p>
                <div class="mt-2 flex items-center">
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        SELESAI
                    </span>
                </div>
            @else
                <p class="text-lg font-bold text-slate-400 italic mt-auto">Belum Absen</p>
            @endif
        </div>
    </div>

    <!-- Action Section -->
    <div class="bg-white rounded-[2rem] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100/50">
        
        @if(!$absenMasuk || !$absenPulang)
            <div class="text-center mb-6">
                <h3 class="text-lg font-bold text-slate-800 mb-1">
                    {{ !$absenMasuk ? 'Absensi Masuk' : 'Absensi Pulang' }}
                </h3>
                <p class="text-sm text-slate-500">Pastikan Anda berada di area sekolah.</p>
            </div>
            
            <div x-data="{ 
                lokasiDitemukan: @entangle('latitude').live ? true : false,
                loadingLokasi: false,
                ambilLokasi() {
                    this.loadingLokasi = true;
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                @this.set('latitude', position.coords.latitude);
                                @this.set('longitude', position.coords.longitude);
                                this.lokasiDitemukan = true;
                                this.loadingLokasi = false;
                            },
                            (error) => {
                                this.loadingLokasi = false;
                                let msg = 'Gagal mendapatkan lokasi.';
                                if (error.code == 1) msg = 'Akses lokasi ditolak. Aktifkan GPS dan izinkan browser mengakses lokasi.';
                                else if (error.code == 2) msg = 'Posisi tidak tersedia.';
                                else if (error.code == 3) msg = 'Waktu pengambilan lokasi habis.';
                                Swal.fire('Error Lokasi', msg, 'error');
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                        );
                    } else {
                        this.loadingLokasi = false;
                        Swal.fire('Error', 'Geolocation tidak didukung browser ini.', 'error');
                    }
                }
            }" class="space-y-6">

                <!-- Location Button -->
                <button type="button" 
                        @click="ambilLokasi()" 
                        x-show="!lokasiDitemukan"
                        :disabled="loadingLokasi"
                        class="w-full relative overflow-hidden rounded-2xl bg-slate-900 text-white p-4 font-bold shadow-lg shadow-slate-900/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 group">
                    
                    <span x-show="!loadingLokasi" class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-400 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Lacak Lokasi Saya
                    </span>

                    <span x-show="loadingLokasi" class="flex items-center gap-2">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Mencari Sinyal GPS...
                    </span>
                </button>

                <!-- Location Found state -->
                <div x-show="lokasiDitemukan" style="display: none;" class="animate-fade-in-up">
                    
                    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 mb-6 flex items-start gap-3">
                        <div class="bg-emerald-100 text-emerald-600 rounded-full p-1.5 shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-emerald-800">Sinyal GPS Terkunci</p>
                            <p class="text-xs text-emerald-600 mt-0.5">Silakan ambil foto selfie untuk memverifikasi kehadiran.</p>
                        </div>
                    </div>
                    
                    @if($pesanErrorLokasi)
                        <div class="bg-rose-50 border border-rose-100 rounded-2xl p-4 mb-6 flex items-start gap-3">
                            <div class="bg-rose-100 text-rose-600 rounded-full p-1.5 shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-rose-800">Lokasi Tidak Valid</p>
                                <p class="text-xs text-rose-600 mt-0.5">{{ $pesanErrorLokasi }}</p>
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="submitAbsen('{{ !$absenMasuk ? 'masuk' : 'pulang' }}')">
                        
                        <!-- Camera Upload Button (Styled as large card) -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Foto Selfie Wajib</label>
                            
                            <div class="relative">
                                <input type="file" wire:model="foto" accept="image/*" capture="user" id="camera-input" class="hidden">
                                <label for="camera-input" class="w-full flex flex-col items-center justify-center p-6 border-2 border-dashed {{ $foto ? 'border-indigo-300 bg-indigo-50/50' : 'border-slate-300 bg-slate-50' }} rounded-3xl cursor-pointer hover:bg-slate-100 transition-colors">
                                    
                                    @if ($foto)
                                        <div class="relative w-full aspect-square max-h-64 rounded-2xl overflow-hidden shadow-md">
                                            <img src="{{ $foto->temporaryUrl() }}" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                <span class="bg-white/90 text-slate-800 px-4 py-2 rounded-full font-semibold text-sm shadow-sm backdrop-blur-sm">Ganti Foto</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mb-3 text-indigo-500">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </div>
                                        <p class="font-bold text-slate-700">Buka Kamera</p>
                                        <p class="text-xs text-slate-500 mt-1 text-center">Pastikan wajah terlihat jelas beserta seragam.</p>
                                    @endif
                                </label>
                            </div>
                            @error('foto') <span class="text-rose-500 font-medium text-xs mt-2 block pl-1 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan (Opsional)</label>
                            <textarea wire:model="keterangan" rows="2" placeholder="Tuliskan keterangan jika perlu..." class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-indigo-500 focus:ring focus:ring-indigo-500/20 transition-all resize-none shadow-sm"></textarea>
                            @error('keterangan') <span class="text-rose-500 font-medium text-xs mt-2 block pl-1 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $message }}</span> @enderror
                        </div>
                        
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="w-full relative overflow-hidden rounded-2xl bg-indigo-600 text-white p-4 font-bold text-lg shadow-lg shadow-indigo-600/30 active:scale-[0.98] transition-all flex items-center justify-center gap-2 group {{ $foto ? '' : 'opacity-70 grayscale' }}">
                            
                            <span wire:loading.remove wire:target="submitAbsen" class="flex items-center gap-2">
                                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                Kirim Data Absensi
                            </span>
                            
                            <span wire:loading wire:target="submitAbsen" class="flex items-center gap-2">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Mengirim ke Server...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
            
        @else
            <div class="text-center py-8">
                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Absensi Selesai!</h3>
                <p class="text-sm text-slate-500">Anda sudah melakukan absen masuk dan pulang untuk hari ini. Selamat beristirahat.</p>
            </div>
        @endif
    </div>
</div>
