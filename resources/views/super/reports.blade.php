@extends('layouts.admin')

@section('title', 'Rapports')

@php
    $fmt = fn ($d) => $d->toDateString();
    $presets = [
        'Ce mois'      => [now()->startOfMonth(), now()],
        'Mois dernier' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
        '3 derniers mois' => [now()->subMonthsNoOverflow(2)->startOfMonth(), now()],
        'Cette année'  => [now()->startOfYear(), now()],
    ];
    $maxMonth = max(1, $months->max('total'));
    $maxMethod = max(1, $byMethod->max('total'));
    $q = ['from' => $fmt($from), 'to' => $fmt($to)];
@endphp

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Rapports</h1>
        <p class="muted">Argent réellement encaissé du {{ $from->translatedFormat('j M Y') }} au {{ $to->translatedFormat('j M Y') }}.</p>
    </div>
    <div class="page-head__actions">
        <a href="{{ route('super.reports.export', $q) }}" class="btn"><x-icon name="upload" size="sm" style="transform:rotate(180deg)" /> Exporter en CSV (Excel)</a>
    </div>
</div>

<form method="GET" class="toolbar">
    <nav class="tabs" aria-label="Période">
        @foreach ($presets as $label => [$a, $b])
            <a href="{{ route('super.reports', ['from' => $fmt($a), 'to' => $fmt($b)]) }}" @if ($fmt($a) === $fmt($from) && $fmt($b) === $fmt($to)) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>
    <div class="row row--tight">
        <label class="sr-only" for="from">Du</label><input class="input" type="date" id="from" name="from" value="{{ $fmt($from) }}" style="width:auto">
        <span class="muted">au</span>
        <label class="sr-only" for="to">Au</label><input class="input" type="date" id="to" name="to" value="{{ $fmt($to) }}" style="width:auto">
        <button class="btn">Afficher</button>
    </div>
</form>

<div class="stats" style="--cols:4;margin-bottom:var(--s5)">
    <div class="stat"><div class="stat__label">Encaissé</div><div class="stat__value num">@money($totals['revenue'])</div><div class="stat__note">{{ $totals['count'] }} paiement(s)</div></div>
    <div class="stat"><div class="stat__label">Locations</div><div class="stat__value num">@money($totals['rentals'])</div><div class="stat__note">{{ $totals['revenue'] ? round($totals['rentals'] / $totals['revenue'] * 100) : 0 }} % du total</div></div>
    <div class="stat"><div class="stat__label">Ventes</div><div class="stat__value num">@money($totals['sales'])</div><div class="stat__note">{{ $totals['revenue'] ? round($totals['sales'] / $totals['revenue'] * 100) : 0 }} % du total</div></div>
    <div class="stat"><div class="stat__label">Frais (retards, dégâts)</div><div class="stat__value num">@money($totals['fees'])</div><div class="stat__note">{{ $fleet['late'] }} location(s) en retard aujourd’hui</div></div>
</div>

<section class="panel" style="margin-bottom:var(--s5)" aria-labelledby="t-12">
    <div class="panel__head"><h2 class="h3" id="t-12">Encaissé sur 12 mois</h2><span class="small muted">Tous types confondus</span></div>
    <div class="bars" style="grid-template-columns:repeat(12,minmax(0,1fr));height:220px" role="img" aria-label="Encaissements des douze derniers mois">
        @foreach ($months as $m)
            <div class="bars__col">
                <span class="bars__tip">{{ $m['label'] }} : {{ \App\Providers\AppServiceProvider::money($m['total']) }}</span>
                @if ($m['total'])<div class="bars__seg bars__seg--sales" style="height: {{ $m['total'] / $maxMonth * 100 }}%"></div>@endif
            </div>
        @endforeach
    </div>
    <div class="bars__labels" style="grid-template-columns:repeat(12,minmax(0,1fr))">@foreach ($months as $m)<span>{{ $m['label'] }}</span>@endforeach</div>
</section>

<div class="grid-2" style="margin-bottom:var(--s5)">
    <section class="panel" aria-labelledby="t-method">
        <div class="panel__head"><h2 class="h3" id="t-method">Par mode de paiement</h2></div>
        <div class="panel__body stack">
            @forelse ($byMethod as $m)
                <div>
                    <div class="row row--between small"><span class="strong">{{ $m['label'] }}</span><span class="num">@money($m['total']) <span class="muted">({{ $m['count'] }})</span></span></div>
                    <div class="timeline__bar" style="margin-top:6px"><div class="timeline__fill" style="width: {{ $m['total'] / $maxMethod * 100 }}%"></div></div>
                </div>
            @empty
                <p class="muted small">Aucun paiement sur la période.</p>
            @endforelse
        </div>
    </section>

    <section class="panel panel--flush" aria-labelledby="t-agent">
        <div class="panel__head"><h2 class="h3" id="t-agent">Par membre du personnel</h2></div>
        @if ($byAgent->isEmpty())
            <x-empty icon="users" title="Aucun encaissement" />
        @else
            <div class="table-wrap"><table class="table">
                <thead><tr><th>Agent</th><th class="right">Opérations</th><th class="right">Encaissé</th></tr></thead>
                <tbody>
                    @foreach ($byAgent as $a)
                        <tr>
                            <td><span class="strong">{{ $a['name'] }}</span>@if ($a['role'])<span class="table__sub">{{ $a['role'] }}</span>@endif</td>
                            <td class="right num">{{ $a['count'] }}</td>
                            <td class="right strong num">@money($a['total'])</td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
        @endif
    </section>
</div>

<section class="panel panel--flush" aria-labelledby="t-veh">
    <div class="panel__head"><h2 class="h3" id="t-veh">Véhicules qui rapportent le plus</h2><span class="small muted">Jours de location sur la période</span></div>
    @if ($byVehicle->isEmpty())
        <x-empty icon="car" title="Aucun véhicule n’a rapporté sur la période" />
    @else
        <div class="table-wrap"><table class="table">
            <thead><tr><th>Véhicule</th><th class="right">Jours loués</th><th class="right">Encaissé</th></tr></thead>
            <tbody>
                @foreach ($byVehicle as $row)
                    <tr>
                        <td>
                            @if ($row['vehicle'])
                                <div class="row row--tight" style="flex-wrap:nowrap"><x-plate :plate="$row['vehicle']->plate" /><a href="{{ route('admin.vehicles.show', $row['vehicle']) }}" class="table__link">{{ $row['vehicle']->name }}</a></div>
                            @else
                                <span class="muted">Véhicule supprimé</span>
                            @endif
                        </td>
                        <td class="right num">{{ $row['days'] }}</td>
                        <td class="right strong num">@money($row['total'])</td>
                    </tr>
                @endforeach
            </tbody>
        </table></div>
    @endif
</section>
@endsection
