@props(['required' => false])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-slate-700 mb-1']) }}>
    {{ $slot }}@if($required)<span class="text-rose-500 ml-0.5">*</span>@endif
</label>
