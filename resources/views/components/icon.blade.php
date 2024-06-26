@props(['name'])

<svg {{ $attributes }}><use xlink:href="{{ asset('tabler-sprite.svg') }}#tabler-{{ $name }}" /></svg>
