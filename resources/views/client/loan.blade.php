@extends('layouts.client')

@section('title', 'Mes locations')

@php
    $current = $loans->filter(fn ($l) => in_array($l->status, \App\Models\LoanCart::HOLDING));
    $past = $loans->diff($current);
@endphp

@section('content')
<div class="page__head">
    <div>
        <h1 class="h1">Mes locations</h1>
        <p class="muted">Suivez vos demandes, les dates de retour et prolongez si besoin.</p>
    </div>
    <a href="{{ route('client.vehicles') }}" class="btn btn--primary"><x-icon name="plus" /> Nouvelle location</a>
</div>

<div class="stack-lg">
    <section class="panel" aria-labelledby="t-cur">
        <div class="panel__head"><h2 class="h3" id="t-cur">En cours et en attente</h2><span class="small muted">{{ $current->count() }}</span></div>
        @forelse ($current as $loan)
            @include('client.partials.loan-row', ['loan' => $loan])
        @empty
            <x-empty icon="key" title="Aucune location en cours" text="Choisissez un véhicule et indiquez le nombre de jours : la demande arrive directement à l’agence.">
                <a href="{{ route('client.vehicles') }}" class="btn">Voir les véhicules</a>
            </x-empty>
        @endforelse
    </section>

    @if ($past->isNotEmpty())
        <section class="panel" aria-labelledby="t-past">
            <div class="panel__head"><h2 class="h3" id="t-past">Historique</h2><span class="small muted">{{ $past->count() }}</span></div>
            @foreach ($past as $loan)
                @include('client.partials.loan-row', ['loan' => $loan])
            @endforeach
        </section>
    @endif
</div>
@endsection
