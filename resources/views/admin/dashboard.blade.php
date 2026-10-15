@extends('layouts.admin')

@section('title', 'Tableau de bord')

@php
    $queueCount = $todo['pendingLoans']->count() + $todo['renewals']->count() + $todo['late']->count() + $todo['dueSoon']->count() + $todo['pendingPurchases']->count();
    $max = max(1, $chart->max(fn ($m) => $m['sales'] + $m['rentals']));
@endphp

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Bonjour {{ auth()->user()->first_name }}</h1>
        <p class="muted">
            {{ ucfirst(now()->translatedFormat('l j F')) }}.
            {{ $queueCount ? $queueCount . ' élément' . ($queueCount > 1 ? 's demandent' : ' demande') . ' votre attention.' : 'Rien en attente, tout est à jour.' }}
        </p>
    </div>
    <div class="page-head__actions">
        <a href="{{ route('admin.loans.index') }}#new" class="btn"><x-icon name="key" size="sm" /> Nouvelle location</a>
        <a href="{{ route('admin.vehicles.index') }}#new" class="btn btn--primary"><x-icon name="plus" size="sm" /> Ajouter un véhicule</a>
    </div>
</div>

<div class="stats" style="--cols:4;margin-bottom:var(--s5)">
    <div class="stat">
        <div class="stat__label">Disponibles</div>
        <div class="stat__value num">{{ $stats['available'] }}</div>
        <div class="stat__note">véhicules prêts à partir</div>
    </div>
    <div class="stat">
        <div class="stat__label">Sortis</div>
        <div class="stat__value num">{{ $stats['out'] }}</div>
        @if ($todo['late']->count())
            <div class="stat__note stat__note--alert">dont {{ $todo['late']->count() }} en retard</div>
        @else
            <div class="stat__note">aucun retard</div>
        @endif
    </div>
    <div class="stat">
        <div class="stat__label">Encaissé ce mois</div>
        <div class="stat__value num">@money($stats['revenueMonth'])</div>
        <div class="stat__note num">@money($stats['revenueTotal']) depuis le début</div>
    </div>
    <div class="stat">
        <div class="stat__label">Clients</div>
        <div class="stat__value num">{{ $stats['clients'] }}</div>
        <a class="stat__note" href="{{ route('admin.contacts.index') }}">{{ $stats['unread'] ? $stats['unread'] . ' message(s) non lu(s)' : 'Aucun message non lu' }}</a>
    </div>
</div>

