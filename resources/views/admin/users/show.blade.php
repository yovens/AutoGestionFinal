@extends('layouts.admin')

@section('title', $user->nom)

@section('crumbs')
    <a href="{{ route('admin.users.index') }}">Comptes</a>
    <x-icon name="chevron-right" size="sm" />
    <span aria-current="page">{{ $user->nom }}</span>
@endsection

@php
    $isMe = $user->is(auth()->user());
    $canManage = auth()->user()->canManage($user);
    $roles = auth()->user()->assignableRoles();
    if (!array_key_exists($user->role, $roles)) { $roles = [$user->role => $user->role_label] + $roles; }
    $paid = $user->transactions->where('status', \App\Models\Transaction::COMPLETED)->sum('amount');
@endphp

@section('content')
<div class="page-head">
    <div class="person">
        <x-avatar :user="$user" size="lg" />
        <div>
            <h1 class="h1">{{ $user->nom }}</h1>
            <div class="row row--tight" style="margin-top:6px">
                <x-badge :tone="$user->isStaff() ? 'brand' : 'off'" class="badge--plain">{{ $user->role_label }}</x-badge>
                @if ($user->isBlocked())<x-badge tone="alert">Suspendu</x-badge>@else<x-badge tone="ok">Actif</x-badge>@endif
                <span class="small muted">Inscrit le {{ $user->created_at?->translatedFormat('j M Y') }}</span>
            </div>
        </div>
    </div>
    @if ($canManage)
        <div class="page-head__actions">
            <form method="POST" action="{{ route('admin.users.block', $user) }}" data-confirm="{{ $user->isBlocked() ? 'Réactiver' : 'Suspendre' }} ce compte ?">
                @csrf
                <button type="submit" class="btn"><x-icon :name="$user->isBlocked() ? 'unlock' : 'lock'" size="sm" /> {{ $user->isBlocked() ? 'Réactiver' : 'Suspendre' }}</button>
            </form>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Supprimer définitivement le compte de {{ $user->nom }} et tout son historique ?">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn--danger"><x-icon name="trash" size="sm" /> Supprimer</button>
            </form>
        </div>
    @endif
</div>

<div class="stats" style="--cols:3;margin-bottom:var(--s5)">
    <div class="stat"><div class="stat__label">Locations</div><div class="stat__value num">{{ $user->loans->count() }}</div></div>
    <div class="stat"><div class="stat__label">En cours</div><div class="stat__value num">{{ $user->loans->whereIn('status', \App\Models\LoanCart::HOLDING)->count() }}</div></div>
    <div class="stat"><div class="stat__label">Total réglé</div><div class="stat__value num">@money($paid)</div></div>
</div>

<div class="grid-main grid-main--wide-side">
    <div class="stack">
        <section class="panel panel--flush">
            <div class="panel__head"><h2 class="h3">Locations</h2></div>
            @if ($user->loans->isEmpty())
                <x-empty icon="key" title="Aucune location" />
            @else
                <div class="table-wrap"><table class="table">
                    <thead><tr><th>Véhicule</th><th>Période</th><th>Statut</th><th class="right">Montant</th></tr></thead>
                    <tbody>
                        @foreach ($user->loans as $loan)
                            <tr>
                                <td><div class="row row--tight" style="flex-wrap:nowrap"><x-plate :plate="$loan->vehicle?->plate ?? '—'" /><a href="{{ route('counter.loans.show', $loan) }}" class="table__link nowrap">{{ $loan->vehicle?->name ?? 'Véhicule supprimé' }}</a></div></td>
                                <td class="nowrap">{{ $loan->start_date ? $loan->start_date->translatedFormat('j M') . ' au ' . $loan->end_date->translatedFormat('j M Y') : 'Pas commencée' }}</td>
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
            @if ($user->transactions->isEmpty())
                <x-empty icon="receipt" title="Aucun paiement" />
            @else
                <div class="table-wrap"><table class="table">
                    <thead><tr><th>Référence</th><th>Véhicule</th><th>Type</th><th>Statut</th><th class="right">Montant</th></tr></thead>
                    <tbody>
                        @foreach ($user->transactions as $t)
                            <tr>
                                <td><a href="{{ route('admin.transactions.show', $t) }}" class="table__link num">{{ $t->reference }}</a><span class="table__sub">{{ $t->created_at->translatedFormat('j M Y') }}</span></td>
                                <td>{{ $t->vehicle?->name ?? '—' }}</td>
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
        <div class="panel__head"><h2 class="h3">Informations du compte</h2></div>
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div class="panel__body stack">
                <x-field name="nom" label="Nom complet" :value="$user->nom" required :readonly="!$canManage && !$isMe" />
                <x-field name="email" type="email" label="E-mail" :value="$user->email" required :readonly="!$canManage && !$isMe" />
                <x-field name="telephone" type="tel" label="Téléphone" :value="$user->telephone" :readonly="!$canManage && !$isMe" />
                <x-field name="role" type="select" label="Rôle" :value="$user->role" :options="$roles" :disabled="$isMe || !$canManage"
                         :hint="$isMe ? 'Vous ne pouvez pas changer votre propre rôle.' : (!$canManage ? 'Réservé au super administrateur.' : null)" />
                @if ($isMe || !$canManage)<input type="hidden" name="role" value="{{ $user->role }}">@endif
            </div>
            @if ($canManage || $isMe)
                <div class="panel__foot row row--end"><button type="submit" class="btn btn--primary">Enregistrer</button></div>
            @endif
        </form>
    </section>
</div>
@endsection
