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

<section class="panel panel--flush">
    @if ($transactions->isEmpty())
        <x-empty icon="receipt" title="Aucun paiement pour l’instant" text="Vos reçus apparaîtront ici dès qu’une location est confirmée ou qu’un achat est réservé." />
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Référence</th><th>Véhicule</th><th>Type</th><th>Date</th><th>Statut</th><th class="right">Montant</th></tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $t)
                        <tr>
                            <td class="strong num">{{ $t->reference }}</td>
                            <td>
                                <span class="strong">{{ $t->vehicle?->name ?? 'Véhicule supprimé' }}</span>
                                @if ($t->vehicle)<span class="table__sub">{{ $t->vehicle->plate }}</span>@endif
                            </td>
                            <td>{{ $t->type_label }}</td>
                            <td class="nowrap">{{ $t->created_at->translatedFormat('j M Y') }}</td>
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
