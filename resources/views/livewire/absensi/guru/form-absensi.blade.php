<div>
    <x-slot name="header">
        Absensi Guru
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        
        <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow">
            <h2 class="text-xl font-bold tracking-tight text-on-surface mb-2">Halo, {{ auth()->user()->name }}</h2>
            <p class="text-on-surface-variant text-sm mb-6">Silakan lakukan absensi harian Anda di sini. Pastikan Anda berada di area sekolah.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Status Masuk -->
                <div class="border rounded-lg p-4 {{ $absenMasuk ? 'bg-green-50 border-green-200' : 'bg-surface border-outline-variant' }}">
                    <h3 class="font-semibold text-sm uppercase tracking-widest text-on-surface-variant mb-1">Absen Masuk</h3>
                    @if($absenMasuk)
                        <p class="text-2xl font-bold text-green-700">{{ \Carbon\Carbon::parse($absenMasuk->waktu_absen)->format('H:i') }}</p>
                        <p class="text-xs text-green-600 mt-1">Status: {{ ucfirst($absenMasuk->status) }}</p>
                    @else
                        <p class="text-on-surface-variant">Belum Absen</p>
                    @endif
                </div>

                <!-- Status Pulang -->
                <div class="border rounded-lg p-4 {{ $absenPulang ? 'bg-green-50 border-green-200' : 'bg-surface border-outline-variant' }}">
                    <h3 class="font-semibold text-sm uppercase tracking-widest text-on-surface-variant mb-1">Absen Pulang</h3>
                    @if($absenPulang)
                        <p class="text-2xl font-bold text-green-700">{{ \Carbon\Carbon::parse($absenPulang->waktu_absen)->format('H:i') }}</p>
                    @else
                        <p class="text-on-surface-variant">Belum Absen</p>
                    @endif
                </div>
            </div>
        </div>

        @if(!$absenMasuk || !$absenPulang)
        <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow"
             x-data="{ 
                lokasiDitemukan: false, 
                mencariLokasi: false,
                errorMessage: '',
                ambilLokasi() {
                    this.mencariLokasi = true;
                    this.errorMessage = '';
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                @this.set('latitude', position.coords.latitude);
                                @this.set('longitude', position.coords.longitude);
                                this.lokasiDitemukan = true;
                                this.mencariLokasi = false;
                            },
                            (error) => {
                                this.mencariLokasi = false;
                                switch(error.code) {
                                    case error.PERMISSION_DENIED:
                                        this.errorMessage = 'Izin lokasi ditolak. Aktifkan GPS Anda.';
                                        break;
                                    case error.POSITION_UNAVAILABLE:
                                        this.errorMessage = 'Informasi lokasi tidak tersedia.';
                                        break;
                                    case error.TIMEOUT:
                                        this.errorMessage = 'Waktu permintaan lokasi habis.';
                                        break;
                                }
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                        );
                    } else {
                        this.mencariLokasi = false;
                        this.errorMessage = 'Browser ini tidak mendukung Geolocation.';
                    }
                }
             }"
             x-init="ambilLokasi()">
            
            <h3 class="font-semibold text-lg text-on-surface mb-4">Form Absensi</h3>
            
            <div x-show="errorMessage" class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-4 text-sm" style="display: none;">
                <span x-text="errorMessage"></span>
                <button type="button" @click="ambilLokasi()" class="ml-2 underline font-semibold">Coba Lagi</button>
            </div>

            <div x-show="mencariLokasi" class="flex items-center text-primary text-sm mb-4">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sedang mendapatkan lokasi...
            </div>

            <div x-show="lokasiDitemukan" style="display: none;">
                <p class="text-sm text-green-600 mb-4 font-medium flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Lokasi berhasil ditemukan!
                </p>
                
                @if($pesanErrorLokasi)
                    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-4 text-sm">
                        {{ $pesanErrorLokasi }}
                    </div>
                @endif

                <form wire:submit.prevent="submitAbsen('{{ !$absenMasuk ? 'masuk' : 'pulang' }}')">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-on-surface mb-2">Foto Selfie Lokasi (Wajib)</label>
                        <input type="file" wire:model="foto" accept="image/*" capture="user" class="block w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-container file:text-on-primary-container hover:file:bg-primary hover:file:text-white transition">
                        @error('foto') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        
                        <!-- Preview Image -->
                        @if ($foto)
                            <div class="mt-4">
                                <img src="{{ $foto->temporaryUrl() }}" class="rounded-lg max-h-48 object-cover border border-outline-variant">
                            </div>
                        @endif
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-on-surface mb-2">Keterangan (Opsional)</label>
                        <textarea wire:model="keterangan" rows="2" class="w-full rounded-md border-outline bg-surface px-3 py-2 text-on-surface shadow-sm focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm"></textarea>
                        @error('keterangan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div wire:loading wire:target="submitAbsen" class="mb-4 text-sm text-primary font-medium">
                        Sedang memproses absensi...
                    </div>

                    @if(!$absenMasuk)
                        <button type="submit" wire:loading.attr="disabled" class="w-full py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary/90 transition shadow-sm flex justify-center items-center">
                            Absen Masuk Sekarang
                        </button>
                    @elseif(!$absenPulang)
                        <button type="submit" wire:loading.attr="disabled" class="w-full py-3 bg-secondary text-white rounded-lg font-semibold hover:bg-secondary/90 transition shadow-sm flex justify-center items-center">
                            Absen Pulang
                        </button>
                    @endif
                </form>
            </div>
        </div>
        @else
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">
                            Terima kasih! Anda sudah menyelesaikan absensi masuk dan pulang untuk hari ini.
                        </p>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
