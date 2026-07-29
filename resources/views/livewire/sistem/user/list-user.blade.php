<div>
    <!-- Header & Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-on-surface-variant" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, email, nip..." class="w-full pl-10 pr-4 py-2 border border-outline-variant rounded-lg bg-surface text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-shadow">
            </div>
            <select wire:model.live="roleFilter" class="w-full sm:w-48 pl-4 pr-8 py-2 border border-outline-variant rounded-lg bg-surface text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-shadow appearance-none cursor-pointer">
                <option value="">Semua Role</option>
                @foreach($roles as $r)
                    <option value="{{ $r->name }}">{{ ucwords(str_replace('-', ' ', $r->name)) }}</option>
                @endforeach
            </select>
        </div>

        <button wire:click="openModal" class="inline-flex items-center justify-center px-4 py-2 bg-primary hover:bg-primary-container text-on-primary font-medium rounded-lg shadow-sm transition-colors w-full sm:w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Pengguna
        </button>
    </div>

    <!-- Table -->
    <div class="bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-surface-container-low text-on-surface-variant font-medium">
                    <tr>
                        <th scope="col" class="px-6 py-4">Pengguna</th>
                        <th scope="col" class="px-6 py-4">Role</th>
                        <th scope="col" class="px-6 py-4">Terakhir Login</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($users as $u)
                        <tr class="hover:bg-surface-container-lowest transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-on-surface">{{ $u->name }}</div>
                                        <div class="text-xs text-on-surface-variant mt-0.5">{{ $u->email ?? $u->identifier ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $u->hasRole('super-admin') ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $u->hasRole('admin-tu') ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $u->hasRole('guru') ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $u->hasRole('tendik') ? 'bg-teal-100 text-teal-800' : '' }}
                                    {{ $u->hasRole('siswa') ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $u->roles->isEmpty() ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ $u->role_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant">
                                {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Belum pernah' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($u->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-medium">
                                <button wire:click="edit({{ $u->id }})" class="text-primary hover:text-primary-container mr-3 transition-colors">Edit</button>
                                <button wire:click="delete({{ $u->id }})" wire:swal-confirm="Yakin ingin menghapus pengguna ini?" class="text-error hover:text-error/80 transition-colors">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-on-surface-variant">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-12 w-12 text-outline mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <p>Tidak ada data pengguna ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant bg-surface-container-lowest">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form (Create/Edit) -->
    <div x-data="{ open: @entangle('isModalOpen') }" 
         x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true" 
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-black/50 transition-opacity" 
                 @click="open = false"></div>

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
                    <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-lowest rounded-t-xl">
                        <h3 class="text-lg leading-6 font-medium text-on-surface" id="modal-title">
                            {{ $isEditMode ? 'Edit Pengguna' : 'Tambah Pengguna' }}
                        </h3>
                        <button type="button" @click="open = false" class="text-on-surface-variant hover:text-on-surface transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="px-6 py-6 space-y-5">
                        <!-- Nama -->
                        <div>
                            <label class="block text-sm font-medium text-on-surface mb-1">Nama Lengkap <span class="text-error">*</span></label>
                            <input wire:model="name" type="text" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow" placeholder="Masukkan nama lengkap">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-on-surface mb-1">Email</label>
                            <input wire:model="email" type="email" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow" placeholder="Masukkan alamat email">
                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- NIP -->
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">NIP / NUPTK</label>
                                <input wire:model="nip" type="text" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow" placeholder="Hanya untuk Guru/Staf">
                                @error('nip') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- NIS -->
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">NIS / NISN</label>
                                <input wire:model="nis" type="text" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow" placeholder="Hanya untuk Siswa">
                                @error('nis') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Role & Status -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Role Akses <span class="text-error">*</span></label>
                                <select wire:model="role" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow appearance-none cursor-pointer">
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->name }}">{{ ucwords(str_replace('-', ' ', $r->name)) }}</option>
                                    @endforeach
                                </select>
                                @error('role') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-on-surface mb-1">Status Akun <span class="text-error">*</span></label>
                                <select wire:model="is_active" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow appearance-none cursor-pointer">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                                @error('is_active') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="bg-surface-container-low p-4 rounded-lg border border-outline-variant/50">
                            <label class="block text-sm font-medium text-on-surface mb-1">Password {!! $isEditMode ? '<span class="text-xs text-on-surface-variant font-normal">(Kosongkan jika tidak ingin diubah)</span>' : '<span class="text-error">*</span>' !!}</label>
                            <input wire:model="password" type="password" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow" placeholder="Minimal 6 karakter">
                            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                    </div>
                    <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant flex justify-end gap-3 rounded-b-xl">
                        <button type="button" @click="open = false" class="px-4 py-2 border border-outline-variant rounded-lg text-on-surface-variant font-medium hover:bg-surface-container transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-medium hover:bg-primary-container transition-colors">
                            <span wire:loading.remove wire:target="store">Simpan</span>
                            <span wire:loading wire:target="store">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
