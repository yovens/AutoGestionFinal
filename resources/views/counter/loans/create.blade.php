@extends('layouts.admin')

@section('title', 'Location sur place')

@section('crumbs')
    <a href="{{ route('counter.index') }}">Comptoir</a>
    <x-icon name="chevron-right" size="sm" />
    <span aria-current="page">Location sur place</span>
@endsection

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Location sur place</h1>
        <p class="muted">Pour un client présent à l’agence. Ensuite vous passez directement à la remise des clés.</p>
    </div>
</div>

<div class="desk">
    <section class="panel">
        <form method="POST" action="{{ route('counter.loans.store') }}" id="counter-loan" data-booking data-price="0">
            @csrf
            <div class="check-step">
                <span class="check-step__n">1</span>
                <div class="check-step__body">
                    <h2 class="h3">Client</h2>
                    <x-field name="user_id" type="select" label="Client existant" placeholder="Choisir un client" required :value="$selected"
                             :options="$clients->mapWithKeys(fn ($c) => [$c->id => $c->nom . ($c->telephone ? ', ' . $c->telephone : '')])->all()" />
                    <p class="small muted">Nouveau client ? <button type="button" class="btn btn--sm" data-dialog-open="new-client"><x-icon name="plus" size="sm" /> Enregistrer le client</button></p>
                </div>
            </div>
            <div class="check-step">
                <span class="check-step__n">2</span>
                <div class="check-step__body">
                    <h2 class="h3">Véhicule et durée</h2>
                    @if ($vehicles->isEmpty())
                        <div class="flash flash--alert"><x-icon name="alert" /><span>Aucun véhicule disponible pour le moment.</span></div>
                    @else
                        <div class="field">
                            <label class="field__label" for="vehicle_id">Véhicule disponible</label>
                            <select class="select" id="vehicle_id" name="vehicle_id" required data-price-source>
                                <option value="">Choisir un véhicule</option>
                                @foreach ($vehicles as $v)
                                    <option value="{{ $v->id }}" data-price="{{ $v->loan_price }}" @selected(old('vehicle_id') == $v->id)>{{ $v->name }} ({{ $v->plate }}), {{ \App\Providers\AppServiceProvider::money($v->loan_price) }} / jour</option>
                                @endforeach
                            </select>
                            @error('vehicle_id')<span class="field__error">{{ $message }}</span>@enderror
                        </div>
                    @endif
                    <div class="field">
                        <label class="field__label" for="duration_days">Durée (jours)</label>
                        <div class="input-group" style="max-width:220px">
                            <button type="button" class="btn" data-step="-1" aria-label="Un jour de moins"><x-icon name="minus" size="sm" /></button>
                            <input class="input" id="duration_days" type="number" name="duration_days" min="1" max="365" value="{{ old('duration_days', 1) }}" data-days required>
                            <button type="button" class="btn" data-step="1" aria-label="Un jour de plus"><x-icon name="plus" size="sm" /></button>
                        </div>
                        @error('duration_days')<span class="field__error">{{ $message }}</span>@enderror
                    </div>
                    <div class="total-line"><span class="muted">Total pour <span data-days-label>1 jour</span></span><span class="total-line__value num" data-total>—</span></div>
                </div>
            </div>
            <div class="panel__foot row row--end">
                <button type="submit" class="btn btn--primary btn--lg" @disabled($vehicles->isEmpty())>Continuer vers la remise des clés</button>
            </div>
        </form>
    </section>

    <aside class="desk__aside panel">
        <div class="panel__body stack">
            <h2 class="h3">À préparer</h2>
            <ul class="small" style="margin:0;padding-left:18px;color:var(--text-2)">
                <li>Pièce d’identité ou permis du client</li>
                <li>Kilométrage et niveau de carburant du véhicule</li>
                <li>Le paiement (espèces, MonCash, NatCash, carte ou virement)</li>
                <li>La caution, si l’agence en demande une</li>
            </ul>
        </div>
    </aside>
</div>

<x-dialog id="new-client" title="Enregistrer un client" size="sm" :open-on-load="$errors->hasAny(['nom', 'telephone', 'email'])">
    <form method="POST" action="{{ route('counter.clients.store') }}">
        @csrf
        <input type="hidden" name="return_to" value="loan">
        <div class="modal__body stack">
            <x-field name="nom" label="Nom complet" required />
            <x-field name="telephone" type="tel" label="Téléphone" placeholder="+509" required />
            <x-field name="email" type="email" label="E-mail" hint="Facultatif. Avec un e-mail, le client pourra se connecter à son espace." />
        </div>
        <div class="modal__foot">
            <button type="button" class="btn" data-dialog-close>Annuler</button>
            <button type="submit" class="btn btn--primary">Enregistrer</button>
        </div>
    </form>
</x-dialog>
@endsection
