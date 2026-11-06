<div>
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $isEdit ? 'Edit Draft Surat Keluar' : 'Buat Draft Surat Keluar' }}</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Draft yang dibuat akan berstatus DRAFT dan tidak memiliki nomor surat. Nomor surat akan di-generate otomatis ketika Kepala Sekolah melakukan Approve.
                </p>
                @if($isEdit && $surat->status->value == 'revisi')
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                    <h4 class="text-sm font-bold text-red-800">Catatan Revisi dari Kepsek:</h4>
                    <p class="text-sm text-red-700 mt-1">{{ $surat->catatan_revisi }}</p>
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
                                <label class="block text-sm font-medium text-gray-700">Balasan Untuk (Opsional)</label>
                                <select wire:model="surat_masuk_id" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- Bukan Surat Balasan --</option>
                                    @foreach($this->suratMasuks as $sm)
                                        <option value="{{ $sm->id }}">{{ $sm->no_agenda }} - {{ \Illuminate\Support\Str::limit($sm->perihal, 30) }}</option>
                                    @endforeach
                                </select>
                                @error('surat_masuk_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Tanggal Pembuatan <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="tanggal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('tanggal') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Tujuan Surat <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="tujuan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Yth. Kepala Dinas Pendidikan...">
                                @error('tujuan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Perihal <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="perihal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Permohonan Bantuan Dana...">
                                @error('perihal') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
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

                            <div class="col-span-6" wire:ignore>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Isi Surat Lengkap <span class="text-red-500">*</span></label>
                                <!-- Quill Editor Container -->
                                <div id="editor-container" class="mt-1" style="height: 300px; border-bottom-left-radius: 0.375rem; border-bottom-right-radius: 0.375rem;">
                                    {!! $isi_ringkas !!}
                                </div>
                                <textarea wire:model="isi_ringkas" id="isi_ringkas" class="hidden"></textarea>
                                @error('isi_ringkas') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Lampiran Tambahan (Opsional)</label>
                                <input type="file" wire:model="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG, DOCX max 5MB. Dokumen pendukung untuk memperjelas draft.</p>
                                
                                <div wire:loading wire:target="file" class="mt-2 text-sm text-blue-600">
                                    Mengunggah file...
                                </div>

                                @if($isEdit && $surat->file_draft_path)
                                    <div class="mt-2 text-sm text-gray-600">
                                        Lampiran saat ini: <a href="{{ \App\Support\Berkas::url($surat->file_draft_path) }}" target="_blank" class="text-indigo-600 underline">Lihat Lampiran</a>
                                    </div>
                                @endif

                                @error('file') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                        <a href="{{ route('administrasi.surat-keluar.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mr-2">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Simpan Draft
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Include Quill via CDN -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            var quill = new Quill('#editor-container', {
                theme: 'snow',
                placeholder: 'Tuliskan isi surat secara lengkap beserta format...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                        ['blockquote', 'code-block'],
                        [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                        [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                        [{ 'direction': 'rtl' }],                         // text direction
                        [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                        [{ 'font': [] }],
                        [{ 'align': [] }],
                        ['clean']                                         // remove formatting button
                    ]
                }
            });

            quill.on('text-change', function(delta, oldDelta, source) {
                // Saat ada perubahan di editor, update model isi_ringkas di Livewire
                @this.set('isi_ringkas', quill.root.innerHTML);
            });
        });
    </script>
</div>
