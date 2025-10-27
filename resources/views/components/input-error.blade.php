@props(['for' => null, 'messages' => null])

@if($for)
    @error($for)
        <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
            <li>{{ $message }}</li>
        </ul>
    @enderror
@elseif($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
