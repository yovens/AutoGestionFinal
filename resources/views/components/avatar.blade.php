@props(['user', 'size' => null])
<span {{ $attributes->class(['avatar', 'avatar--' . $size => $size]) }} aria-hidden="true">
    @if ($user?->avatar_url)
        <img src="{{ $user->avatar_url }}" alt="" loading="lazy">
    @else
        {{ $user?->initials ?? 'AG' }}
    @endif
</span>
