@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2 text-white bg-gray-700 rounded-md transition-colors duration-200'
            : 'flex items-center px-4 py-2 text-gray-400 hover:bg-gray-700 hover:text-white rounded-md transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>