@php
    $daftar = $notifications instanceof \Illuminate\Contracts\Pagination\Paginator ? $notifications->getCollection() : collect($notifications);
    $kelompok = $daftar->groupBy(fn ($n) => $n->created_at->isToday() ? 'Hari ini' : ($n->created_at->isYesterday() ? 'Kemarin' : 'Sebelumnya'));
@endphp

<div class="font-body">
    <p class="text-[12px] font-medium text-navy-400">Kotak masuk</p>
    <h1 class="font-heading text-[1.35rem] font-semibold leading-tight text-navy-900">Notifikasi</h1>

    @forelse ($kelompok as $judul => $isi)
        <h2 class="mt-6 mb-2 px-1 text-[12px] font-semibold uppercase tracking-wider text-navy-400">{{ $judul }}</h2>
        <ul class="divide-y divide-ivory-300 overflow-hidden rounded-2xl bg-white ring-1 ring-ivory-300">
            @foreach ($isi as $n)
                @php $d = $n->data; $url = $d['url'] ?? null; @endphp
                <li>
                    <{{ $url ? 'a' : 'div' }} @if ($url) wire:navigate href="{{ $url }}" @endif class="flex gap-3.5 px-4 py-3.5 {{ $url ? 'transition hover:bg-ivory-100' : '' }}">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $n->read_at ? 'bg-ivory-200 text-navy-500' : 'bg-gold-100 text-gold-700' }}">
                            <x-ui.icon :name="($d['icon'] ?? 'bell') === 'bell' ? 'lonceng' : 'info'" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="flex items-baseline justify-between gap-2">
                                <span class="truncate text-[15px] font-semibold text-navy-900">{{ $d['title'] ?? 'Pemberitahuan' }}</span>
                                <time class="shrink-0 text-[11px] text-navy-400" datetime="{{ $n->created_at->toIso8601String() }}">{{ $n->created_at->isToday() ? $n->created_at->format('H:i') : $n->created_at->locale('id')->isoFormat('D MMM') }}</time>
                            </span>
                            <span class="mt-0.5 block text-[13px] leading-relaxed text-navy-600">{{ $d['body'] ?? '' }}</span>
                        </span>
                    </{{ $url ? 'a' : 'div' }}>
                </li>
            @endforeach
        </ul>
    @empty
        <div class="mt-6 rounded-3xl border border-dashed border-ivory-400 bg-ivory-50 px-6 py-12 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-navy-300 ring-1 ring-ivory-300"><x-ui.icon name="lonceng" /></span>
            <h3 class="mt-3 font-heading text-[15px] font-semibold text-navy-800">Belum ada notifikasi</h3>
            <p class="mt-1 text-[13px] text-navy-400">Pengingat jurnal dan info sekolah akan muncul di sini.</p>
        </div>
    @endforelse

    @if ($notifications instanceof \Illuminate\Contracts\Pagination\Paginator && $notifications->hasPages())
        <div class="mt-6">{{ $notifications->links('pagination::simple-tailwind') }}</div>
    @endif
</div>
