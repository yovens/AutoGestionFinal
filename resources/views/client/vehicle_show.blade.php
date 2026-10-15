@extends('layouts.client')

@section('title', $vehicle->name)

@section('content')
<nav class="crumbs" aria-label="Fil d’Ariane" style="margin-bottom:var(--s4)">
    <a href="{{ route('client.vehicles') }}"><x-icon name="arrow-left" size="sm" style="vertical-align:-3px" /> Le parc</a>
</nav>

<div class="vehicle">
    <div class="stack">
        <div class="vehicle__photo">
            @if ($vehicle->image_url)<img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->name }}">@endif
        </div>
        <dl class="specs">
            <div><dt>Année</dt><dd class="num">{{ $vehicle->year }}</dd></div>
            <div><dt>Location</dt><dd class="num">@money($vehicle->loan_price) / jour</dd></div>
            <div><dt>Achat</dt><dd class="num">@money($vehicle->price)</dd></div>
        </dl>
    </div>

    <aside class="vehicle__aside stack">
        <div class="stack-sm">
            <x-plate :plate="$vehicle->plate" size="lg" />
            <p class="muted" style="margin-top:var(--s3)">{{ $vehicle->brand }}</p>
            <h1 class="vehicle__title">{{ $vehicle->model }}</h1>
            <div class="row row--tight" style="margin-top:var(--s2)">
                @if ($vehicle->is_available)
                    <x-badge tone="ok">Disponible{{ $vehicle->quantity > 1 ? ', ' . $vehicle->quantity . ' exemplaires' : '' }}</x-badge>
                @else
                    <x-badge>Indisponible pour le moment</x-badge>
                @endif
            </div>
        </div>

        @if ($vehicle->is_available)
            <div class="panel" data-booking data-price="{{ $vehicle->loan_price }}">
                <div class="panel__body stack">
                    <div class="segment" role="radiogroup" aria-label="Je veux">
                        <input type="radio" name="mode" id="mode-rent" value="rent" checked><label for="mode-rent">Louer</label>
                        <input type="radio" name="mode" id="mode-buy" value="buy"><label for="mode-buy">Acheter</label>
                    </div>

                    <form method="POST" action="{{ route('client.loan.start', $vehicle) }}" class="book-pane stack" data-pane="rent">
                        @csrf
                        <div class="field">
                            <label class="field__label" for="duration">Durée de location</label>
                            <div class="input-group" style="max-width:240px">
                                <button type="button" class="btn" data-step="-1" aria-label="Un jour de moins"><x-icon name="minus" size="sm" /></button>
                                <input class="input" id="duration" type="number" name="duration" min="1" max="365" value="{{ old('duration', 3) }}" data-days inputmode="numeric">
                                <button type="button" class="btn" data-step="1" aria-label="Un jour de plus"><x-icon name="plus" size="sm" /></button>
                            </div>
                            @error('duration')<span class="field__error">{{ $message }}</span>@enderror
                        </div>
                        <div class="total-line">
                            <span class="muted">Total pour <span data-days-label>3 jours</span></span>
                            <span class="total-line__value num" data-total>@money($vehicle->loan_price * 3)</span>
                        </div>
                        <button type="submit" class="btn btn--primary btn--lg btn--block">Demander la location</button>
                        <p class="small muted">Le véhicule est bloqué à votre nom. L’agence confirme la demande, puis vous récupérez les clés et réglez sur place.</p>
                    </form>

                    <form method="POST" action="{{ route('client.purchase.start', $vehicle) }}" class="book-pane stack" data-pane="buy" hidden
                          data-confirm="Réserver l’achat de {{ $vehicle->name }} pour {{ \App\Providers\AppServiceProvider::money($vehicle->price) }} ?">
                        @csrf
                        <div class="total-line">
                            <span class="muted">Prix d’achat</span>
                            <span class="total-line__value num">@money($vehicle->price)</span>
                        </div>
                        <button type="submit" class="btn btn--primary btn--lg btn--block">Réserver l’achat</button>
                        <p class="small muted">Le véhicule est retiré de la vente et réservé à votre nom. Le paiement et les papiers se font à l’agence.</p>
                    </form>
                </div>
            </div>
        @else
            <div class="flash flash--info"><x-icon name="info" /><span>Ce véhicule est actuellement loué ou réservé. Ajoutez-le à votre sélection pour le retrouver facilement.</span></div>
        @endif

        @if ($inCart)
            <a href="{{ route('client.cart') }}" class="btn btn--block"><x-icon name="check" /> Dans votre sélection</a>
        @else
            <form method="POST" action="{{ route('client.cart.add', $vehicle) }}">
                @csrf
                <button type="submit" class="btn btn--block"><x-icon name="bag" /> Garder dans ma sélection</button>
            </form>
        @endif
    </aside>
</div>

@if ($similar->isNotEmpty())
    <section class="section section--line" style="padding:var(--s6) 0 0;margin-top:var(--s6)">
        <h2 class="h2" style="margin-bottom:var(--s4)">Dans la même gamme</h2>
        <div class="car-grid">
            @foreach ($similar as $s)<x-vehicle-card :vehicle="$s" />@endforeach
        </div>
    </section>
@endif
@endsection
