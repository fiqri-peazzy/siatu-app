<div>
    <x-slot:header>Data Tahun Ajaran</x-slot:header>

    <div class="mb-6 flex items-center justify-between">
        <div class="relative w-64">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tahun ajaran..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
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
            Tambah Data
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-surface-container-low text-on-surface-variant font-medium border-b border-outline-variant">
                    <tr>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Semester</th>
                        <th class="px-6 py-4">Mulai</th>
                        <th class="px-6 py-4">Selesai</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($this->tahunAjarans as $ta)
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="px-6 py-4 font-medium text-on-surface">{{ $ta->nama }}</td>
                        <td class="px-6 py-4">{{ $ta->semester }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($ta->mulai)->format('d M Y') }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($ta->selesai)->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            @if($ta->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $ta->id }})" class="text-primary hover:text-primary-container font-medium mr-3">Edit</button>
                            <button wire:click="delete({{ $ta->id }})" wire:swal-confirm="Yakin ingin menghapus data ini?" class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-on-surface-variant">
                            Tidak ada data tahun ajaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-outline-variant">
            {{ $this->tahunAjarans->links() }}
        </div>
    </div>

    <!-- Modal Form (Alpine.js integration) -->
    <div x-data="{ open: @entangle('isModalOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
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

            <!-- This element is to trick the browser into centering the modal contents. -->
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
                                {{ $editingId ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}
                            </h3>
                            <button type="button" @click="open = false" class="text-on-surface-variant hover:text-on-surface transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Nama Tahun Ajaran</label>
                                <input type="text" wire:model="nama" placeholder="Contoh: 2023/2024" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Semester</label>
                                <select wire:model="semester" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                                @error('semester') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-on-surface mb-1">Tanggal Mulai</label>
                                    <input type="date" wire:model="mulai" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                    @error('mulai') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-on-surface mb-1">Tanggal Selesai</label>
                                    <input type="date" wire:model="selesai" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                                    @error('selesai') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="flex items-center mt-2 pt-2">
                                <input type="checkbox" id="is_active" wire:model="is_active" class="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary focus:ring-2">
                                <label for="is_active" class="ml-2 block text-sm text-on-surface cursor-pointer select-none">Jadikan Tahun Ajaran Aktif</label>
                            </div>
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
