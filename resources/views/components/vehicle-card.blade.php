@props(['vehicle', 'href' => null])
@php $href ??= route('client.vehicle.show', $vehicle); @endphp
<article class="car">
    <div class="car__media">
        @if ($vehicle->image_url)
            <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->name }}" loading="lazy">
        @endif
        <x-plate :plate="$vehicle->plate" class="car__plate" />
        @if ($vehicle->quantity === 0)
            <x-badge class="car__stock">Indisponible</x-badge>
        @endif
    </div>
    <div class="car__body">
        <span class="car__brand">{{ $vehicle->brand }}, {{ $vehicle->year }}</span>
        <h3 class="car__name"><a href="{{ $href }}">{{ $vehicle->model }}</a></h3>
    </div>
    <div class="car__prices">
        <div class="car__price">
            <div class="car__price-label">Location</div>
            <div class="car__price-value num">@money($vehicle->loan_price) <small>/ jour</small></div>
        </div>
        <div class="car__price">
            <div class="car__price-label">Achat</div>
            <div class="car__price-value num">@money($vehicle->price)</div>
        </div>
    </div>
</article>
