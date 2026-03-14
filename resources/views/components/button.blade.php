@props([
    'variant' => 'primary',
    'type' => 'submit',
    'href' => null,
    'size' => 'md',
])

@php
    $base = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';

    $sizes = [
        'xs' => 'px-2.5 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-2.5 text-sm',
    ];

    $variants = [
        'primary'   => 'bg-blue-800 text-white hover:bg-blue-900 focus:ring-blue-800',
        'secondary' => 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 focus:ring-slate-300',
        'danger'    => 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-600',
        'outline'   => 'bg-transparent text-blue-800 border border-blue-800 hover:bg-blue-50 focus:ring-blue-800',
        'ghost'     => 'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:ring-slate-300',
    ];

    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
