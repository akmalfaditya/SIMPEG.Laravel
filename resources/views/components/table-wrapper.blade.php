@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'w-full overflow-x-auto rounded-lg border border-slate-200 shadow-sm bg-white ' . $class]) }}>
    <table class="w-full text-sm">
        {{ $slot }}
    </table>
</div>
