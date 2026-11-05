{{-- Panel konten dashboard dengan judul, keterangan, dan slot aksi --}}
@props(['judul', 'keterangan' => null])
<section {{ $attributes->class('flex flex-col rounded-2xl bg-white ring-1 ring-ivory-300') }}>
    <header class="flex flex-wrap items-start justify-between gap-2 border-b border-ivory-300 px-4 py-3.5 sm:px-5">
        <div class="min-w-0">
            <h2 class="font-heading text-[15px] font-semibold text-navy-900">{{ $judul }}</h2>
            @if ($keterangan)<p class="mt-0.5 text-[12px] text-navy-400">{{ $keterangan }}</p>@endif
        </div>
        @isset($aksi)<div class="flex shrink-0 items-center gap-2">{{ $aksi }}</div>@endisset
    </header>
    <div class="flex-1 p-4 sm:p-5">{{ $slot }}</div>
</section>
