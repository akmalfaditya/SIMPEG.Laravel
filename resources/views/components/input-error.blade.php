@props(['messages' => []])

@if($messages)
    @foreach((array) $messages as $message)
        <p {{ $attributes->merge(['class' => 'text-sm text-rose-600 mt-1']) }}>{{ $message }}</p>
    @endforeach
@endif
