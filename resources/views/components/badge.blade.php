@props(['color' => 'slate'])

@php
    $colors = [
        'slate'   => 'bg-slate-100 text-slate-700',
        'blue'    => 'bg-blue-100 text-blue-800',
        'emerald' => 'bg-emerald-100 text-emerald-700',
        'rose'    => 'bg-rose-100 text-rose-700',
        'amber'   => 'bg-amber-100 text-amber-700',
        'red'     => 'bg-rose-100 text-rose-700',
    ];
    $colorClasses = $colors[$color] ?? $colors['slate'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ' . $colorClasses]) }}>
    {{ $slot }}
</span>
