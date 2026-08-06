<div>
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $isEdit ? 'Edit Surat Masuk' : 'Tambah Surat Masuk' }}</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Gunakan form ini untuk mencatat surat masuk. Nomor agenda akan otomatis di-generate setelah Anda menyimpannya.
                </p>
                @if($isEdit)
                <div class="mt-4 p-4 bg-gray-50 border rounded-md">
                    <p class="text-sm font-semibold text-gray-700">No Agenda Saat Ini:</p>
                    <p class="text-lg text-indigo-600 font-bold">{{ $surat->no_agenda }}</p>
                </div>
                @endif
            </div>
        </div>
        <div class="mt-5 md:col-span-2 md:mt-0">
            <form wire:submit="simpan">
                <div class="shadow sm:overflow-hidden sm:rounded-md">
                    <div class="space-y-6 bg-white px-4 py-5 sm:p-6">
                        
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Kategori Surat <span class="text-red-500">*</span></label>
                                <div x-data="{
                                    open: false,
                                    search: '',
                                    selectedId: @entangle('kategori_surat_id').live,
                                    options: {{ $this->kategoris->map(fn($k) => ['id' => $k->id, 'label' => $k->kode . ' - ' . $k->nama])->toJson() }},
                                    get filteredOptions() {
                                        if (this.search === '') return this.options;
                                        return this.options.filter(option => option.label.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    get selectedLabel() {
                                        const selected = this.options.find(option => option.id == this.selectedId);
                                        return selected ? selected.label : '-- Pilih Kategori --';
                                    },
                                    selectOption(id) {
                                        this.selectedId = id;
                                        this.open = false;
                                        this.search = '';
                                    }
                                }" class="relative mt-1">
                                    <button @click="open = !open" type="button" class="flex w-full items-center justify-between rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                        <span x-text="selectedLabel" class="block truncate"></span>
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    
                                    <div x-show="open" @click.away="open = false" class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm" style="display: none;">
                                        <div class="sticky top-0 bg-white px-3 py-2">
                                            <input x-model="search" type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Cari kategori...">
                                        </div>
                                        <ul tabindex="-1" role="listbox" aria-labelledby="listbox-label">
                                            <template x-for="option in filteredOptions" :key="option.id">
                                                <li @click="selectOption(option.id)" class="text-gray-900 relative cursor-pointer select-none py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white">
                                                    <span x-text="option.label" class="font-normal block truncate"></span>
                                                </li>
                                            </template>
                                            <li x-show="filteredOptions.length === 0" class="text-gray-500 relative cursor-default select-none py-2 pl-3 pr-9">
                                                Tidak ditemukan
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @error('kategori_surat_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Klasifikasi Arsip</label>
                                <select wire:model="klasifikasi_arsip_id" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- Pilih Klasifikasi (Opsional) --</option>
                                    @foreach($this->klasifikasis as $klas)
                                        <option value="{{ $klas->id }}">{{ $klas->kode }} - {{ $klas->nama }}</option>
                                    @endforeach
                                </select>
                                @error('klasifikasi_arsip_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Tanggal Terima <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="tanggal_terima" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('tanggal_terima') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Tanggal Surat (Asli) <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="tanggal_surat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('tanggal_surat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Asal Instansi/Pengirim <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="asal_instansi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Dinas Pendidikan Provinsi...">
                                @error('asal_instansi') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Perihal <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="perihal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Undangan Rapat Koordinasi...">
                                @error('perihal') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Sifat Surat <span class="text-red-500">*</span></label>
                                <select wire:model="sifat" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                    @foreach(\App\Enums\SifatSurat::cases() as $s)
                                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                                    @endforeach
                                </select>
                                @error('sifat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            @if($dynamicConfig && count($dynamicConfig) > 0)
                            <div class="col-span-6 bg-indigo-50/50 p-4 rounded-lg border border-indigo-100">
                                <h4 class="text-sm font-semibold text-indigo-900 mb-4 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Informasi Spesifik: <span class="ml-1 font-normal">{{ $this->kategoris->firstWhere('id', $kategori_surat_id)?->nama }}</span>
                                </h4>
                                <div class="grid grid-cols-6 gap-4">
                                    @foreach($dynamicConfig as $field)
                                        <div class="col-span-6 sm:col-span-3">
                                            <label class="block text-xs font-medium text-gray-700">{{ $field['label'] }} {!! $field['required'] ? '<span class="text-red-500">*</span>' : '' !!}</label>
                                            <input type="{{ $field['type'] }}" wire:model="metadata.{{ $field['name'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Masukkan {{ strtolower($field['label']) }}...">
                                            @error('metadata.'.$field['name']) <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Upload File Surat <span class="text-red-500">{{ $isEdit ? '' : '*' }}</span></label>
                                <div class="mt-1 flex justify-center rounded-md border-2 border-dashed border-gray-300 px-6 pt-5 pb-6">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-medium text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:text-indigo-500">
                                                <span>Upload a file</span>
                                                <input id="file-upload" wire:model="file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PDF, PNG, JPG up to 5MB</p>
                                    </div>
                                </div>
                                
                                <div wire:loading wire:target="file" class="mt-2 text-sm text-blue-600">
                                    Mengunggah file...
                                </div>

                                @if ($file)
                                    <div class="mt-2 text-sm text-green-600">
                                        File terpilih: {{ $file->getClientOriginalName() }}
                                    </div>
                                @elseif($isEdit && $surat->file_path)
                                    <div class="mt-2 text-sm text-gray-600">
                                        File saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::disk('r2')->url($surat->file_path) }}" target="_blank" class="text-indigo-600 underline">Lihat Dokumen</a>
                                    </div>
                                @endif

                                @error('file') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                        <a href="{{ route('administrasi.surat-masuk.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mr-2">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
