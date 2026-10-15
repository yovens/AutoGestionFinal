@extends('layouts.admin')

@section('title', 'Tickets')

@php $T = \App\Models\Ticket::class; @endphp

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Tickets du {{ $date->translatedFormat('j F Y') }}</h1>
        <p class="muted">Bons de sortie, de retour et de remise imprimés au comptoir.</p>
    </div>
    <form method="GET" class="row row--tight">
        @if ($kind)<input type="hidden" name="kind" value="{{ $kind }}">@endif
        <label class="sr-only" for="date">Date</label>
        <input class="input" type="date" id="date" name="date" value="{{ $date->toDateString() }}" max="{{ now()->toDateString() }}" style="width:auto">
        <button class="btn">Afficher</button>
    </form>
</div>

<div class="toolbar">
    <nav class="tabs" aria-label="Type de ticket">
        <a href="{{ route('counter.tickets.index', ['date' => $date->toDateString()]) }}" @if (!$kind) aria-current="page" @endif>Tous</a>
        @foreach ($T::TITLES as $k => $label)
            <a href="{{ route('counter.tickets.index', ['date' => $date->toDateString(), 'kind' => $k]) }}" @if ($kind === $k) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>
</div>

<section class="panel panel--flush">
    @if ($tickets->isEmpty())
        <x-empty icon="printer" title="Aucun ticket ce jour-là" text="Un ticket est créé à chaque remise de clés, retour de véhicule ou vente encaissée." />
    @else
        <div class="table-wrap"><table class="table">
            <thead><tr><th>Ticket</th><th>Heure</th><th>Client</th><th>Véhicule</th><th>Agent</th><th class="right">Montant</th><th class="right"><span class="sr-only">Actions</span></th></tr></thead>
            <tbody>
                @foreach ($tickets as $t)
                    <tr>
                        <td><a href="{{ route('counter.tickets.show', $t) }}" class="table__link num">{{ $t->code }}</a><span class="table__sub">{{ $t->title }}</span></td>
                        <td class="num">{{ $t->created_at->format('H:i') }}</td>
                        <td>{{ $t->client?->nom ?? '—' }}</td>
                        <td>@if ($t->vehicle)<x-plate :plate="$t->vehicle->plate" />@else — @endif</td>
                        <td>{{ $t->agent?->nom ?? '—' }}</td>
                        <td class="right strong num">@money($t->amount)</td>
                        <td><div class="actions"><a href="{{ route('counter.tickets.show', $t) }}" class="btn btn--sm"><x-icon name="printer" size="sm" /> Ouvrir</a></div></td>
                    </tr>
                @endforeach
            </tbody>
        </table></div>
    @endif
</section>
@endsection
