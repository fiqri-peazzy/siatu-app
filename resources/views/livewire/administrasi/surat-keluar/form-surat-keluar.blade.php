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
                                <select wire:model="kategori_surat_id" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($this->kategoris as $kat)
                                        <option value="{{ $kat->id }}">{{ $kat->kode }} - {{ $kat->nama }}</option>
                                    @endforeach
                                </select>
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

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Isi Ringkas Surat <span class="text-red-500">*</span></label>
                                <textarea wire:model="isi_ringkas" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Tuliskan draf isi utama surat di sini..."></textarea>
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
                                        Lampiran saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($surat->file_draft_path) }}" target="_blank" class="text-indigo-600 underline">Lihat Lampiran</a>
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
</div>
