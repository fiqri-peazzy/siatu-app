<div>
    <x-slot:header>
        <div class="flex items-center gap-4">
            <a href="/master/siswa" wire:navigate class="text-on-surface-variant hover:text-primary transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            Tambah Data Siswa
        </div>
    </x-slot:header>

    <div class="max-w-7xl mx-auto py-6">
        <form wire:submit="save">
            
            <!-- Data Pribadi & Akademik -->
            <div class="bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-outline-variant bg-surface-container-lowest">
                    <h3 class="text-lg font-medium text-on-surface">Data Pribadi & Akademik</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="state.nama" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow" required>
                        @error('state.nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">NIS</label>
                        <input type="text" wire:model="state.nis" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                        @error('state.nis') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">NISN</label>
                        <input type="text" wire:model="state.nisn" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                        @error('state.nisn') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Kelas Saat Ini</label>
                        <select wire:model="state.kelas_id" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                            <option value="">-- Belum ada kelas --</option>
                            @foreach($this->kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        @error('state.kelas_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Status <span class="text-red-500">*</span></label>
                        <select wire:model="state.status" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow" required>
                            <option value="aktif">Aktif</option>
                            <option value="lulus">Lulus</option>
                            <option value="pindah">Pindah</option>
                            <option value="keluar">Keluar</option>
                        </select>
                        @error('state.status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Jenis Kelamin</label>
                        <select wire:model="state.jk" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Tempat Lahir</label>
                        <input type="text" wire:model="state.tempat_lahir" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Tanggal Lahir</label>
                        <input type="date" wire:model="state.tanggal_lahir" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">NIK</label>
                        <input type="text" wire:model="state.nik" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Agama</label>
                        <input type="text" wire:model="state.agama" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">No HP</label>
                        <input type="text" wire:model="state.hp" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Email</label>
                        <input type="email" wire:model="state.email" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                </div>
            </div>

            <!-- Data Alamat -->
            <div class="bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-outline-variant bg-surface-container-lowest">
                    <h3 class="text-lg font-medium text-on-surface">Data Alamat Tempat Tinggal</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="lg:col-span-4">
                        <label class="block text-sm font-medium text-on-surface mb-1">Alamat Lengkap</label>
                        <textarea wire:model="state.alamat" rows="2" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">RT</label>
                        <input type="text" wire:model="state.rt" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">RW</label>
                        <input type="text" wire:model="state.rw" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Dusun / Lingkungan</label>
                        <input type="text" wire:model="state.dusun" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Kelurahan / Desa</label>
                        <input type="text" wire:model="state.kelurahan" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Kecamatan</label>
                        <input type="text" wire:model="state.kecamatan" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Kode Pos</label>
                        <input type="text" wire:model="state.kode_pos" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Jenis Tinggal</label>
                        <input type="text" wire:model="state.jenis_tinggal" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Alat Transportasi</label>
                        <input type="text" wire:model="state.alat_transportasi" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    </div>
                </div>
            </div>
            
            <!-- Data Orang Tua -->
            <div class="bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-outline-variant bg-surface-container-lowest">
                    <h3 class="text-lg font-medium text-on-surface">Data Orang Tua / Wali</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Data Ayah -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-primary mb-3">Data Ayah</h4>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">Nama Ayah</label>
                                <input type="text" wire:model="state.ayah_nama" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">NIK Ayah</label>
                                <input type="text" wire:model="state.ayah_nik" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">Tahun Lahir</label>
                                <input type="text" wire:model="state.ayah_tahun_lahir" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">Pendidikan</label>
                                <input type="text" wire:model="state.ayah_pendidikan" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">Pekerjaan</label>
                                <input type="text" wire:model="state.ayah_pekerjaan" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                        </div>
                        <!-- Data Ibu -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-primary mb-3">Data Ibu</h4>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">Nama Ibu</label>
                                <input type="text" wire:model="state.ibu_nama" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">NIK Ibu</label>
                                <input type="text" wire:model="state.ibu_nik" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">Tahun Lahir</label>
                                <input type="text" wire:model="state.ibu_tahun_lahir" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">Pendidikan</label>
                                <input type="text" wire:model="state.ibu_pendidikan" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">Pekerjaan</label>
                                <input type="text" wire:model="state.ibu_pekerjaan" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                        </div>
                        <!-- Data Wali -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-primary mb-3">Data Wali (Opsional)</h4>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">Nama Wali</label>
                                <input type="text" wire:model="state.wali_nama" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">NIK Wali</label>
                                <input type="text" wire:model="state.wali_nik" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">Tahun Lahir</label>
                                <input type="text" wire:model="state.wali_tahun_lahir" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-on-surface-variant mb-1">Pekerjaan</label>
                                <input type="text" wire:model="state.wali_pekerjaan" class="w-full px-3 py-2 text-sm rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-end gap-4 mt-6 mb-12">
                <a href="/master/siswa" wire:navigate class="px-6 py-3 border border-outline-variant rounded-lg text-on-surface-variant font-medium hover:bg-surface-container transition-colors">Batal</a>
                <button type="submit" class="px-6 py-3 bg-primary text-on-primary rounded-lg font-medium hover:bg-primary-container transition-colors">
                    Simpan Data Siswa
                </button>
            </div>
        </form>
    </div>
</div>
