<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/30">
        <h2 class="text-xl font-bold text-on-surface mb-2">Pengaturan Titik Lokasi Absensi</h2>
        <p class="text-sm text-on-surface-variant mb-6">Kelola titik koordinat sekolah dan batas radius (dalam meter) bagi guru untuk melakukan absensi.</p>

        <form wire:submit="save" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kiri: Form Input -->
                <div class="space-y-4">
                    
                    <div x-data="{ 
                        loading: false,
                        getLocation() {
                            this.loading = true;
                            if(navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(
                                    (pos) => {
                                        @this.set('lat_sekolah', pos.coords.latitude);
                                        @this.set('lng_sekolah', pos.coords.longitude);
                                        this.loading = false;
                                        // Update marker di peta via event dispatch custom
                                        window.dispatchEvent(new CustomEvent('update-map', { detail: { lat: pos.coords.latitude, lng: pos.coords.longitude } }));
                                        Swal.fire({ toast:true, position: 'top-end', showConfirmButton: false, timer: 3000, icon: 'success', title: 'Lokasi berhasil didapatkan' });
                                    },
                                    (err) => {
                                        this.loading = false;
                                        window.showAppAlert.error('Tidak dapat mengambil lokasi. Pastikan GPS aktif.');
                                    },
                                    { enableHighAccuracy: true }
                                );
                            }
                        }
                    }">
                        <button type="button" @click="getLocation()" :disabled="loading" class="w-full flex justify-center items-center gap-2 px-4 py-2 bg-secondary text-on-secondary rounded-lg font-semibold hover:bg-secondary/90 transition-all shadow-md">
                            <span x-show="!loading"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></span>
                            <span x-show="loading" class="animate-spin"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                            <span x-text="loading ? 'Mengambil GPS...' : 'Ambil Lokasi Saat Ini'"></span>
                        </button>
                    </div>

                    <div class="mt-4 border-t border-outline-variant/30 pt-4">
                        <label class="block text-sm font-semibold text-on-surface mb-2">Latitude (Garis Lintang)</label>
                        <input wire:model.live="lat_sekolah" type="text" class="w-full bg-surface-container-low border-0 border-b-2 border-transparent focus:border-primary focus:ring-0 rounded-md p-3 transition-all" placeholder="-0.1234567">
                        @error('lat_sekolah') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-2">Longitude (Garis Bujur)</label>
                        <input wire:model.live="lng_sekolah" type="text" class="w-full bg-surface-container-low border-0 border-b-2 border-transparent focus:border-primary focus:ring-0 rounded-md p-3 transition-all" placeholder="123.456789">
                        @error('lng_sekolah') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-2">Radius Maksimal (Meter)</label>
                        <input wire:model.live="radius_absen" type="number" class="w-full bg-surface-container-low border-0 border-b-2 border-transparent focus:border-primary focus:ring-0 rounded-md p-3 transition-all" placeholder="50">
                        <p class="text-xs text-on-surface-variant mt-1">Jarak maksimum dari titik koordinat agar guru bisa absen.</p>
                        @error('radius_absen') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Kanan: Preview Leaflet Maps -->
                <div class="h-[400px] bg-surface-container-low rounded-xl border border-outline-variant/30 overflow-hidden relative" wire:ignore>
                    <div id="map-preview" class="w-full h-full z-0"></div>
                </div>
            </div>

            <div class="flex justify-end border-t border-outline-variant/30 pt-6 mt-6">
                <button type="submit" class="px-6 py-2.5 bg-primary text-on-primary font-bold rounded-lg shadow-md hover:bg-primary/90 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- Leaflet CSS & JS -->
    @assets
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endassets

    @script
    <script>
        let map, marker, circle;
        
        // Initialize map
        const initMap = (lat, lng, radius) => {
            // Default to Jakarta if null
            const startLat = lat || -6.200000;
            const startLng = lng || 106.816666;
            
            map = L.map('map-preview').setView([startLat, startLng], 17);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Add Marker
            marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);
            
            // Add Circle for radius
            circle = L.circle([startLat, startLng], {
                color: '#0e2a55',
                fillColor: '#0e2a55',
                fillOpacity: 0.2,
                radius: radius || 50
            }).addTo(map);
            
            // Handle Marker Drag
            marker.on('dragend', function (e) {
                const position = marker.getLatLng();
                $wire.set('lat_sekolah', position.lat);
                $wire.set('lng_sekolah', position.lng);
                circle.setLatLng(position);
            });
            
            // Handle Map Click
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                $wire.set('lat_sekolah', e.latlng.lat);
                $wire.set('lng_sekolah', e.latlng.lng);
                circle.setLatLng(e.latlng);
            });
        };

        // Run on init
        initMap($wire.lat_sekolah, $wire.lng_sekolah, $wire.radius_absen);

        // Custom event when getting location from browser
        window.addEventListener('update-map', (e) => {
            if(marker && circle && map) {
                const newLatLng = new L.LatLng(e.detail.lat, e.detail.lng);
                marker.setLatLng(newLatLng);
                circle.setLatLng(newLatLng);
                map.setView(newLatLng, 17);
            }
        });
        
        // Listen to Livewire property changes (e.g. radius input)
        $wire.watch('radius_absen', value => {
            if(circle) circle.setRadius(value || 50);
        });
        $wire.watch('lat_sekolah', value => {
            if(marker && circle && value) {
                const pos = new L.LatLng(value, marker.getLatLng().lng);
                marker.setLatLng(pos);
                circle.setLatLng(pos);
            }
        });
        $wire.watch('lng_sekolah', value => {
            if(marker && circle && value) {
                const pos = new L.LatLng(marker.getLatLng().lat, value);
                marker.setLatLng(pos);
                circle.setLatLng(pos);
            }
        });
    </script>
    @endscript
</div>
