@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden ' . $class]) }}>
    @isset($header)
        <div class="bg-slate-50 border-b border-slate-200 px-5 py-4">
            {{ $header }}
        </div>
    @endisset

    <div class="p-6">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-slate-200 px-5 py-4">
            {{ $footer }}
        </div>
    @endisset
</div>
