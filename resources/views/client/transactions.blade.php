@extends('layouts.client')

@section('title', 'Paiements')

@section('content')
<div class="page__head">
    <div>
        <h1 class="h1">Paiements</h1>
        <p class="muted">Tous vos paiements de location et vos achats, avec leur référence pour l’agence.</p>
    </div>
</div>

<div class="stats" style="--cols:3;margin-bottom:var(--s5)">
    <div class="stat"><div class="stat__label">Total réglé</div><div class="stat__value num">@money($totals['paid'])</div></div>
    <div class="stat"><div class="stat__label">À régler à l’agence</div><div class="stat__value num">@money($totals['pending'])</div></div>
    <div class="stat"><div class="stat__label">Opérations</div><div class="stat__value num">{{ $transactions->count() }}</div></div>
</div>

@if ($tickets->isNotEmpty())
    <section class="panel" style="margin-bottom:var(--s5)">
        <div class="panel__head"><h2 class="h3">Mes bons</h2><span class="small muted">Bons de sortie, de retour et de remise remis à l’agence</span></div>
        <div class="queue">
            @foreach ($tickets as $tk)
                <a href="{{ route('client.tickets.show', $tk) }}" class="queue__item" style="text-decoration:none;color:inherit">
                    <x-icon name="receipt" />
                    <div class="queue__main">
                        <div class="queue__title">{{ $tk->title }} <span class="muted num">{{ $tk->code }}</span></div>
                        <div class="queue__meta">{{ $tk->vehicle?->name }}, {{ $tk->created_at->translatedFormat('j M Y, H:i') }}</div>
                    </div>
                    <span class="strong num">@money($tk->amount)</span>
                </a>
            @endforeach
        </div>
    </section>
@endif

<section class="panel panel--flush">
    @if ($transactions->isEmpty())
        <x-empty icon="receipt" title="Aucun paiement pour l’instant" text="Vos paiements apparaîtront ici dès que vous réglez une location à l’agence ou réservez un achat." />
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Référence</th><th>Véhicule</th><th>Type</th><th>Date</th><th>Mode</th><th>Statut</th><th class="right">Montant</th></tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $t)
                        <tr>
                            <td class="strong num">{{ $t->reference }}</td>
                            <td>
                                <span class="strong">{{ $t->vehicle?->name ?? 'Véhicule supprimé' }}</span>
                                @if ($t->vehicle)<span class="table__sub">{{ $t->vehicle->plate }}</span>@endif
                            </td>
                            <td>{{ $t->note ?: $t->type_label }}</td>
                            <td class="nowrap">{{ $t->created_at->translatedFormat('j M Y') }}</td>
                            <td>{{ $t->method_label }}</td>
                            <td><x-status :model="$t" /></td>
                            <td class="right strong num">@money($t->amount)</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
