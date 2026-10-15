@props(['tone' => 'off'])
<span {{ $attributes->class(['badge', 'badge--' . $tone]) }}>{{ $slot }}</span>
