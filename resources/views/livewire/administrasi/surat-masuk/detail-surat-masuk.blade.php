<div>
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Surat Masuk</h1>
            <p class="mt-2 text-sm text-gray-700">Detail lengkap surat dan riwayat disposisi.</p>
        </div>
        <div class="mt-4 flex sm:mt-0 sm:space-x-3">
            <a href="{{ route('administrasi.surat-masuk.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                Kembali
            </a>
            @can('dispose', $surat)
            @if($surat->status->value !== 'selesai')
            <button type="button" wire:click="tandaiSelesai" wire:swal-confirm="Yakin ingin menandai disposisi ini selesai / ditindaklanjuti?" class="inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700">
                Tandai Selesai
            </button>
            <button type="button" wire:click="openDisposisiModal" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                Disposisikan
            </button>
            @endif
            @endcan
        </div>
    </div>

    <div class="overflow-hidden bg-white shadow sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi Surat</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Nomor Agenda: <span class="font-bold text-indigo-600">{{ $surat->no_agenda }}</span></p>
            </div>
            <div>
                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold leading-5 
                    @if($surat->status->value == 'baru') bg-gray-100 text-gray-800 
                    @elseif($surat->status->value == 'didisposisi') bg-blue-100 text-blue-800 
                    @elseif($surat->status->value == 'diproses') bg-yellow-100 text-yellow-800 
                    @elseif($surat->status->value == 'selesai') bg-green-100 text-green-800 
                    @else bg-purple-100 text-purple-800 @endif">
                    {{ $surat->status->label() }}
                </span>
            </div>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Kategori Surat</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $surat->kategori->nama }} ({{ $surat->kategori->kode }})</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Klasifikasi Arsip</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        {{ $surat->klasifikasiArsip ? $surat->klasifikasiArsip->kode . ' - ' . $surat->klasifikasiArsip->nama : '-' }}
                    </dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Asal Instansi</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $surat->asal_instansi }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Perihal</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 font-semibold">{{ $surat->perihal }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Tanggal Surat Asli</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $surat->tanggal_surat->format('d F Y') }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Tanggal Terima</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $surat->tanggal_terima->format('d F Y') }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Sifat Surat</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 
                            @if($surat->sifat->value == 'biasa') bg-green-100 text-green-800 
                            @elseif($surat->sifat->value == 'penting') bg-yellow-100 text-yellow-800 
                            @else bg-red-100 text-red-800 @endif">
                            {{ $surat->sifat->label() }}
                        </span>
                    </dd>
                </div>
                
                @if($surat->metadata && count($surat->metadata) > 0)
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6 bg-indigo-50/50">
                    <dt class="text-sm font-medium text-gray-500 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Informasi Spesifik
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        <ul role="list" class="divide-y divide-gray-200 rounded-md border border-gray-200 bg-white">
                            @foreach($surat->metadata as $key => $val)
                            @php
                                $label = $key;
                                if($surat->kategori->config) {
                                    $configField = collect($surat->kategori->config)->firstWhere('name', $key);
                                    if($configField) $label = $configField['label'];
                                }
                            @endphp
                            <li class="flex items-center justify-between py-2 pl-3 pr-4 text-sm">
                                <span class="font-medium text-gray-500 w-1/3">{{ $label }}</span>
                                <span class="text-gray-900 w-2/3">{{ $val ?: '-' }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </dd>
                </div>
                @endif
                
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">File Surat</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        @if($surat->file_path)
                            <ul role="list" class="divide-y divide-gray-200 rounded-md border border-gray-200">
                                <li class="flex items-center justify-between py-3 pl-3 pr-4 text-sm">
                                    <div class="flex w-0 flex-1 items-center">
                                        <svg class="h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M15.621 4.379a3 3 0 00-4.242 0l-7 7a3 3 0 004.241 4.243h.001l.497-.5a.75.75 0 011.064 1.057l-.498.501-.002.002a4.5 4.5 0 01-6.364-6.364l7-7a4.5 4.5 0 016.368 6.36l-3.455 3.553A2.625 2.625 0 119.52 9.52l3.45-3.451a.75.75 0 111.061 1.06l-3.45 3.451a1.125 1.125 0 001.587 1.595l3.454-3.553a3 3 0 000-4.242z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="ml-2 w-0 flex-1 truncate">{{ basename($surat->file_path) }}</span>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('r2')->url($surat->file_path) }}" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500">Lihat File</a>
                                    </div>
                                </li>
                            </ul>
                        @else
                            <span class="text-gray-400 italic">Tidak ada file lampiran</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Riwayat Disposisi Timeline -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Riwayat Disposisi</h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            @if($surat->disposisis->isEmpty())
                <div class="text-center text-gray-500 py-4">Belum ada riwayat disposisi untuk surat ini.</div>
            @else
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($surat->disposisis->sortBy('tgl_disposisi') as $idx => $disposisi)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white 
                                            @if($disposisi->status->value == 'diteruskan') bg-blue-500 
                                            @elseif($disposisi->status->value == 'selesai') bg-green-500
                                            @else bg-gray-400 @endif">
                                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                        <div>
                                            <p class="text-sm text-gray-500">
                                                Disposisi dari <span class="font-medium text-gray-900">{{ $disposisi->dariUser->name }}</span> ke <a href="#" class="font-medium text-gray-900">{{ $disposisi->keUser->name }}</a>
                                            </p>
                                            <div class="mt-2 text-sm text-gray-700 bg-gray-50 rounded-md p-3 border border-gray-100">
                                                "{{ $disposisi->catatan }}"
                                            </div>
                                            <div class="mt-2 text-xs">
                                                Status Disposisi: 
                                                <span class="font-semibold 
                                                    @if($disposisi->status->value == 'selesai') text-green-600 
                                                    @else text-blue-600 @endif">
                                                    {{ $disposisi->status->label() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                            <time datetime="{{ $disposisi->tgl_disposisi }}">{{ $disposisi->tgl_disposisi->format('d M Y H:i') }}</time>
                                            <div class="mt-1 text-xs text-gray-400">Level {{ $disposisi->level }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

    <!-- Modal Disposisi -->
    @if($isDisposisiModalOpen)
    <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <form wire:submit="saveDisposisi">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Teruskan / Disposisikan Surat</h3>
                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Teruskan Kepada <span class="text-red-500">*</span></label>
                                            <select wire:model="ke_user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="">-- Pilih User --</option>
                                                @foreach($this->users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role_label }})</option>
                                                @endforeach
                                            </select>
                                            @error("ke_user_id") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Catatan/Arahan <span class="text-red-500">*</span></label>
                                            <textarea wire:model="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Mohon diproses segera..."></textarea>
                                            @error("catatan") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                Kirim Disposisi
                            </button>
                            <button type="button" wire:click="closeDisposisiModal" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

