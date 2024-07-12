@props(['disabled' => false, 'value'])

<textarea rows="5" {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}
    >{{ $slot }}</textarea>
