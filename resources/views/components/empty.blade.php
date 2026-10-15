@props(['icon' => 'info', 'title', 'text' => null])
<div {{ $attributes->class(['empty']) }}>
    <span class="empty__icon"><x-icon :name="$icon" size="lg" /></span>
    <p class="empty__title">{{ $title }}</p>
    @if ($text)<p class="empty__text">{{ $text }}</p>@endif
    {{ $slot }}
</div>
