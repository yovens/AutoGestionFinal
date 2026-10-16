@extends('layouts.admin')

@section('title', 'Locations')

@php
    $L = \App\Models\LoanCart::class;
    $active = ($counts[$L::APPROVED] ?? 0) + ($counts[$L::EXPIRED] ?? 0) + ($counts[$L::PENDING_RENEWAL] ?? 0);
    $tabs = [
        null                => ['Toutes', $counts->sum()],
        $L::PENDING         => ['Demandes', $counts[$L::PENDING] ?? 0],
        'active'            => ['Sorties', $active],
        $L::EXPIRED         => ['En retard', $counts[$L::EXPIRED] ?? 0],
        $L::PENDING_RENEWAL => ['Prolongations', $counts[$L::PENDING_RENEWAL] ?? 0],
        $L::RETURNED        => ['Rendues', $counts[$L::RETURNED] ?? 0],
        $L::REJECTED        => ['Refusées', $counts[$L::REJECTED] ?? 0],
    ];
    $newHasErrors = $errors->hasAny(['user_id', 'vehicle_id', 'duration_days']);
@endphp

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Locations</h1>
        <p class="muted">Validez les demandes, enregistrez les retours et gérez les prolongations.</p>
    </div>
    <div class="page-head__actions">
        <button type="button" class="btn btn--primary" data-dialog-open="new"><x-icon name="plus" size="sm" /> Nouvelle location</button>
    </div>
</div>

<div class="toolbar">
    <nav class="tabs" aria-label="Filtrer par statut">
        @foreach ($tabs as $key => [$label, $n])
            <a href="{{ route('admin.loans.index', $key ? ['status' => $key] : []) }}" @if ($filter == $key) aria-current="page" @endif>
                {{ $label }} <span class="tabs__n">{{ $n }}</span>
            </a>
        @endforeach
    </nav>
</div>

<section class="panel panel--flush">
    @if ($loans->isEmpty())
        <x-empty icon="key" title="Aucune location ici" text="Les demandes faites par les clients en ligne apparaissent automatiquement. Vous pouvez aussi créer une location au comptoir.">
            <button type="button" class="btn" data-dialog-open="new">Créer une location</button>
        </x-empty>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Véhicule</th><th>Client</th><th>Période</th><th class="right">Montant</th><th>Statut</th><th class="right"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loans as $loan)
                        <tr class="{{ $loan->status === $L::EXPIRED ? 'is-alert' : (in_array($loan->status, [$L::PENDING, $L::PENDING_RENEWAL]) ? 'is-wait' : '') }}">
                            <td>
                                <div class="row row--tight" style="flex-wrap:nowrap">
                                    <x-plate :plate="$loan->vehicle?->plate ?? '—'" />
                                    <a href="{{ route('admin.loans.show', $loan) }}" class="table__link nowrap">{{ $loan->vehicle?->name ?? 'Véhicule supprimé' }}</a>
                                </div>
                            </td>
                            <td class="nowrap">
                                @if ($loan->user)
                                    <a href="{{ route('admin.users.show', $loan->user) }}" class="table__link" style="font-weight:500">{{ $loan->user->nom }}</a>
                                    <span class="table__sub">{{ $loan->user->telephone ?: $loan->user->email }}</span>
                                @else
                                    <span class="muted">Compte supprimé</span>
                                @endif
                            </td>
                            <td class="nowrap">
                                @if ($loan->start_date)
                                    {{ $loan->start_date->translatedFormat('j M') }} au {{ $loan->end_date->translatedFormat('j M') }}
                                @else
                                    <span class="muted">Pas commencée</span>
                                @endif
                                <span class="table__sub">{{ $loan->duration_days }} jour(s)@if ($loan->status === $L::EXPIRED), <span style="color:var(--alert);font-weight:600">{{ $loan->daysLate() }} j de retard</span>@elseif ($loan->status === $L::PENDING_RENEWAL), +{{ $loan->renewal_requested_days }} j demandés @endif</span>
                            </td>
                            <td class="right strong num">@money($loan->total_amount)</td>
                            <td><x-status :model="$loan" /></td>
                            <td>
                                <div class="actions">
                                    @include('admin.loans.partials.actions')
                                    <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn--sm btn--ghost btn--icon" aria-label="Voir la fiche"><x-icon name="chevron-right" size="sm" /></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>

<x-dialog id="new" title="Nouvelle location au comptoir" :open-on-load="$newHasErrors">
    <form method="POST" action="{{ route('admin.loans.store') }}" data-booking data-price="0">
        @csrf
        <div class="modal__body stack">
            <p class="small muted">La location commence aujourd’hui et le paiement est enregistré immédiatement.</p>
            <x-field name="user_id" type="select" label="Client" placeholder="Choisir un client" required
                     :options="$clients->mapWithKeys(fn ($c) => [$c->id => $c->nom . ' (' . $c->email . ')'])->all()" />
            <x-field name="vehicle_id" type="select" label="Véhicule disponible" placeholder="Choisir un véhicule" required
                     :options="$vehicles->mapWithKeys(fn ($v) => [$v->id => $v->name . ' (' . $v->plate . '), ' . \App\Providers\AppServiceProvider::money($v->loan_price) . ' / jour'])->all()" />
            <x-field name="duration_days" type="number" label="Durée (jours)" :value="3" min="1" max="365" required />
        </div>
        <div class="modal__foot">
            <button type="button" class="btn" data-dialog-close>Annuler</button>
            <button type="submit" class="btn btn--primary">Créer la location</button>
        </div>
    </form>
</x-dialog>
@endsection
