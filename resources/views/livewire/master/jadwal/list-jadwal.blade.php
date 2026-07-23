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
                            <th class="px-4 py-3 border-r border-outline-variant w-20">Jam Ke</th>
                            @foreach($hariList as $hari)
                                <th class="px-4 py-3 border-r border-outline-variant min-w-[200px]">{{ $hari }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @php
                            $maxJam = 10;
                        @endphp
                        @for($i = 1; $i <= $maxJam; $i++)
                        <tr class="hover:bg-surface-container-lowest transition-colors">
                            <td class="px-4 py-4 border-r border-outline-variant font-bold text-on-surface bg-surface-container-lowest">
                                {{ $i }}
                            </td>
                            @foreach($hariList as $hari)
                                <td class="px-2 py-2 border-r border-outline-variant align-top relative group">
                                    @php
                                        $jadwalList = $this->jadwals[$hari]->where('jam_ke', $i);
                                    @endphp

                                    @if($jadwalList->count() > 0)
                                        @foreach($jadwalList as $jadwal)
                                            <div class="bg-primary/10 border border-primary/20 rounded-lg p-3 text-left relative overflow-hidden mb-2 last:mb-0 hover:shadow-md transition-shadow">
                                                <div class="absolute top-0 left-0 w-1 h-full bg-primary"></div>
                                                <div class="flex justify-between items-start mb-1">
                                                    <span class="font-bold text-primary text-sm line-clamp-1">{{ $jadwal->mapel->nama }}</span>
                                                    <div class="flex gap-1">
                                                        <button wire:click="edit({{ $jadwal->id }})" class="text-on-surface-variant hover:text-primary transition-colors p-1" title="Edit">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                            </svg>
                                                        </button>
                                                        <button wire:click="delete({{ $jadwal->id }})" wire:confirm="Yakin ingin menghapus jadwal ini?" class="text-on-surface-variant hover:text-red-600 transition-colors p-1" title="Hapus">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="text-xs text-on-surface-variant flex items-center gap-1 mb-1 line-clamp-1" title="{{ $jadwal->guru->nama }}">
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    {{ $jadwal->guru->nama }}
                                                </div>
                                                <div class="text-xs font-medium text-on-surface flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-on-surface-variant flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    <button wire:click="create('{{ $hari }}', {{ $i }})" class="opacity-0 group-hover:opacity-100 absolute inset-0 w-full h-full flex items-center justify-center bg-surface-container-low/50 text-on-surface-variant hover:text-primary hover:bg-primary/5 transition-all {{ $jadwalList->count() > 0 ? 'hidden' : '' }}" title="Tambah Jadwal">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </td>
                            @endforeach
                        </tr>
                        @endfor
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
                                <div>
                                    <label class="block text-sm font-medium text-on-surface mb-1">Hari</label>
                                    <select wire:model="form_hari" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                        @foreach($hariList as $hari)
                                            <option value="{{ $hari }}">{{ $hari }}</option>
                                        @endforeach
                                    </select>
                                    @error('form_hari') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-on-surface mb-1">Jam Ke</label>
                                    <input type="number" min="1" max="10" wire:model="form_jam_ke" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                    @error('form_jam_ke') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div wire:ignore>
                                    <label class="block text-sm font-medium text-on-surface mb-1">Jam Mulai</label>
                                    <input type="text" 
                                        x-data="{ value: @entangle('form_jam_mulai') }"
                                        x-init="flatpickr($el, { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true, defaultDate: value, onChange: function(selectedDates, dateStr) { value = dateStr; } })"
                                        x-model="value"
                                        class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow" 
                                        placeholder="07:00">
                                </div>
                                <div wire:ignore>
                                    <label class="block text-sm font-medium text-on-surface mb-1">Jam Selesai</label>
                                    <input type="text" 
                                        x-data="{ value: @entangle('form_jam_selesai') }"
                                        x-init="flatpickr($el, { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true, defaultDate: value, onChange: function(selectedDates, dateStr) { value = dateStr; } })"
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
                                <label class="block text-sm font-medium text-on-surface mb-1">Mata Pelajaran</label>
                                <select wire:model="form_mapel_id" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach($this->mapels as $mapel)
                                        <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                                    @endforeach
                                </select>
                                @error('form_mapel_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Guru Pengajar</label>
                                <select wire:model="form_guru_id" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach($this->gurus as $guru)
                                        <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                    @endforeach
                                </select>
                                @error('form_guru_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
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
