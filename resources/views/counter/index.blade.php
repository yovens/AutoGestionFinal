@extends('layouts.admin')

@section('title', 'Comptoir')

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Comptoir</h1>
        <p class="muted">{{ ucfirst(now()->translatedFormat('l j F')) }}. Accueil des clients, remise des clés et retours.</p>
    </div>
    <div class="page-head__actions">
        <a href="{{ route('counter.loans.create') }}" class="btn btn--primary"><x-icon name="plus" size="sm" /> Location sur place</a>
    </div>
</div>

{{-- Premye aksyon an: kliyan an rive, li bay kòd li oswa non li --}}
<form method="GET" action="{{ route('counter.search') }}" class="lookup" role="search" style="margin-bottom:var(--s5)">
    <label class="lookup__field">
        <span class="sr-only">Rechercher</span>
        <x-icon name="search" />
        <input class="lookup__input" type="search" name="q" placeholder="Code de retrait, plaque, nom ou téléphone" autocomplete="off" autofocus>
    </label>
    <button type="submit" class="btn btn--primary">Rechercher</button>
    <p class="lookup__hint">Avec le code de retrait du client (ex. K7P-29Q), la remise des clés s’ouvre directement.</p>
</form>

<div class="stats" style="--cols:4;margin-bottom:var(--s5)">
    <div class="stat"><div class="stat__label">Clés à remettre</div><div class="stat__value num">{{ $stats['pickups'] }}</div><div class="stat__note">locations confirmées</div></div>
    <div class="stat">
        <div class="stat__label">Retours attendus</div><div class="stat__value num">{{ $stats['returns'] }}</div>
        @if ($queues['late']->count())<div class="stat__note stat__note--alert">dont {{ $queues['late']->count() }} en retard</div>@else<div class="stat__note">aujourd’hui</div>@endif
    </div>
    <div class="stat"><div class="stat__label">Véhicules dehors</div><div class="stat__value num">{{ $stats['out'] }}</div><div class="stat__note">chez les clients</div></div>
    <div class="stat"><div class="stat__label">J’ai encaissé aujourd’hui</div><div class="stat__value num">@money($stats['cashToday'])</div><a class="stat__note" href="{{ route('counter.cash') }}">{{ $stats['opsToday'] }} opération(s), voir la caisse</a></div>
</div>

