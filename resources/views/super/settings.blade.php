@extends('layouts.admin')

@section('title', 'Paramètres de l’agence')

@php $s = $settings; @endphp

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Paramètres de l’agence</h1>
        <p class="muted">Ces informations apparaissent sur le site, dans l’espace client et sur les tickets imprimés. Chaque modification est inscrite au journal.</p>
    </div>
</div>

<form method="POST" action="{{ route('super.settings.update') }}" class="grid-2">
    @csrf @method('PUT')

    <section class="panel">
        <div class="panel__head"><h2 class="h3">Coordonnées</h2></div>
        <div class="panel__body stack">
            <div class="form-grid">
                <x-field name="name" label="Nom de l’agence" :value="$s['name']" required />
                <x-field name="city" label="Ville" :value="$s['city']" required hint="Affichée en pied de page et sur les tickets." />
            </div>
            <x-field name="address" label="Adresse complète" :value="$s['address']" required />
            <div class="form-grid">
                <x-field name="phone" type="tel" label="Téléphone" :value="$s['phone']" required />
                <x-field name="email" type="email" label="E-mail de contact" :value="$s['email']" required />
            </div>
            <div class="form-grid">
                <x-field name="hours" label="Horaires" :value="$s['hours']" required />
                <x-field name="hours_note" label="Précision horaires" :value="$s['hours_note']" placeholder="Ex. Samedi sur rendez-vous" />
            </div>
            <x-field name="map_query" label="Recherche pour la carte" :value="$s['map_query']" hint="Ce qui est cherché sur Google Maps pour la carte de la page d’accueil." />
        </div>
    </section>

    <div class="stack">
        <section class="panel">
            <div class="panel__head"><h2 class="h3">Règles du comptoir</h2></div>
            <div class="panel__body stack">
                <div class="form-grid">
                    <x-field name="late_fee_percent" type="number" label="Frais de retard (% du prix par jour)" :value="$s['late_fee_percent']" min="0" max="500" required
                             hint="100 % = un jour de retard coûte un jour de location. Proposé au retour, modifiable." />
                    <x-field name="default_deposit" type="number" label="Caution proposée ($)" :value="$s['default_deposit']" min="0" required
                             hint="Pré-remplie à la remise des clés. 0 = pas de caution." />
                </div>
                <fieldset class="field" style="border:0;padding:0;margin-inline:0;margin-bottom:0">
                    <legend class="field__label" style="margin-bottom:6px">Modes de paiement acceptés</legend>
                    <div class="row">
                        @foreach (\App\Models\Transaction::METHODS as $key => $label)
                            <label class="check"><input type="checkbox" name="methods[]" value="{{ $key }}" @checked(in_array($key, old('methods', $s['methods'])))> {{ $label }}</label>
                        @endforeach
                    </div>
                    @error('methods')<span class="field__error">{{ $message }}</span>@enderror
                </fieldset>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head"><h2 class="h3">Tickets</h2></div>
            <div class="panel__body stack">
                <x-field name="ticket_footer" type="textarea" label="Mention en bas des tickets" :value="$s['ticket_footer']" rows="2" maxlength="200"
                         placeholder="Ex. NIF 000-000-000-0. Merci de votre confiance." />
            </div>
            <div class="panel__foot row row--end"><button type="submit" class="btn btn--primary">Enregistrer les paramètres</button></div>
        </section>
    </div>
</form>
@endsection
