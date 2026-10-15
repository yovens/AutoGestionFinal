@extends('layouts.client')

@section('title', 'Accueil')

@section('content')
<div class="page__head">
    <div>
        <h1 class="h1">Bonjour {{ $user->first_name }}</h1>
        <p class="muted">
            @if ($activeLoans->isEmpty() && $pendingPurchases->isEmpty())
                Aucune location en cours. Trouvez votre prochain véhicule dans le parc.
            @else
                Voici où en sont vos véhicules.
            @endif
        </p>
    </div>
    <a href="{{ route('client.vehicles') }}" class="btn btn--primary"><x-icon name="car" /> Parcourir les véhicules</a>
</div>

<div class="stack-lg">
    @if ($activeLoans->isNotEmpty())
        <section class="panel" aria-labelledby="t-loans">
            <div class="panel__head">
                <h2 class="h3" id="t-loans">Locations en cours</h2>
                <a href="{{ route('client.loan') }}" class="small">Tout voir</a>
            </div>
            @foreach ($activeLoans as $loan)
                @include('client.partials.loan-row', ['loan' => $loan, 'compact' => true])
            @endforeach
        </section>
    @endif

    @if ($pendingPurchases->isNotEmpty())
        <section class="panel" aria-labelledby="t-buy">
            <div class="panel__head">
                <h2 class="h3" id="t-buy">Achats réservés</h2>
                <span class="small muted">À régler à l’agence</span>
            </div>
            @foreach ($pendingPurchases as $t)
                <div class="loan">
                    @if ($t->vehicle?->image_url)<img class="loan__img" src="{{ $t->vehicle->image_url }}" alt="">@else<span class="loan__img"></span>@endif
                    <div>
                        <div class="loan__name">{{ $t->vehicle?->name ?? 'Véhicule supprimé' }}</div>
                        <div class="loan__meta">Réservé le {{ $t->created_at->translatedFormat('j F Y') }}, référence {{ $t->reference }}</div>
                    </div>
                    <div class="loan__side">
                        <x-status :model="$t" />
                        <span class="strong num">@money($t->amount)</span>
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    <div class="stats" style="--cols:4">
        <div class="stat"><div class="stat__label">Ma sélection</div><div class="stat__value num">{{ $stats['cart'] }}</div><a class="stat__note" href="{{ route('client.cart') }}">Ouvrir</a></div>
        <div class="stat"><div class="stat__label">Locations</div><div class="stat__value num">{{ $stats['loans'] }}</div><div class="stat__note">depuis l’ouverture du compte</div></div>
        <div class="stat"><div class="stat__label">Opérations</div><div class="stat__value num">{{ $stats['transactions'] }}</div><a class="stat__note" href="{{ route('client.transactions') }}">Voir les reçus</a></div>
        <div class="stat"><div class="stat__label">Total réglé</div><div class="stat__value num">@money($stats['spent'])</div><div class="stat__note">paiements confirmés</div></div>
    </div>

    @if ($suggestions->isNotEmpty())
        <section aria-labelledby="t-new">
            <div class="section__head" style="margin-bottom:var(--s4)">
                <h2 class="h2" id="t-new">Arrivés récemment</h2>
                <a href="{{ route('client.vehicles') }}" class="small">Tout le parc</a>
            </div>
            <div class="car-grid">
                @foreach ($suggestions as $vehicle)
                    <x-vehicle-card :vehicle="$vehicle" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
