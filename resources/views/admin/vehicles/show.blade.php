@extends('layouts.admin')

@section('title', $vehicle->name)

@section('crumbs')
    <a href="{{ route('admin.vehicles.index') }}">Parc automobile</a>
    <x-icon name="chevron-right" size="sm" />
    <span aria-current="page">{{ $vehicle->name }}</span>
@endsection

@php
    $revenue = $vehicle->transactions->where('status', \App\Models\Transaction::COMPLETED)->sum('amount');
@endphp

@section('content')
<div class="page-head">
    <div class="page-head__text row" style="align-items:center">
        @if ($vehicle->image_url)<img src="{{ $vehicle->image_url }}" alt="" class="thumb" style="width:96px;height:64px">@endif
        <div>
            <x-plate :plate="$vehicle->plate" />
            <h1 class="h1" style="margin-top:6px">{{ $vehicle->name }}</h1>
        </div>
    </div>
    <div class="page-head__actions">
        <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}" data-confirm="Supprimer {{ $vehicle->name }} du parc ? L’historique lié sera aussi supprimé.">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn--danger"><x-icon name="trash" size="sm" /> Supprimer</button>
        </form>
    </div>
</div>

<div class="stats" style="--cols:4;margin-bottom:var(--s5)">
    <div class="stat"><div class="stat__label">En stock</div><div class="stat__value num">{{ $vehicle->quantity }}</div></div>
    <div class="stat"><div class="stat__label">Locations</div><div class="stat__value num">{{ $vehicle->loans->count() }}</div></div>
    <div class="stat"><div class="stat__label">Encaissé</div><div class="stat__value num">@money($revenue)</div></div>
    <div class="stat"><div class="stat__label">Ajouté le</div><div class="stat__value" style="font-size:1.2rem">{{ $vehicle->created_at?->translatedFormat('j M Y') }}</div></div>
</div>

<div class="grid-main grid-main--wide-side">
    <div class="stack">
        <section class="panel panel--flush">
            <div class="panel__head"><h2 class="h3">Historique des locations</h2></div>
            @if ($vehicle->loans->isEmpty())
                <x-empty icon="key" title="Jamais loué" />
            @else
                <div class="table-wrap"><table class="table">
                    <thead><tr><th>Client</th><th>Période</th><th>Statut</th><th class="right">Montant</th></tr></thead>
                    <tbody>
                        @foreach ($vehicle->loans as $loan)
                            <tr>
                                <td><a href="{{ route('admin.loans.show', $loan) }}" class="table__link">{{ $loan->user?->nom ?? 'Compte supprimé' }}</a></td>
                                <td class="nowrap">{{ $loan->start_date ? $loan->start_date->translatedFormat('j M') . ' au ' . $loan->end_date->translatedFormat('j M Y') : '—' }}</td>
                                <td><x-status :model="$loan" /></td>
                                <td class="right num">@money($loan->total_amount)</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            @endif
        </section>

        <section class="panel panel--flush">
            <div class="panel__head"><h2 class="h3">Paiements</h2></div>
            @if ($vehicle->transactions->isEmpty())
                <x-empty icon="receipt" title="Aucun paiement" />
            @else
                <div class="table-wrap"><table class="table">
                    <thead><tr><th>Référence</th><th>Client</th><th>Type</th><th>Statut</th><th class="right">Montant</th></tr></thead>
                    <tbody>
                        @foreach ($vehicle->transactions as $t)
                            <tr>
                                <td><a href="{{ route('admin.transactions.show', $t) }}" class="table__link num">{{ $t->reference }}</a></td>
                                <td>{{ $t->user?->nom ?? '—' }}</td>
                                <td>{{ $t->type_label }}</td>
                                <td><x-status :model="$t" /></td>
                                <td class="right num">@money($t->amount)</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            @endif
        </section>
    </div>

    <section class="panel">
        <div class="panel__head"><h2 class="h3">Modifier</h2></div>
        <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="panel__body" style="--cols:1">
                @include('admin.vehicles.partials.form', ['vehicle' => $vehicle])
            </div>
            <div class="panel__foot row row--end"><button type="submit" class="btn btn--primary">Enregistrer</button></div>
        </form>
    </section>
</div>
@endsection