<div class="grid-2">
    {{-- Depa --}}
    <section class="panel" aria-labelledby="t-out">
        <div class="panel__head"><h2 class="h3" id="t-out">Clés à remettre</h2><span class="small muted">{{ $queues['pickups']->count() }}</span></div>
        @forelse ($queues['pickups'] as $loan)
            <div class="queue__item">
                <x-plate :plate="$loan->vehicle?->plate ?? '—'" />
                <div class="queue__main">
                    <a href="{{ route('counter.loans.show', $loan) }}" class="queue__title table__link">{{ $loan->user?->nom }}</a>
                    <div class="queue__meta">{{ $loan->vehicle?->name }}, {{ $loan->duration_days }} j, code <strong class="num">{{ $loan->pickup_code }}</strong></div>
                </div>
                <div class="queue__actions">@include('counter.loans.partials.actions')</div>
            </div>
        @empty
            <x-empty icon="key" title="Aucune remise prévue" text="Les locations confirmées apparaissent ici jusqu’à ce que le client vienne chercher les clés." />
        @endforelse
    </section>

    {{-- Retou --}}
    <section class="panel" aria-labelledby="t-in">
        <div class="panel__head"><h2 class="h3" id="t-in">Retours</h2><span class="small muted">{{ $queues['late']->count() + $queues['returns']->count() }}</span></div>
        @if ($queues['late']->isEmpty() && $queues['returns']->isEmpty())
            <x-empty icon="return" title="Aucun retour attendu aujourd’hui" />
        @endif
        @foreach ($queues['late'] as $loan)
            <div class="queue__item" style="box-shadow: inset 3px 0 0 var(--alert)">
                <x-plate :plate="$loan->vehicle?->plate ?? '—'" />
                <div class="queue__main">
                    <a href="{{ route('counter.loans.show', $loan) }}" class="queue__title table__link">{{ $loan->user?->nom }}</a>
                    <div class="queue__meta">
                        <strong style="color:var(--alert)">{{ $loan->daysLate() }} j de retard</strong>@if ($loan->user?->telephone), <a href="tel:{{ preg_replace('/\s+/', '', $loan->user->telephone) }}">{{ $loan->user->telephone }}</a>@endif
                    </div>
                </div>
                <div class="queue__actions">@include('counter.loans.partials.actions')</div>
            </div>
        @endforeach
        @foreach ($queues['returns'] as $loan)
            <div class="queue__item">
                <x-plate :plate="$loan->vehicle?->plate ?? '—'" />
                <div class="queue__main">
                    <a href="{{ route('counter.loans.show', $loan) }}" class="queue__title table__link">{{ $loan->user?->nom }}</a>
                    <div class="queue__meta">{{ $loan->vehicle?->name }}, retour prévu aujourd’hui</div>
                </div>
                <div class="queue__actions"><a href="{{ route('counter.loans.checkin', $loan) }}" class="btn btn--sm"><x-icon name="return" size="sm" /> Réceptionner</a></div>
            </div>
        @endforeach
    </section>

    {{-- Demann sou sit la --}}
    <section class="panel" aria-labelledby="t-req">
        <div class="panel__head"><h2 class="h3" id="t-req">Demandes en ligne à confirmer</h2><span class="small muted">{{ $queues['requests']->count() + $queues['renewals']->count() }}</span></div>
        @if ($queues['requests']->isEmpty() && $queues['renewals']->isEmpty())
            <x-empty icon="check-circle" title="Rien à confirmer" />
        @endif
        @foreach ($queues['requests'] as $loan)
            <div class="queue__item">
                <x-plate :plate="$loan->vehicle?->plate ?? '—'" />
                <div class="queue__main">
                    <a href="{{ route('counter.loans.show', $loan) }}" class="queue__title table__link">{{ $loan->user?->nom }}</a>
                    <div class="queue__meta">{{ $loan->vehicle?->name }}, {{ $loan->duration_days }} j, <span class="num">@money($loan->total_amount)</span></div>
                </div>
                <div class="queue__actions">@include('counter.loans.partials.actions')</div>
            </div>
        @endforeach
        @foreach ($queues['renewals'] as $loan)
            <div class="queue__item">
                <x-plate :plate="$loan->vehicle?->plate ?? '—'" />
                <div class="queue__main">
                    <a href="{{ route('counter.loans.show', $loan) }}" class="queue__title table__link">{{ $loan->user?->nom }}</a>
                    <div class="queue__meta">Prolongation de {{ $loan->renewal_requested_days }} j demandée</div>
                </div>
                <div class="queue__actions">
                    <form method="POST" action="{{ route('counter.loans.renewal.approve', $loan) }}" class="inline-form">@csrf<button class="btn btn--sm btn--ok"><x-icon name="check" size="sm" /> Accepter</button></form>
                    <form method="POST" action="{{ route('counter.loans.renewal.decline', $loan) }}" class="inline-form">@csrf<button class="btn btn--sm">Refuser</button></form>
                </div>
            </div>
        @endforeach
    </section>

    {{-- Acha pou ankese --}}
    <section class="panel" aria-labelledby="t-sale">
        <div class="panel__head"><h2 class="h3" id="t-sale">Achats à encaisser</h2><span class="small muted">{{ $queues['sales']->count() }}</span></div>
        @forelse ($queues['sales'] as $t)
            <div class="queue__item">
                <x-plate :plate="$t->vehicle?->plate ?? '—'" />
                <div class="queue__main">
                    <div class="queue__title">{{ $t->user?->nom }}</div>
                    <div class="queue__meta">{{ $t->vehicle?->name }}, {{ $t->reference }}, <span class="num">@money($t->amount)</span></div>
                </div>
                <div class="queue__actions">
                    <button type="button" class="btn btn--sm btn--ok" data-dialog-open="sale-{{ $t->id }}"><x-icon name="check" size="sm" /> Encaisser</button>
                </div>
            </div>
            @include('counter.partials.sale-dialog', ['t' => $t])
        @empty
            <x-empty icon="tag" title="Aucun achat en attente" />
        @endforelse
    </section>
</div>
@endsection
