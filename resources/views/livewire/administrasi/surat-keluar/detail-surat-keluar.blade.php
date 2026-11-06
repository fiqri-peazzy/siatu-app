<div>
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Surat Keluar</h1>
            <p class="mt-2 text-sm text-gray-700">Informasi lengkap pengajuan surat keluar.</p>
        </div>
        <div class="mt-4 flex sm:mt-0 sm:space-x-3">
            <a href="{{ route('administrasi.surat-keluar.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                Kembali
            </a>
            
            @if(in_array($surat->status->value, ['draft', 'revisi']) && auth()->user()->can('update', $surat))
                <button type="button" wire:click="ajukanApproval" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                    Ajukan Approval
                </button>
            @endif

            @if($surat->status->value == 'menunggu_approval' && auth()->user()->can('approve', $surat))
                <button type="button" wire:click="openRejectModal" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50">
                    Reject (Revisi)
                </button>
                <button type="button" wire:click="approve" class="inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700">
                    Approve Surat
                </button>
            @endif
        </div>
    </div>

    @if(session()->has('success'))
    <div class="rounded-md bg-green-50 p-4 mb-6">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if($surat->status->value == 'revisi' && $surat->catatan_revisi)
    <div class="rounded-md bg-red-50 p-4 mb-6 border border-red-200">
        <div class="flex">
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Surat Dikembalikan untuk Revisi</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p><strong>Catatan Kepala Sekolah:</strong> {{ $surat->catatan_revisi }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="overflow-hidden bg-white shadow sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi Surat</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Nomor Surat: 
                    <span class="font-bold {{ $surat->no_surat ? 'text-indigo-600' : 'text-gray-400 italic' }}">
                        {{ $surat->no_surat ?? 'Belum Di-generate (Masih Draft)' }}
                    </span>
                </p>
            </div>
            <div>
                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold leading-5 
                    @if($surat->status->value == 'draft') bg-gray-100 text-gray-800 
                    @elseif($surat->status->value == 'menunggu_approval') bg-yellow-100 text-yellow-800 
                    @elseif($surat->status->value == 'revisi') bg-red-100 text-red-800 
                    @elseif($surat->status->value == 'approved') bg-blue-100 text-blue-800 
                    @else bg-green-100 text-green-800 @endif">
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
                    <dt class="text-sm font-medium text-gray-500">Tujuan Surat</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 font-semibold">{{ $surat->tujuan }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Perihal</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 font-semibold">{{ $surat->perihal }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Isi Ringkas</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 prose prose-sm max-w-none">{!! $surat->isi_ringkas !!}</dd>
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
                    <dt class="text-sm font-medium text-gray-500">Tanggal Pembuatan</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $surat->tanggal->format('d F Y') }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Balasan Untuk Surat Masuk</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        @if($surat->balasanDari)
                            <a href="{{ route('administrasi.surat-masuk.show', $surat->balasanDari) }}" class="text-indigo-600 hover:underline">
                                {{ $surat->balasanDari->no_agenda }} - {{ $surat->balasanDari->perihal }}
                            </a>
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $surat->dibuatOleh->name ?? '-' }} (pada {{ $surat->created_at->format('d M Y H:i') }})</dd>
                </div>
                @if($surat->disetujui_oleh)
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Disetujui Oleh</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $surat->disetujuiOleh->name ?? '-' }} (pada {{ \Carbon\Carbon::parse($surat->disetujui_at)->format('d M Y H:i') }})</dd>
                </div>
                @endif
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Lampiran Draft</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        @if($surat->file_draft_path)
                            <a href="{{ \App\Support\Berkas::url($surat->file_draft_path) }}" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500">Download Lampiran Draft</a>
                        @else
                            <span class="text-gray-400 italic">Tidak ada lampiran</span>
                        @endif
                    </dd>
                </div>
                @if($surat->status->value == 'approved' || $surat->status->value == 'terkirim')
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6 bg-blue-50">
                    <dt class="text-sm font-medium text-blue-900">Dokumen Resmi</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 flex space-x-4">
                        <a href="{{ route('administrasi.surat-keluar.pdf', $surat) }}" target="_blank" class="font-medium text-red-600 hover:text-red-900 inline-flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Download PDF
                        </a>
                        <a href="{{ route('administrasi.surat-keluar.word', $surat) }}" target="_blank" class="font-medium text-blue-600 hover:text-blue-900 inline-flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Download Word (.docx)
                        </a>
                    </dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Modal Reject -->
    @if($isRejectModalOpen)
    <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <form wire:submit="reject">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Tolak dan Minta Revisi</h3>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700">Catatan Revisi <span class="text-red-500">*</span></label>
                                        <textarea wire:model="catatan_revisi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Tuliskan alasan penolakan atau perbaikan yang diperlukan..."></textarea>
                                        @error("catatan_revisi") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                Tolak Surat
                            </button>
                            <button type="button" wire:click="closeRejectModal" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