<div class="grid-main grid-main--wide-side">
    <section class="panel" aria-labelledby="t-todo">
        <div class="panel__head">
            <h2 class="h3" id="t-todo">À traiter</h2>
            <a href="{{ route('admin.loans.index') }}" class="small">Toutes les locations</a>
        </div>

        @if ($queueCount === 0)
            <x-empty icon="check-circle" title="Tout est à jour" text="Les nouvelles demandes de location, prolongations et achats apparaîtront ici." />
        @else
            <div class="queue">
                @if ($todo['late']->isNotEmpty())
                    <div class="queue__group queue__group--alert"><span class="dot"></span>En retard</div>
                    @foreach ($todo['late'] as $loan)
                        <div class="queue__item">
                            <x-plate :plate="$loan->vehicle?->plate ?? '—'" />
                            <div class="queue__main">
                                <a href="{{ route('admin.loans.show', $loan) }}" class="queue__title table__link">{{ $loan->vehicle?->name }}</a>
                                <div class="queue__meta">{{ $loan->user?->nom }}, retour attendu le {{ $loan->end_date->translatedFormat('j M') }} <strong style="color:var(--alert)">({{ $loan->daysLate() }} j de retard)</strong></div>
                            </div>
                            <div class="queue__actions">@include('admin.loans.partials.actions')</div>
                        </div>
                    @endforeach
                @endif

                @if ($todo['pendingLoans']->isNotEmpty())
                    <div class="queue__group queue__group--wait"><span class="dot"></span>Demandes de location</div>
                    @foreach ($todo['pendingLoans'] as $loan)
                        <div class="queue__item">
                            <x-plate :plate="$loan->vehicle?->plate ?? '—'" />
                            <div class="queue__main">
                                <a href="{{ route('admin.loans.show', $loan) }}" class="queue__title table__link">{{ $loan->vehicle?->name }}</a>
                                <div class="queue__meta">{{ $loan->user?->nom }}, {{ $loan->duration_days }} jour(s), <span class="num">@money($loan->total_amount)</span></div>
                            </div>
                            <div class="queue__actions">@include('admin.loans.partials.actions')</div>
                        </div>
                    @endforeach
                @endif

                @if ($todo['renewals']->isNotEmpty())
                    <div class="queue__group queue__group--wait"><span class="dot"></span>Prolongations demandées</div>
                    @foreach ($todo['renewals'] as $loan)
                        <div class="queue__item">
                            <x-plate :plate="$loan->vehicle?->plate ?? '—'" />
                            <div class="queue__main">
                                <a href="{{ route('admin.loans.show', $loan) }}" class="queue__title table__link">{{ $loan->vehicle?->name }}</a>
                                <div class="queue__meta">{{ $loan->user?->nom }} demande {{ $loan->renewal_requested_days }} jour(s) de plus, soit <span class="num">@money(($loan->vehicle?->loan_price ?? 0) * $loan->renewal_requested_days)</span></div>
                            </div>
                            <div class="queue__actions">@include('admin.loans.partials.actions')</div>
                        </div>
                    @endforeach
                @endif

                @if ($todo['pendingPurchases']->isNotEmpty())
                    <div class="queue__group queue__group--brand"><span class="dot"></span>Achats à encaisser</div>
                    @foreach ($todo['pendingPurchases'] as $t)
                        <div class="queue__item">
                            <x-plate :plate="$t->vehicle?->plate ?? '—'" />
                            <div class="queue__main">
                                <a href="{{ route('admin.transactions.show', $t) }}" class="queue__title table__link">{{ $t->vehicle?->name }}</a>
                                <div class="queue__meta">{{ $t->user?->nom }}, {{ $t->reference }}, <span class="num">@money($t->amount)</span></div>
                            </div>
                            <div class="queue__actions">
                                <form method="POST" action="{{ route('admin.transactions.confirm', $t) }}" class="inline-form" data-confirm="Confirmer le paiement de {{ \App\Providers\AppServiceProvider::money($t->amount) }} ?">
                                    @csrf<button type="submit" class="btn btn--sm btn--ok"><x-icon name="check" size="sm" /> Paiement reçu</button>
                                </form>
                                <form method="POST" action="{{ route('admin.transactions.cancel', $t) }}" class="inline-form" data-confirm="Annuler cet achat ? Le véhicule sera remis en vente.">
                                    @csrf<button type="submit" class="btn btn--sm btn--danger">Annuler</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if ($todo['dueSoon']->isNotEmpty())
                    <div class="queue__group"><span class="dot"></span>Retours prévus sous 48 h</div>
                    @foreach ($todo['dueSoon'] as $loan)
                        <div class="queue__item">
                            <x-plate :plate="$loan->vehicle?->plate ?? '—'" />
                            <div class="queue__main">
                                <a href="{{ route('admin.loans.show', $loan) }}" class="queue__title table__link">{{ $loan->vehicle?->name }}</a>
                                <div class="queue__meta">{{ $loan->user?->nom }}, {{ $loan->end_date->isToday() ? 'aujourd’hui' : $loan->end_date->translatedFormat('l j M') }}</div>
                            </div>
                            <div class="queue__actions">@include('admin.loans.partials.actions')</div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
    </section>

    <div class="stack">
        <section class="panel" aria-labelledby="t-rev">
            <div class="panel__head">
                <h2 class="h3" id="t-rev">Encaissements, 6 mois</h2>
                <div class="legend">
                    <span><i style="background:var(--brand)"></i>Ventes</span>
                    <span><i style="background:color-mix(in srgb, var(--brand) 38%, var(--surface))"></i>Locations</span>
                </div>
            </div>
            <div class="bars" role="img" aria-label="Encaissements des six derniers mois">
                @foreach ($chart as $m)
                    @php $sum = $m['sales'] + $m['rentals']; @endphp
                    <div class="bars__col">
                        <span class="bars__tip">{{ $m['label'] }} : {{ \App\Providers\AppServiceProvider::money($sum) }}</span>
                        @if ($m['rentals'])<div class="bars__seg bars__seg--rentals" style="height: {{ $m['rentals'] / $max * 100 }}%"></div>@endif
                        @if ($m['sales'])<div class="bars__seg bars__seg--sales" style="height: {{ $m['sales'] / $max * 100 }}%"></div>@endif
                    </div>
                @endforeach
            </div>
            <div class="bars__labels">@foreach ($chart as $m)<span>{{ $m['label'] }}</span>@endforeach</div>
        </section>

        <section class="panel" aria-labelledby="t-last">
            <div class="panel__head">
                <h2 class="h3" id="t-last">Derniers paiements</h2>
                <a href="{{ route('admin.transactions.index') }}" class="small">Tout voir</a>
            </div>
            @if ($recent->isEmpty())
                <x-empty icon="receipt" title="Aucun paiement" />
            @else
                <div class="queue">
                    @foreach ($recent as $t)
                        <a href="{{ route('admin.transactions.show', $t) }}" class="queue__item" style="text-decoration:none;color:inherit">
                            <div class="queue__main">
                                <div class="queue__title">{{ $t->user?->nom ?? 'Client supprimé' }}</div>
                                <div class="queue__meta">{{ $t->type_label }}, {{ $t->vehicle?->name }}</div>
                            </div>
                            <div style="text-align:right">
                                <div class="strong num">@money($t->amount)</div>
                                <div class="tiny muted">{{ $t->created_at->translatedFormat('j M') }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
