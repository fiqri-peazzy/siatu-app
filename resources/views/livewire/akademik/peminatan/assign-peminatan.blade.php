<div>
    @if($viewMode === 'index')
        <x-slot:header>Data Mapel Peminatan</x-slot:header>
        
        <div class="mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex gap-4 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" placeholder="Cari mapel..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-on-surface-variant" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <select wire:model.live="tahun_ajaran_id" class="rounded-lg px-4 py-2 border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }} ({{ $ta->semester }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-surface-container-low text-on-surface-variant font-medium border-b border-outline-variant">
                        <tr>
                            <th class="px-6 py-4 w-16">No</th>
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4">Mata Pelajaran</th>
                            <th class="px-6 py-4">Kelompok</th>
                            <th class="px-6 py-4 text-center">Jumlah Siswa</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse($mapelWithCounts as $index => $mapel)
                        <tr class="hover:bg-surface-container-lowest transition-colors">
                            <td class="px-6 py-4 text-on-surface-variant">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium">{{ $mapel->kode }}</td>
                            <td class="px-6 py-4 font-bold text-primary">{{ $mapel->nama }}</td>
                            <td class="px-6 py-4">{{ $mapel->kelompok ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold {{ $mapel->peminatan_siswas_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $mapel->peminatan_siswas_count }} Siswa
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="manageMapel({{ $mapel->id }})" class="px-3 py-1.5 bg-primary/10 text-primary hover:bg-primary/20 rounded-lg text-sm font-medium transition-colors">
                                    Kelola Peminatan
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-on-surface-variant">
                                Tidak ada data mapel.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <x-slot:header>Kelola Peminatan / Lintas Minat</x-slot:header>
        
        <div class="mb-4">
            <button wire:click="backToIndex" class="flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors font-medium text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Mapel
            </button>
        </div>

        <div class="bg-surface rounded-xl shadow-sm border border-outline-variant p-6 mb-6">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-outline-variant pb-6">
                <div>
                    <h3 class="text-xl font-bold text-primary">{{ $selectedMapel->nama ?? 'Mapel' }}</h3>
                    <p class="text-sm text-on-surface-variant mt-1">Tahun Ajaran: {{ $tahunAjarans->firstWhere('id', $tahun_ajaran_id)->nama ?? '' }} ({{ $tahunAjarans->firstWhere('id', $tahun_ajaran_id)->semester ?? '' }})</p>
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

            <!-- Tabs -->
            <div class="border-b border-outline-variant mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button wire:click="$set('activeTab', 'peserta')" class="{{ $activeTab === 'peserta' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface hover:border-outline-variant' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Daftar Peserta Peminatan
                    </button>
                    <button wire:click="$set('activeTab', 'assign')" class="{{ $activeTab === 'assign' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface hover:border-outline-variant' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Assign Siswa Baru (Bulk)
                    </button>
                </nav>
            </div>

            @if($activeTab === 'peserta')
                <!-- Tab: Daftar Peserta -->
                <div class="border rounded-xl overflow-hidden border-outline-variant">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-surface-container-low text-on-surface-variant font-medium border-b border-outline-variant">
                                <tr>
                                    <th scope="col" class="px-6 py-4 w-16">No</th>
                                    <th scope="col" class="px-6 py-4">NISN / NIS</th>
                                    <th scope="col" class="px-6 py-4">Nama Siswa</th>
                                    <th scope="col" class="px-6 py-4">Kelas Asal</th>
                                    <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant bg-surface">
                                @forelse($pesertaMapel as $index => $siswa)
                                <tr class="hover:bg-surface-container-lowest transition-colors">
                                    <td class="px-6 py-4 text-on-surface-variant">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 text-on-surface-variant">{{ $siswa->nisn ?? '-' }} / {{ $siswa->nis ?? '-' }}</td>
                                    <td class="px-6 py-4 font-medium text-on-surface">{{ $siswa->nama }}</td>
                                    <td class="px-6 py-4 text-on-surface-variant">{{ $siswa->kelas->nama ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="removePeserta({{ $siswa->id }})" class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-on-surface-variant">
                                        Belum ada siswa yang terdaftar di mapel peminatan ini. Buka tab "Assign Siswa Baru" untuk menambahkan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <!-- Tab: Assign Siswa Baru -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-on-surface mb-1">Pilih Kelas Asal Siswa</label>
                    <select wire:model.live="kelas_id" class="w-full md:w-1/2 px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                        <option value="">Pilih Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                </div>

                @if($kelas_id)
                    <div class="mt-6 pt-6 border-t border-outline-variant">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                            <h3 class="text-lg font-medium text-on-surface">Pilih Siswa dari Kelas Ini</h3>
                            <div class="flex gap-2">
                                <button type="button" wire:click="selectAll" class="px-4 py-2 bg-surface-container-high text-on-surface-variant rounded-lg text-sm font-medium hover:bg-surface-container-highest transition-colors">Pilih Semua</button>
                                <button type="button" wire:click="deselectAll" class="px-4 py-2 bg-surface-container-high text-on-surface-variant rounded-lg text-sm font-medium hover:bg-surface-container-highest transition-colors">Batal Pilih Semua</button>
                            </div>
                        </div>

                        <div class="border rounded-xl overflow-hidden border-outline-variant">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead class="bg-surface-container-low text-on-surface-variant font-medium border-b border-outline-variant">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 w-16">Pilih</th>
                                            <th scope="col" class="px-6 py-4">NISN / NIS</th>
                                            <th scope="col" class="px-6 py-4">Nama Siswa</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-outline-variant bg-surface">
                                        @forelse($siswas as $siswa)
                                        <tr class="hover:bg-surface-container-lowest transition-colors">
                                            <td class="px-6 py-4">
                                                <input type="checkbox" wire:model="selectedSiswas" value="{{ $siswa->id }}" class="rounded border-outline-variant text-primary focus:ring-primary bg-surface-container-lowest cursor-pointer h-4 w-4">
                                            </td>
                                            <td class="px-6 py-4 text-on-surface-variant">
                                                {{ $siswa->nisn ?? '-' }} / {{ $siswa->nis ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 font-medium text-on-surface">
                                                {{ $siswa->nama }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-8 text-center text-on-surface-variant">
                                                Tidak ada data siswa di kelas ini.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button wire:click="save" class="px-6 py-2.5 bg-primary text-on-primary rounded-lg font-medium hover:bg-primary-container transition-colors flex items-center gap-2 shadow-sm">
                                Simpan Penugasan
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 text-on-surface-variant bg-surface-container-lowest rounded-lg border border-dashed border-outline-variant">
                        <p class="text-sm font-medium">Pilih kelas di atas untuk melihat dan menugaskan siswa ke mapel ini.</p>
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>
