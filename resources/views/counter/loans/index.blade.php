@extends('layouts.admin')

@section('title', 'Locations')

@php
    $L = \App\Models\LoanCart::class;
    $out = collect($L::OUT)->sum(fn ($s) => $counts[$s] ?? 0);
    $tabs = [
        null                => ['Toutes', $counts->sum()],
        $L::PENDING         => ['À confirmer', $counts[$L::PENDING] ?? 0],
        $L::READY           => ['À remettre', $counts[$L::READY] ?? 0],
        'out'               => ['Dehors', $out],
        $L::EXPIRED         => ['En retard', $counts[$L::EXPIRED] ?? 0],
        $L::PENDING_RENEWAL => ['Prolongations', $counts[$L::PENDING_RENEWAL] ?? 0],
        $L::RETURNED        => ['Rendues', $counts[$L::RETURNED] ?? 0],
        $L::REJECTED        => ['Refusées', $counts[$L::REJECTED] ?? 0],
        $L::CANCELLED       => ['Annulées', $counts[$L::CANCELLED] ?? 0],
    ];
@endphp

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Locations</h1>
        <p class="muted">Confirmez les demandes, remettez les clés, réceptionnez les retours.</p>
    </div>
    <div class="page-head__actions">
        <a href="{{ route('counter.loans.create') }}" class="btn btn--primary"><x-icon name="plus" size="sm" /> Location sur place</a>
    </div>
</div>

<div class="toolbar">
    <nav class="tabs" aria-label="Filtrer par statut">
        @foreach ($tabs as $key => [$label, $n])
            <a href="{{ route('counter.loans.index', $key ? ['status' => $key] : []) }}" @if ($filter == $key) aria-current="page" @endif>
                {{ $label }} <span class="tabs__n">{{ $n }}</span>
            </a>
        @endforeach
    </nav>
</div>

<section class="panel panel--flush">
    @if ($loans->isEmpty())
        <x-empty icon="key" title="Aucune location ici" text="Les demandes faites en ligne apparaissent automatiquement. Pour un client présent à l’agence, créez une location sur place.">
            <a href="{{ route('counter.loans.create') }}" class="btn">Location sur place</a>
        </x-empty>
    @else
        @include('counter.loans.partials.table', ['loans' => $loans])
    @endif
</section>
@endsection
