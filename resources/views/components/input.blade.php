@props([
    'name' => '',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'disabled' => false,
])

<input
    type="{{ $type }}"
    name="{{ $name }}"
    value="{{ $value }}"
    {{ $required ? 'required' : '' }}
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => 'w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-800/30 focus:border-blue-800 transition-colors']) }}
/>
