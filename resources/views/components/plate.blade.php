@props(['plate', 'size' => null])
{{-- Plak machin: se idantite machin nan toupatou nan aplikasyon an. --}}
<span {{ $attributes->class(['plate', 'plate--' . $size => $size]) }} title="Plaque {{ $plate }}">
    <span class="plate__band" aria-hidden="true">HT</span>
    <span class="plate__num">{{ $plate }}</span>
</span>
