@extends('layouts.client')

@section('title', 'Véhicules')

@php $hasFilters = collect($filters)->except('sort')->filter()->isNotEmpty(); @endphp

@section('content')
<div class="page__head">
    <div>
        <h1 class="h1">Le parc</h1>
        <p class="muted">{{ $vehicles->count() }} véhicule{{ $vehicles->count() > 1 ? 's' : '' }} {{ $hasFilters ? 'correspondant à votre recherche' : 'disponibles à la location ou à l’achat' }}.</p>
    </div>
</div>

<form method="GET" action="{{ route('client.vehicles') }}" class="panel filters" role="search" style="margin-bottom:var(--s5)">
    <div class="field">
        <label class="field__label" for="q">Recherche</label>
        <div class="input-icon">
            <x-icon name="search" size="sm" />
            <input class="input" type="search" id="q" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Marque ou modèle">
        </div>
    </div>
    <div class="field">
        <label class="field__label" for="brand">Marque</label>
        <select class="select" id="brand" name="brand">
            <option value="">Toutes</option>
            @foreach ($brands as $b)<option value="{{ $b }}" @selected(($filters['brand'] ?? '') === $b)>{{ $b }}</option>@endforeach
        </select>
    </div>
    <div class="field">
        <label class="field__label" for="year">Année</label>
        <select class="select" id="year" name="year">
            <option value="">Toutes</option>
            @foreach ($years as $y)<option value="{{ $y }}" @selected(($filters['year'] ?? '') == $y)>{{ $y }}</option>@endforeach
        </select>
    </div>
    <div class="field">
        <label class="field__label" for="max">Prix d’achat max</label>
        <input class="input" type="number" id="max" name="max_price" min="0" step="1000" value="{{ $filters['max_price'] ?? '' }}" placeholder="Sans limite">
    </div>
    <div class="field">
        <label class="field__label" for="sort">Trier par</label>
        <select class="select" id="sort" name="sort">
            <option value="recent" @selected(($filters['sort'] ?? 'recent') === 'recent')>Plus récents</option>
            <option value="loan_asc" @selected(($filters['sort'] ?? '') === 'loan_asc')>Location la moins chère</option>
            <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Prix d’achat croissant</option>
            <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Prix d’achat décroissant</option>
        </select>
    </div>
    <div class="row row--tight">
        <button type="submit" class="btn btn--primary"><x-icon name="filter" size="sm" /> Filtrer</button>
        @if ($hasFilters)<a href="{{ route('client.vehicles') }}" class="btn btn--ghost">Effacer</a>@endif
    </div>
</form>

@if ($vehicles->isEmpty())
    <div class="panel">
        <x-empty icon="search" title="Aucun véhicule ne correspond" text="Élargissez la recherche : retirez un filtre ou augmentez le prix maximum.">
            <a href="{{ route('client.vehicles') }}" class="btn">Voir tout le parc</a>
        </x-empty>
    </div>
@else
    <div class="car-grid">
        @foreach ($vehicles as $vehicle)
            <x-vehicle-card :vehicle="$vehicle" />
        @endforeach
    </div>
@endif
@endsection
