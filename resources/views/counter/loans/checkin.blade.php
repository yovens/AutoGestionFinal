@extends('layouts.admin')

@section('title', 'Retour du véhicule')

@php
    $v = $loan->vehicle; $c = $loan->user;
    $late = $loan->suggestedLateFee();
    $unpaidTotal = $unpaid->sum('amount');
@endphp

@section('crumbs')
    <a href="{{ route('counter.index') }}">Comptoir</a>
    <x-icon name="chevron-right" size="sm" />
    <a href="{{ route('counter.loans.show', $loan) }}">{{ $loan->reference }}</a>
    <x-icon name="chevron-right" size="sm" />
    <span aria-current="page">Retour</span>
@endsection

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Retour du véhicule</h1>
        <p class="muted">Comparez avec l’état au départ, ajoutez les frais éventuels, encaissez le solde.</p>
    </div>
    <x-status :model="$loan" />
</div>

<form method="POST" action="{{ route('counter.loans.checkin.store', $loan) }}" class="desk" data-sum data-base="{{ $unpaidTotal }}" novalidate>
    @csrf
    <section class="panel">
        <div class="check-step">
            <span class="check-step__n">1</span>
            <div class="check-step__body">
                <h2 class="h3">Kilométrage et carburant</h2>
                <dl class="dl" style="max-width:420px">
                    <dt>Au départ</dt><dd>{{ $loan->km_out !== null ? number_format($loan->km_out, 0, ',', ' ') . ' km' : '—' }}</dd>
                    <dt>Carburant au départ</dt><dd><x-fuel-read :level="$loan->fuel_out" /></dd>
                </dl>
                <div class="form-grid">
                    <x-field name="km_in" type="number" label="Kilométrage au retour" :min="$loan->km_out ?? 0" required inputmode="numeric" />
                </div>
                <x-fuel name="fuel_in" label="Niveau de carburant au retour" :value="$loan->fuel_out" id-prefix="fi" />
            </div>
        </div>

        <div class="check-step">
            <span class="check-step__n">2</span>
            <div class="check-step__body">
                <h2 class="h3">État du véhicule</h2>
                @if ($loan->condition_out)
                    <p class="small"><span class="muted">Noté au départ :</span> {{ $loan->condition_out }}</p>
                @endif
                <x-field name="condition_in" type="textarea" label="Constat au retour" rows="3" placeholder="Ex. rien à signaler, ou nouvelle rayure pare-chocs avant" />
            </div>
        </div>

        <div class="check-step">
            <span class="check-step__n">3</span>
            <div class="check-step__body">
                <h2 class="h3">Frais</h2>
                <div class="form-grid">
                    <x-field name="late_fee" type="number" label="Frais de retard ($)" :value="$late" min="0" data-add
                             :hint="$loan->daysLate() ? $loan->daysLate() . ' jour(s) × ' . \App\Providers\AppServiceProvider::money($v?->loan_price ?? 0) . ', modifiable' : 'Pas de retard'" />
                    <x-field name="extra_fee" type="number" label="Autres frais ($)" :value="0" min="0" data-add hint="Carburant manquant, dégâts, nettoyage..." />
                    <x-field name="extra_reason" label="Motif des autres frais" class="span-2" maxlength="120" placeholder="Ex. réservoir à 1/4 au lieu de plein" />
                </div>
                @if ($loan->deposit > 0)
                    <label class="confirm-box">
                        <input type="checkbox" name="deposit_returned" value="1" @checked(old('deposit_returned', true))>
                        <span><strong>Caution de @money($loan->deposit) rendue au client</strong><br><span class="small muted">Décochez si elle est retenue pour couvrir des frais.</span></span>
                    </label>
                @endif
            </div>
        </div>

        <div class="check-step" data-needs-payment>
            <span class="check-step__n">4</span>
            <div class="check-step__body">
                <h2 class="h3">Encaisser le solde</h2>
                <x-methods id-prefix="ci" />
            </div>
        </div>
    </section>

    <aside class="desk__aside stack">
        <section class="panel">
            <div class="panel__body stack">
                <div class="row" style="flex-wrap:nowrap">
                    @if ($v?->image_url)<img src="{{ $v->image_url }}" alt="" class="thumb" style="width:88px;height:60px">@endif
                    <div>
                        <x-plate :plate="$v?->plate ?? '—'" />
                        <div class="strong" style="margin-top:4px">{{ $v?->name }}</div>
                        <div class="small muted">{{ $c?->nom }}</div>
                    </div>
                </div>
                <div class="bill">
                    <div class="bill__row"><span class="muted">Sortie le</span><span class="bill__value">{{ $loan->start_date?->translatedFormat('j M') }}</span></div>
                    <div class="bill__row"><span class="muted">Retour prévu</span><span class="bill__value">{{ $loan->end_date?->translatedFormat('j M') }}</span></div>
                    @if ($loan->daysLate())<div class="bill__row" style="color:var(--alert)"><span>Retard</span><span class="bill__value">{{ $loan->daysLate() }} jour(s)</span></div>@endif
                    <div class="bill__row"><span class="muted">Location déjà réglée</span><span class="bill__value">@money($loan->total_amount - $unpaidTotal)</span></div>
                    @foreach ($unpaid as $t)
                        <div class="bill__row"><span>{{ $t->note ?: 'Prolongation' }}, non réglée</span><span class="bill__value">@money($t->amount)</span></div>
                    @endforeach
                    <div class="bill__row bill__row--total"><span>Solde à encaisser</span><span class="bill__big num" data-sum-total>@money($unpaidTotal + $late)</span></div>
                </div>
            </div>
            <div class="panel__foot">
                <button type="submit" class="btn btn--primary btn--lg btn--block"><x-icon name="return" /> Valider le retour</button>
            </div>
        </section>
        <p class="small muted">Le véhicule redevient disponible et le bon de retour s’ouvre pour impression.</p>
    </aside>
</form>
@endsection
