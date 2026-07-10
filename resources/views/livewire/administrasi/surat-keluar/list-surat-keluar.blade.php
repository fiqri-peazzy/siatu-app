<div>
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Surat Keluar</h1>
            <p class="mt-2 text-sm text-gray-700">Daftar draft dan pengajuan surat keluar.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            @can('create', \App\Models\SuratKeluar::class)
            <a href="{{ route('administrasi.surat-keluar.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Buat Draft Baru
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari perihal, tujuan..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
            <select wire:model.live="filterKategori" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Semua Kategori</option>
                @foreach($this->kategoris as $kategori)
                    <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
            <select wire:model.live="filterBulan" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Semua Bulan</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
            <select wire:model.live="filterTahun" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Semua Tahun</option>
                @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select wire:model.live="filterStatus" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Semua Status</option>
                @foreach(\App\Enums\StatusSuratKeluar::cases() as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg bg-white">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">No. Surat</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tgl Surat</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tujuan</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Perihal</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kategori</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($this->suratKeluars as $surat)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    {{ $surat->no_surat ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $surat->tanggal->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $surat->tujuan }}</td>
                                <td class="px-3 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $surat->perihal }}">{{ $surat->perihal }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $surat->kategori->nama }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 
                                        @if($surat->status->value == 'draft') bg-gray-100 text-gray-800 
                                        @elseif($surat->status->value == 'menunggu_approval') bg-yellow-100 text-yellow-800 
                                        @elseif($surat->status->value == 'revisi') bg-red-100 text-red-800 
                                        @elseif($surat->status->value == 'approved') bg-blue-100 text-blue-800 
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ $surat->status->label() }}
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="{{ route('administrasi.surat-keluar.show', $surat) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                    @can('update', $surat)
                                    <a href="{{ route('administrasi.surat-keluar.edit', $surat) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-sm text-gray-500">Tidak ada data surat keluar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $this->suratKeluars->links() }}
    </div>
</div>
