<div>
    <x-slot:header>Manajemen Jadwal Pelajaran</x-slot:header>

    <div class="mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="flex gap-4 w-full sm:w-auto">
            <select wire:model.live="filterTahunAjaran" class="rounded-lg px-4 py-2 border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                <option value="">-- Pilih Tahun Ajaran --</option>
                @foreach($this->tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">{{ $ta->nama }} ({{ $ta->semester }})</option>
                @endforeach
            </select>
            <select wire:model.live="filterKelas" class="rounded-lg px-4 py-2 border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                <option value="">-- Pilih Kelas --</option>
                @foreach($this->kelasList as $kelas)
                    <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="flex gap-2">
            <button wire:click="create" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-medium hover:bg-primary-container transition-colors flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Jadwal
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-800 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    @if(!$filterTahunAjaran || !$filterKelas)
        <div class="bg-surface rounded-xl shadow-sm border border-outline-variant p-8 text-center text-on-surface-variant">
            Silakan pilih Tahun Ajaran dan Kelas terlebih dahulu untuk melihat jadwal.
        </div>
    @else
        <div class="bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-center text-sm border-collapse">
                    <thead class="bg-surface-container-low text-on-surface-variant font-medium border-b border-outline-variant">
                        <tr>
                            <th class="px-4 py-3 border-r border-outline-variant w-28 sticky left-0 z-10 bg-surface-container-low">Waktu</th>
                            @foreach($hariList as $hari)
                                <th class="px-4 py-3 border-r border-outline-variant min-w-[240px]">{{ $hari }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @php
                            $timeSlots = \App\Models\Master\MasterJadwalPelajaran::getAllTimeSlots();
                        @endphp
                        @foreach($timeSlots as $index => $slot)
                        <tr class="hover:bg-surface-container-lowest transition-colors">
                            <td class="px-4 py-4 border-r border-outline-variant font-medium text-on-surface bg-surface-container-lowest whitespace-nowrap sticky left-0 z-10">
                                {{ $slot[0] }} - {{ $slot[1] }}
                            </td>
                            @foreach($hariList as $hari)
                                <td class="px-3 py-3 border-r border-outline-variant align-top relative group"
                                    x-data
                                    @dragover.prevent="$el.classList.add('bg-primary/5')"
                                    @dragleave.prevent="$el.classList.remove('bg-primary/5')"
                                    @drop.prevent="$el.classList.remove('bg-primary/5'); let id = event.dataTransfer.getData('text/plain'); if(id) { $wire.duplicateSchedule(id, '{{ $hari }}', '{{ $slot[0] }}', '{{ $slot[1] }}') }"
                                >
                                    @php
                                        // Match events that start at exactly this slot's start time
                                        $jadwalList = $this->jadwals[$hari]->filter(function($j) use ($slot) {
                                            return substr($j->jam_mulai, 0, 5) === $slot[0];
                                        });
                                    @endphp

                                    @if($jadwalList->count() > 0)
                                        <div class="flex flex-col gap-3">
                                            @foreach($jadwalList as $jadwal)
                                                <div draggable="true" @dragstart="event.dataTransfer.setData('text/plain', {{ $jadwal->id }})" class="cursor-move border rounded-xl p-3 text-left relative overflow-hidden hover:shadow-md transition-all 
                                                    {{ $jadwal->kategori_kegiatan === 'pembelajaran' ? 'bg-primary/5 border-primary/30 hover:border-primary/60' : 'bg-secondary/5 border-secondary/30 hover:border-secondary/60' }}" title="Drag untuk menyalin ke jam lain">
                                                    <div class="absolute top-0 left-0 w-1.5 h-full {{ $jadwal->kategori_kegiatan === 'pembelajaran' ? 'bg-primary' : 'bg-secondary' }}"></div>
                                                    <div class="flex justify-between items-start mb-2 gap-2">
                                                        <span class="font-bold {{ $jadwal->kategori_kegiatan === 'pembelajaran' ? 'text-primary' : 'text-secondary' }} text-sm leading-tight" style="word-break: break-word;">
                                                            @if($jadwal->kategori_kegiatan === 'pembelajaran')
                                                                {{ $jadwal->mapel->nama }}
                                                            @else
                                                                {{ $jadwal->keterangan_kegiatan ?: strtoupper($jadwal->kategori_kegiatan) }}
                                                            @endif
                                                        </span>
                                                        <div class="flex gap-1 flex-shrink-0 bg-surface/80 rounded-md shadow-sm border border-outline-variant/30 backdrop-blur-sm">
                                                            <button wire:click="edit({{ $jadwal->id }})" class="text-on-surface-variant hover:text-primary transition-colors p-1.5" title="Edit">
                                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                                </svg>
                                                            </button>
                                                            <button wire:click="delete({{ $jadwal->id }})" wire:swal-confirm="Yakin ingin menghapus jadwal ini?" class="text-on-surface-variant hover:text-red-600 transition-colors p-1.5" title="Hapus">
                                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($jadwal->guru)
                                                    <div class="text-xs text-on-surface-variant flex items-center gap-1.5 mb-1.5 line-clamp-1" title="{{ $jadwal->guru->nama }}">
                                                        <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        {{ $jadwal->guru->nama }}
                                                    </div>
                                                    @endif
                                                    
                                                    @if($jadwal->kelas_id === null)
                                                    <div class="text-xs text-secondary flex items-center gap-1.5 mb-1.5 line-clamp-1">
                                                        <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        </svg>
                                                        Jadwal Global
                                                    </div>
                                                    @endif
                                                    
                                                    <div class="text-xs font-medium text-on-surface/80 flex items-center gap-1.5 bg-surface-container-lowest inline-flex px-2 py-1 rounded">
                                                        <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <button wire:click="create('{{ $hari }}', '{{ $slot[0] }}', '{{ $slot[1] }}')" class="opacity-0 group-hover:opacity-100 absolute inset-0 w-full h-full flex items-center justify-center bg-surface-container-low/40 backdrop-blur-[1px] text-on-surface-variant hover:text-primary hover:bg-primary/5 transition-all z-10 {{ $jadwalList->count() > 0 ? 'hidden' : '' }}" title="Tambah Jadwal">
                                        <svg class="w-8 h-8 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Modal Form (Alpine.js integration) -->
    <div x-data="{ open: @entangle('isModalOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-75" @click="open = false"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-surface rounded-xl text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full relative z-10 border border-outline-variant">
                <form wire:submit="store">
                    <div class="px-6 pt-6 pb-4">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-lg font-medium text-on-surface">
                                {{ $editingId ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Pelajaran' }}
                            </h3>
                            <button type="button" @click="open = false" class="text-on-surface-variant hover:text-on-surface transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Readonly display of Tahun Ajaran and Kelas context -->
                            <div class="p-3 bg-surface-container-lowest rounded-lg border border-outline-variant flex gap-4 text-sm">
                                <div>
                                    <span class="text-on-surface-variant block text-xs">Tahun Ajaran</span>
                                    <span class="font-medium">{{ $this->tahunAjarans->firstWhere('id', $form_tahun_ajaran_id)?->nama ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-on-surface-variant block text-xs">Kelas</span>
                                    <span class="font-medium">{{ $this->kelasList->firstWhere('id', $form_kelas_id)?->nama ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="{{ $form_kategori_kegiatan === 'pembelajaran' ? '' : 'col-span-2' }}">
                                    <label class="block text-sm font-medium text-on-surface mb-1">Hari</label>
                                    <select wire:model.live="form_hari" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                        <option value="">Pilih Hari</option>
                                        @foreach($hariList as $h)
                                            <option value="{{ $h }}">{{ $h }}</option>
                                        @endforeach
                                    </select>
                                    @error('form_hari') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                @if($form_kategori_kegiatan === 'pembelajaran')
                                <div>
                                    <label class="block text-sm font-medium text-on-surface mb-1">Jam Ke</label>
                                    <input type="number" min="1" max="10" wire:model.live="form_jam_ke" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow" placeholder="Contoh: 1">
                                    @error('form_jam_ke') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div wire:ignore>
                                    <label class="block text-sm font-medium text-on-surface mb-1">Jam Mulai</label>
                                    <input type="text" 
                                        x-data="{ value: @entangle('form_jam_mulai') }"
                                        x-init="let fp = flatpickr($el, { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true, defaultDate: value, onChange: function(selectedDates, dateStr) { value = dateStr; } }); $watch('value', val => fp.setDate(val));"
                                        x-model="value"
                                        class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow" 
                                        placeholder="07:00">
                                </div>
                                <div wire:ignore>
                                    <label class="block text-sm font-medium text-on-surface mb-1">Jam Selesai</label>
                                    <input type="text" 
                                        x-data="{ value: @entangle('form_jam_selesai') }"
                                        x-init="let fp = flatpickr($el, { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true, defaultDate: value, onChange: function(selectedDates, dateStr) { value = dateStr; } }); $watch('value', val => fp.setDate(val));"
                                        x-model="value"
                                        class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow"
                                        placeholder="08:30">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mt-1">
                                <div>@error('form_jam_mulai') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror</div>
                                <div>@error('form_jam_selesai') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Kategori Kegiatan</label>
                                <select wire:model.live="form_kategori_kegiatan" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                    <option value="pembelajaran">Pembelajaran (Mata Pelajaran)</option>
                                    <option value="upacara">Upacara Bendera</option>
                                    <option value="istirahat">Istirahat / Shalat Dhuha</option>
                                    <option value="ishoma">ISHOMA</option>
                                    <option value="ekstrakurikuler">Pembinaan Ekstrakurikuler</option>
                                    <option value="pembinaan">Pembinaan Lainnya (ORTOM, dll)</option>
                                </select>
                                @error('form_kategori_kegiatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if(!$editingId && $form_kategori_kegiatan === 'pembelajaran')
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Durasi (Jumlah JP)</label>
                                <select wire:model="form_durasi_jp" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                    <option value="1">1 Jam Pelajaran</option>
                                    <option value="2">2 Jam Pelajaran berurutan</option>
                                    <option value="3">3 Jam Pelajaran berurutan</option>
                                    <option value="4">4 Jam Pelajaran berurutan</option>
                                </select>
                                <p class="text-xs text-on-surface-variant mt-1">Sistem akan otomatis membuat jadwal berurutan sesuai durasi.</p>
                                @error('form_durasi_jp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            @if($form_kategori_kegiatan !== 'pembelajaran')
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Keterangan Kegiatan (Opsional)</label>
                                <input type="text" wire:model="form_keterangan_kegiatan" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow" placeholder="Contoh: Pembinaan Ekstrakurikuler Olahraga">
                                @error('form_keterangan_kegiatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            @if($form_kategori_kegiatan !== 'pembelajaran')
                            <div class="flex items-center mt-2">
                                <input id="is_global" type="checkbox" wire:model="form_is_global" class="w-4 h-4 text-primary bg-surface-container-lowest border-outline-variant rounded focus:ring-primary focus:ring-2">
                                <label for="is_global" class="ml-2 text-sm font-medium text-on-surface">Terapkan Ke Semua Kelas (Jadwal Global)</label>
                            </div>
                            @endif

                            @if($form_kategori_kegiatan === 'pembelajaran')
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Mata Pelajaran</label>
                                <select wire:model="form_mapel_id" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach($this->mapels as $mapel)
                                        <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                                    @endforeach
                                </select>
                                @error('form_mapel_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            @if($form_kategori_kegiatan === 'pembelajaran' || in_array($form_kategori_kegiatan, ['ekstrakurikuler', 'pembinaan']))
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Guru Pengajar / Pembina</label>
                                <select wire:model="form_guru_id" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach($this->gurus as $guru)
                                        <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                    @endforeach
                                </select>
                                @error('form_guru_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            @endif
                            
                            @error('conflict') 
                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant flex justify-end gap-3 rounded-b-xl">
                        <button type="button" @click="open = false" class="px-4 py-2 border border-outline-variant rounded-lg text-on-surface-variant font-medium hover:bg-surface-container transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-medium hover:bg-primary-container transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
