@extends('layouts.admin')

@section('title', 'Caisse')

@php
    $isAdmin = auth()->user()->isAdmin();
    $total = $payments->sum('amount');
@endphp

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">{{ $isAdmin ? 'Caisse' : 'Ma caisse' }} du {{ $date->translatedFormat('j F Y') }}</h1>
        <p class="muted">Tout ce qui a été encaissé au comptoir ce jour-là, par mode de paiement. Comparez avec l’argent en caisse avant de fermer.</p>
    </div>
    <form method="GET" class="row row--tight no-print">
        @if ($isAdmin && $agents->isNotEmpty())
            <label class="sr-only" for="agent">Agent</label>
            <select class="select" id="agent" name="agent" style="width:auto">
                <option value="">Tous les agents</option>
                @foreach ($agents as $a)<option value="{{ $a->id }}" @selected($agentId == $a->id)>{{ $a->nom }}</option>@endforeach
            </select>
        @endif
        <label class="sr-only" for="date">Date</label>
        <input class="input" type="date" id="date" name="date" value="{{ $date->toDateString() }}" max="{{ now()->toDateString() }}" style="width:auto">
        <button class="btn">Afficher</button>
        <button type="button" class="btn btn--ghost" onclick="window.print()"><x-icon name="printer" size="sm" /> Imprimer</button>
    </form>
</div>

<div class="stats" style="--cols:{{ count($byMethod) + 1 }};margin-bottom:var(--s5)">
    <div class="stat"><div class="stat__label">Total encaissé</div><div class="stat__value num">@money($total)</div><div class="stat__note">{{ $payments->count() }} opération(s)</div></div>
    @foreach ($byMethod as $m)
        <div class="stat"><div class="stat__label">{{ $m['label'] }}</div><div class="stat__value num" style="font-size:1.25rem">@money($m['total'])</div><div class="stat__note">{{ $m['count'] }} opération(s)</div></div>
    @endforeach
</div>

<section class="panel panel--flush">
    @if ($payments->isEmpty())
        <x-empty icon="tag" title="Rien encaissé ce jour-là" text="Les paiements apparaissent ici dès qu’une remise de clés, un retour ou une vente est encaissé au comptoir." />
    @else
        <div class="table-wrap"><table class="table">
            <thead><tr><th>Heure</th><th>Opération</th><th>Client</th><th>Véhicule</th><th>Mode</th>@if ($isAdmin)<th>Agent</th>@endif<th class="right">Montant</th></tr></thead>
            <tbody>
                @foreach ($payments as $p)
                    <tr>
                        <td class="num">{{ $p->updated_at->format('H:i') }}</td>
                        <td><span class="strong">{{ $p->note ?: $p->type_label }}</span><span class="table__sub num">{{ $p->reference }}</span></td>
                        <td>{{ $p->user?->nom ?? '—' }}</td>
                        <td>@if ($p->vehicle)<x-plate :plate="$p->vehicle->plate" />@else — @endif</td>
                        <td>{{ $p->method_label }}</td>
                        @if ($isAdmin)<td>{{ $p->recorder?->nom ?? '—' }}</td>@endif
                        <td class="right strong num">@money($p->amount)</td>
                    </tr>
                @endforeach
            </tbody>
        </table></div>
    @endif
</section>
@endsection
