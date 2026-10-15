@extends($layout)

@php
    $T = \App\Models\Ticket::class;
    $money = fn ($v) => \App\Providers\AppServiceProvider::money($v);
    $date = fn ($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('j M Y') : '—';
    $km = fn ($v) => $v !== null ? number_format($v, 0, ',', ' ') . ' km' : '—';
    $lines = $ticket->line('lines', []);
    $method = \App\Models\Transaction::METHODS[$ticket->line('method')] ?? null;
    $isStaff = $layout === 'layouts.admin';
@endphp

@section('title', $ticket->title . ' ' . $ticket->code)

@section('crumbs')
    <a href="{{ route('counter.tickets.index') }}">Tickets</a>
    <x-icon name="chevron-right" size="sm" />
    <span aria-current="page">{{ $ticket->code }}</span>
@endsection

@section('content')
<div class="receipt-wrap">
    {{-- Tikè a menm: fòma 80 mm pou enprimant tikè, li parèt sèl sou papye a --}}
    <article class="receipt" aria-label="{{ $ticket->title }}">
        <header class="receipt__head">
            <div class="receipt__brand">AutoGestion</div>
            <div style="font-size:11px;color:#555">Simon, Les Cayes, +509 40 16 03 35</div>
            <div class="receipt__title">{{ mb_strtoupper($ticket->title) }}</div>
            <div class="receipt__code">{{ $ticket->code }}</div>
            <div style="font-size:12px">{{ $ticket->created_at->translatedFormat('j F Y, H:i') }}</div>
        </header>

        <div class="receipt__sec">
            <div class="receipt__row"><span>Client</span><strong>{{ $ticket->client?->nom ?? '—' }}</strong></div>
            @if ($ticket->client?->telephone)<div class="receipt__row"><span>Téléphone</span><strong>{{ $ticket->client->telephone }}</strong></div>@endif
            <div class="receipt__row" style="align-items:center;margin-top:4px"><span>{{ $ticket->vehicle?->name ?? 'Véhicule' }}</span>@if ($ticket->vehicle)<x-plate :plate="$ticket->vehicle->plate" />@endif</div>
            <div class="receipt__row"><span>Référence</span><strong>{{ $ticket->line('reference') }}</strong></div>
        </div>

        @if ($ticket->kind === $T::CHECKOUT)
            <div class="receipt__sec">
                <div class="receipt__row"><span>Départ</span><strong>{{ $date($ticket->line('start')) }}</strong></div>
                <div class="receipt__row"><span>Retour prévu</span><strong>{{ $date($ticket->line('end')) }}</strong></div>
                <div class="receipt__row"><span>Kilométrage</span><strong>{{ $km($ticket->line('km')) }}</strong></div>
                <div class="receipt__row"><span>Carburant</span><strong>{{ \App\Models\LoanCart::fuelLabel($ticket->line('fuel')) }}</strong></div>
                @if ($ticket->line('deposit'))<div class="receipt__row"><span>Caution reçue</span><strong>{{ $money($ticket->line('deposit')) }}</strong></div>@endif
                @if ($ticket->line('condition'))<div style="margin-top:4px"><span style="color:#555">État :</span> {{ $ticket->line('condition') }}</div>@endif
            </div>
        @elseif ($ticket->kind === $T::CHECKIN)
            <div class="receipt__sec">
                <div class="receipt__row"><span>Sortie</span><strong>{{ $date($ticket->line('start')) }}</strong></div>
                <div class="receipt__row"><span>Retour prévu</span><strong>{{ $date($ticket->line('end')) }}</strong></div>
                <div class="receipt__row"><span>Rendu le</span><strong>{{ $date($ticket->line('returned')) }}</strong></div>
                @if ($ticket->line('days_late'))<div class="receipt__row"><span>Retard</span><strong>{{ $ticket->line('days_late') }} jour(s)</strong></div>@endif
                <div class="receipt__row"><span>Km départ / retour</span><strong>{{ $km($ticket->line('km_out')) }} / {{ $km($ticket->line('km_in')) }}</strong></div>
                <div class="receipt__row"><span>Carburant départ / retour</span><strong>{{ \App\Models\LoanCart::fuelLabel($ticket->line('fuel_out')) }} / {{ \App\Models\LoanCart::fuelLabel($ticket->line('fuel_in')) }}</strong></div>
                @if ($ticket->line('deposit'))<div class="receipt__row"><span>Caution {{ $money($ticket->line('deposit')) }}</span><strong>{{ $ticket->line('deposit_returned') ? 'Rendue' : 'Retenue' }}</strong></div>@endif
                @if ($ticket->line('condition'))<div style="margin-top:4px"><span style="color:#555">État :</span> {{ $ticket->line('condition') }}</div>@endif
            </div>
        @endif

        <div class="receipt__sec">
            @forelse ($lines as $l)
                <div class="receipt__row"><span>{{ $l['label'] }}</span><strong>{{ $money($l['amount']) }}</strong></div>
            @empty
                <div class="receipt__row"><span>Aucun montant à régler</span><strong>{{ $money(0) }}</strong></div>
            @endforelse
            <div class="receipt__total"><span>{{ $ticket->amount ? 'Payé' : 'Total' }}</span><strong>{{ $money($ticket->amount) }}</strong></div>
            @if ($method && $ticket->amount)<div class="receipt__row"><span>Mode de paiement</span><strong>{{ $method }}</strong></div>@endif
        </div>

        <div class="receipt__sign">
            <div>Signature du client</div>
            <div>Agent : {{ $ticket->agent?->nom ?? '—' }}</div>
        </div>

        <p class="receipt__foot">
            @switch($ticket->kind)
                @case($T::CHECKOUT) Conservez ce bon et présentez-le au retour du véhicule. @break
                @case($T::CHECKIN) Merci d’avoir choisi AutoGestion. @break
                @default Clés et documents du véhicule remis au client.
            @endswitch
        </p>
    </article>

    <div class="stack no-print">
        <div>
            <h1 class="h1">{{ $ticket->title }}</h1>
            <p class="muted" style="margin-top:6px">Format ticket 80 mm. Sur une imprimante normale, il s’imprime en haut de la page.</p>
        </div>
        <div class="row">
            <button type="button" class="btn btn--primary btn--lg" onclick="window.print()"><x-icon name="printer" /> Imprimer</button>
            @if ($isStaff && $ticket->loan)
                <a href="{{ route('counter.loans.show', $ticket->loan) }}" class="btn btn--lg">Voir la location</a>
            @endif
            @if ($isStaff)
                <a href="{{ route('counter.index') }}" class="btn btn--lg btn--ghost">Retour au comptoir</a>
            @else
                <a href="{{ route('client.transactions') }}" class="btn btn--lg btn--ghost">Mes paiements</a>
            @endif
        </div>
    </div>
</div>
@endsection
