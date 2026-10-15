@extends('layouts.admin')

@section('title', $client->nom)

@section('crumbs')
    <a href="{{ route('counter.clients.index') }}">Clients</a>
    <x-icon name="chevron-right" size="sm" />
    <span aria-current="page">{{ $client->nom }}</span>
@endsection

@php
    $L = \App\Models\LoanCart::class;
    $active = $client->loans->whereIn('status', $L::HOLDING);
    $history = $client->loans->diff($active);
    $paid = $client->transactions->where('status', \App\Models\Transaction::COMPLETED)->sum('amount');
@endphp

@section('content')
<div class="page-head">
    <div class="person">
        <x-avatar :user="$client" size="lg" />
        <div>
            <h1 class="h1">{{ $client->nom }}</h1>
            <p class="muted">
                @if ($client->telephone)<a href="tel:{{ preg_replace('/\s+/', '', $client->telephone) }}">{{ $client->telephone }}</a>, @endif
                {{ str_ends_with($client->email, '.local') ? 'sans e-mail' : $client->email }}
                @if ($client->isBlocked()) <x-badge tone="alert">Suspendu</x-badge>@endif
            </p>
        </div>
    </div>
    <div class="page-head__actions">
        @unless ($client->isBlocked())
            <a href="{{ route('counter.loans.create', ['client' => $client->id]) }}" class="btn btn--primary"><x-icon name="plus" size="sm" /> Nouvelle location</a>
        @endunless
        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.users.show', $client) }}" class="btn">Gérer le compte</a>
        @endif
    </div>
</div>

<div class="stats" style="--cols:3;margin-bottom:var(--s5)">
    <div class="stat"><div class="stat__label">Locations</div><div class="stat__value num">{{ $client->loans->count() }}</div></div>
    <div class="stat"><div class="stat__label">En cours</div><div class="stat__value num">{{ $active->count() }}</div></div>
    <div class="stat"><div class="stat__label">Total réglé</div><div class="stat__value num">@money($paid)</div></div>
</div>

<div class="stack-lg">
    <section class="panel panel--flush">
        <div class="panel__head"><h2 class="h3">En cours et à venir</h2></div>
        @if ($active->isEmpty())
            <x-empty icon="key" title="Aucune location en cours" />
        @else
            @include('counter.loans.partials.table', ['loans' => $active->each->setRelation('user', $client)])
        @endif
    </section>

    @if ($history->isNotEmpty())
        <section class="panel panel--flush">
            <div class="panel__head"><h2 class="h3">Historique</h2></div>
            @include('counter.loans.partials.table', ['loans' => $history->each->setRelation('user', $client)])
        </section>
    @endif
</div>
@endsection
