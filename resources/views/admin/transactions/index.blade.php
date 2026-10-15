@extends('layouts.admin')

@section('title', 'Paiements')

@php
    $T = \App\Models\Transaction::class;
    $q = fn (array $p) => route('admin.transactions.index', array_filter(array_merge(['type' => $type, 'status' => $status], $p)));
@endphp

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Paiements</h1>
        <p class="muted">Locations encaissées, achats à confirmer et paiements saisis au comptoir.</p>
    </div>
    <div class="page-head__actions">
        <button type="button" class="btn btn--primary" data-dialog-open="new"><x-icon name="plus" size="sm" /> Enregistrer un paiement</button>
    </div>
</div>

<div class="stats" style="--cols:4;margin-bottom:var(--s5)">
    <div class="stat"><div class="stat__label">Total encaissé</div><div class="stat__value num">@money($totals['revenue'])</div></div>
    <div class="stat"><div class="stat__label">Ventes</div><div class="stat__value num">@money($totals['sales'])</div></div>
    <div class="stat"><div class="stat__label">Locations</div><div class="stat__value num">@money($totals['rentals'])</div></div>
    <div class="stat">
        <div class="stat__label">Achats à confirmer</div>
        <div class="stat__value num">{{ $totals['pending'] }}</div>
        @if ($totals['pending'])<a class="stat__note" href="{{ route('admin.transactions.index', ['status' => 'pending']) }}">Afficher</a>@endif
    </div>
</div>

<div class="toolbar">
    <nav class="tabs" aria-label="Filtrer par type">
        <a href="{{ $q(['type' => null]) }}" @if (!$type) aria-current="page" @endif>Tous</a>
        <a href="{{ $q(['type' => $T::SALE]) }}" @if ($type === $T::SALE) aria-current="page" @endif>Achats</a>
        <a href="{{ $q(['type' => $T::RENTAL]) }}" @if ($type === $T::RENTAL) aria-current="page" @endif>Locations</a>
    </nav>
    <nav class="tabs" aria-label="Filtrer par statut">
        <a href="{{ $q(['status' => null]) }}" @if (!$status) aria-current="page" @endif>Tous statuts</a>
        @foreach ($T::STATUS_LABELS as $k => $label)
            <a href="{{ $q(['status' => $k]) }}" @if ($status === $k) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>
</div>

<section class="panel panel--flush">
    @if ($transactions->isEmpty())
        <x-empty icon="receipt" title="Aucun paiement" text="Les paiements sont créés automatiquement à l’approbation d’une location ou à la réservation d’un achat." />
    @else
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Référence</th><th>Client</th><th>Véhicule</th><th>Type</th><th>Statut</th><th class="right">Montant</th><th class="right"><span class="sr-only">Actions</span></th></tr></thead>
                <tbody>
                    @foreach ($transactions as $t)
                        <tr class="{{ $t->status === $T::PENDING ? 'is-wait' : '' }}">
                            <td><a href="{{ route('admin.transactions.show', $t) }}" class="table__link num">{{ $t->reference }}</a><span class="table__sub">{{ $t->created_at->translatedFormat('j M Y, H:i') }}</span></td>
                            <td>{{ $t->user?->nom ?? 'Compte supprimé' }}</td>
                            <td><span class="nowrap">{{ $t->vehicle?->name ?? '—' }}</span>@if ($t->vehicle)<span class="table__sub">{{ $t->vehicle->plate }}</span>@endif</td>
                            <td>{{ $t->type_label }}</td>
                            <td><x-status :model="$t" /></td>
                            <td class="right strong num">@money($t->amount)</td>
                            <td>
                                <div class="actions">
                                    @if ($t->status === $T::PENDING)
                                        <form method="POST" action="{{ route('admin.transactions.confirm', $t) }}" class="inline-form" data-confirm="Confirmer le paiement de {{ \App\Providers\AppServiceProvider::money($t->amount) }} ?">
                                            @csrf<button type="submit" class="btn btn--sm btn--ok">Paiement reçu</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.transactions.show', $t) }}" class="btn btn--sm btn--ghost btn--icon" aria-label="Voir le reçu"><x-icon name="chevron-right" size="sm" /></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>

<x-dialog id="new" title="Enregistrer un paiement" :open-on-load="$errors->any()">
    <form method="POST" action="{{ route('admin.transactions.store') }}">
        @csrf
        <div class="modal__body stack">
            <p class="small muted">Pour un paiement reçu hors de l’application. Il est enregistré comme réglé.</p>
            <x-field name="user_id" type="select" label="Client" placeholder="Choisir un client" required :options="$clients->mapWithKeys(fn ($c) => [$c->id => $c->nom . ' (' . $c->email . ')'])->all()" />
            <x-field name="vehicle_id" type="select" label="Véhicule" placeholder="Choisir un véhicule" required :options="$vehicles->mapWithKeys(fn ($v) => [$v->id => $v->name . ' (' . $v->plate . ')'])->all()" />
            <div class="form-grid">
                <x-field name="type" type="select" label="Type" :value="'location'" :options="[$T::RENTAL => 'Location', $T::SALE => 'Achat']" />
                <x-field name="amount" type="number" label="Montant ($)" min="0" required />
            </div>
        </div>
        <div class="modal__foot">
            <button type="button" class="btn" data-dialog-close>Annuler</button>
            <button type="submit" class="btn btn--primary">Enregistrer</button>
        </div>
    </form>
</x-dialog>
@endsection
