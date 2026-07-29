<div>
    <x-slot:header>Data Mata Pelajaran</x-slot:header>

    <div class="mb-6 flex items-center justify-between">
        <div class="relative w-64">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kode, nama, kelompok..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-on-surface-variant" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
        
        <button wire:click="create" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-medium hover:bg-primary-container transition-colors flex items-center gap-2">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Mapel
        </button>
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

    <div class="bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-surface-container-low text-on-surface-variant font-medium border-b border-outline-variant">
                    <tr>
                        <th class="px-6 py-4">Kode Mapel</th>
                        <th class="px-6 py-4">Nama Mapel</th>
                        <th class="px-6 py-4">Kelompok</th>
                        <th class="px-6 py-4 text-center">Beban Jam</th>
                        <th class="px-6 py-4">Kurikulum</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($this->mapels as $mapel)
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="px-6 py-4 text-on-surface-variant font-mono">{{ $mapel->kode }}</td>
                        <td class="px-6 py-4 font-medium text-on-surface">{{ $mapel->nama }}</td>
                        <td class="px-6 py-4">
                            @if($mapel->kelompok)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $mapel->kelompok }}
                                </span>
                            @else
                                <span class="text-on-surface-variant">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">{{ $mapel->jam }} Jam</td>
                        <td class="px-6 py-4">{{ $mapel->kurikulum ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $mapel->id }})" class="text-primary hover:text-primary-container font-medium mr-3">Edit</button>
                            <button wire:click="delete({{ $mapel->id }})" wire:swal-confirm="Yakin ingin menghapus mapel ini? Guru yang terkait dengan mapel ini mungkin terpengaruh." class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-on-surface-variant">
                            Tidak ada data mata pelajaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-outline-variant">
            {{ $this->mapels->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-data="{ open: true }">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-black/50 transition-opacity" aria-hidden="true" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false; setTimeout(() => $wire.set('isModalOpen', false), 200)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
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
                                    {{ $editingId ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
                                </h3>
                                <button type="button" @click="open = false; setTimeout(() => $wire.set('isModalOpen', false), 200)" class="text-on-surface-variant hover:text-on-surface transition-colors">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-on-surface mb-1">Nama Mata Pelajaran</label>
                                    <input type="text" wire:model="nama" placeholder="Contoh: Matematika Lanjut" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                    @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-on-surface mb-1">Kelompok Mapel (Opsional)</label>
                                    <input type="text" wire:model="kelompok" placeholder="Contoh: IPA, IPS, Muatan Lokal, Umum" list="kelompok-list" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                    <datalist id="kelompok-list">
                                        <option value="Umum"></option>
                                        <option value="IPA"></option>
                                        <option value="IPS"></option>
                                        <option value="Seni & Prakarya"></option>
                                        <option value="Muatan Lokal"></option>
                                    </datalist>
                                    @error('kelompok') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-on-surface mb-1">Beban Jam (Per Minggu)</label>
                                        <input type="number" wire:model="jam" min="1" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                        @error('jam') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-on-surface mb-1">Kurikulum</label>
                                        <input type="text" wire:model="kurikulum" placeholder="Contoh: Merdeka" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                        @error('kurikulum') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                
                                @if(!$editingId)
                                <div class="p-3 bg-blue-50 text-blue-800 text-xs rounded-lg border border-blue-200 mt-2">
                                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Kode Mapel akan di-*generate* secara otomatis (MPL-xxx) setelah Anda menyimpan data.
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant rounded-b-xl flex justify-end gap-3">
                            <button type="button" @click="open = false; setTimeout(() => $wire.set('isModalOpen', false), 200)" class="px-4 py-2 text-on-surface-variant hover:text-on-surface font-medium transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-medium hover:bg-primary-container transition-colors">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
