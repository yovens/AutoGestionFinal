@extends('layouts.admin')

@section('title', 'Remise des clés')

@php $v = $loan->vehicle; $c = $loan->user; @endphp

@section('crumbs')
    <a href="{{ route('counter.index') }}">Comptoir</a>
    <x-icon name="chevron-right" size="sm" />
    <a href="{{ route('counter.loans.show', $loan) }}">{{ $loan->reference }}</a>
    <x-icon name="chevron-right" size="sm" />
    <span aria-current="page">Remise des clés</span>
@endsection

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Remise des clés</h1>
        <p class="muted">Vérifiez le client, notez l’état du véhicule, encaissez. La location commence à la validation.</p>
    </div>
    <span class="pickup-code"><small>Code</small>{{ $loan->pickup_code }}</span>
</div>

<form method="POST" action="{{ route('counter.loans.checkout.store', $loan) }}" class="desk" novalidate>
    @csrf
    <section class="panel">
        <div class="check-step">
            <span class="check-step__n">1</span>
            <div class="check-step__body">
                <h2 class="h3">Identité du client</h2>
                <div class="person">
                    <x-avatar :user="$c" size="lg" />
                    <div>
                        <div class="h2">{{ $c?->nom }}</div>
                        <div class="small muted">{{ $c?->telephone ?: 'Pas de téléphone' }}, {{ $c?->email }}</div>
                    </div>
                </div>
                <div class="form-grid">
                    <x-field name="id_document" label="N° de pièce d’identité ou permis" placeholder="Ex. NIF, CIN, permis" hint="Facultatif, utile en cas de litige." maxlength="60" />
                </div>
                <label class="confirm-box">
                    <input type="checkbox" name="id_checked" value="1" @checked(old('id_checked')) required>
                    <span><strong>J’ai vérifié la pièce d’identité.</strong><br><span class="small muted">Le nom correspond à celui de la réservation.</span></span>
                </label>
                @error('id_checked')<span class="field__error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="check-step">
            <span class="check-step__n">2</span>
            <div class="check-step__body">
                <h2 class="h3">État du véhicule au départ</h2>
                <div class="form-grid">
                    <x-field name="km_out" type="number" label="Kilométrage au compteur" :value="$lastKm" min="0" required inputmode="numeric"
                             :hint="$lastKm ? 'Dernier relevé au retour : ' . number_format($lastKm, 0, ',', ' ') . ' km' : null" />
                </div>
                <x-fuel name="fuel_out" label="Niveau de carburant" :value="4" id-prefix="fo" />
                <x-field name="condition_out" type="textarea" label="Rayures, chocs, remarques" rows="3" placeholder="Ex. rayure portière arrière gauche, roue de secours présente" />
            </div>
        </div>

        <div class="check-step">
            <span class="check-step__n">3</span>
            <div class="check-step__body">
                <h2 class="h3">Paiement</h2>
                <x-methods id-prefix="co" required />
                <div class="form-grid">
                    <x-field name="deposit" type="number" label="Caution reçue ($)" :value="$agency['default_deposit']" min="0" hint="Rendue au retour si tout est en ordre." />
                </div>
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
                    </div>
                </div>
                <div class="bill">
                    <div class="bill__row"><span class="muted">Départ</span><span class="bill__value">Aujourd’hui</span></div>
                    <div class="bill__row"><span class="muted">Retour prévu</span><span class="bill__value">{{ now()->addDays($loan->duration_days)->translatedFormat('l j F') }}</span></div>
                    <div class="bill__row"><span class="muted">{{ $loan->duration_days }} jour(s) × @money($v?->loan_price ?? 0)</span><span class="bill__value">@money($loan->total_amount)</span></div>
                    <div class="bill__row bill__row--total"><span>À encaisser</span><span class="bill__big num">@money($loan->total_amount)</span></div>
                </div>
            </div>
            <div class="panel__foot">
                <button type="submit" class="btn btn--primary btn--lg btn--block"><x-icon name="key" /> Encaisser et remettre les clés</button>
            </div>
        </section>
        <p class="small muted">Le bon de sortie s’ouvre ensuite pour impression. Faites-le signer par le client.</p>
    </aside>
</form>
@endsection
