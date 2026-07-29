@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-4 py-3 text-sm font-semibold text-primary bg-surface-container-high border-l-4 border-primary transition-all duration-200'
            : 'flex items-center gap-3 px-4 py-3 text-sm font-medium text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-all duration-200 border-l-4 border-transparent';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if (isset($icon))
        <div class="flex-shrink-0 w-5 h-5 flex items-center justify-center">
            {{ $icon }}
        </div>
    @endif
    <span class="truncate whitespace-nowrap transition-opacity duration-200 sidebar-label">
        {{ $slot }}
    </span>
</a>
