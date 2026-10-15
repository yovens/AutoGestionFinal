@extends('layouts.guest')

@section('description', 'Location à la journée et vente de véhicules à Simon, Les Cayes. Réservez en ligne, récupérez à l’agence.')

@php
    $cta = auth()->check() ? auth()->user()->homeRoute() : route('register');
@endphp

@section('body')
<header class="site-head" id="site-head">
    <div class="container site-head__in">
        <a href="{{ route('home') }}" class="logo">@include('layouts.partials.logo')</a>
        <nav class="site-nav site-nav--main" aria-label="Navigation">
            <a href="#parc">Véhicules</a>
            <a href="#fonctionnement">Comment ça marche</a>
            <a href="#agence">L’agence</a>
        </nav>
        <div class="site-head__end">
            @auth
                <a href="{{ auth()->user()->homeRoute() }}" class="btn btn--primary">Mon espace</a>
            @else
                <a href="{{ route('login') }}" class="btn btn--ghost">Se connecter</a>
                <a href="{{ route('register') }}" class="btn btn--primary hide-sm">Créer un compte</a>
            @endauth
            <button type="button" class="btn btn--ghost btn--icon nav-toggle" data-nav-toggle="#site-head" aria-expanded="false" aria-label="Menu">
                <x-icon name="menu" />
            </button>
        </div>
    </div>
</header>

<main id="main">
    <section class="hero">
        <div class="container hero__grid">
            <div>
                <h1 class="display">Location et vente de véhicules aux Cayes.</h1>
                <p class="lead" style="margin-top: var(--s5)">
                    Choisissez un véhicule en ligne, réservez-le en deux minutes et récupérez les clés à notre agence de Simon.
                    Le prix affiché est le prix payé.
                </p>
                <div class="hero__actions">
                    <a href="#parc" class="btn btn--primary btn--lg">Voir les véhicules disponibles</a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn--lg">Créer un compte</a>
                    @endguest
                </div>
                <dl class="hero__facts">
                    <div><dt>Véhicules au parc</dt><dd class="num">{{ $stats['models'] }}</dd></div>
                    <div><dt>Marques</dt><dd class="num">{{ $stats['brands'] }}</dd></div>
                    @if ($stats['fromDay'])
                        <div><dt>Location dès</dt><dd class="num">@money($stats['fromDay']) <span class="small muted">/ jour</span></dd></div>
                    @endif
                </dl>
            </div>

            @php $featured = $vehicles->first(); @endphp
            <div class="hero__media">
                <div class="hero__photo">
                    @if ($featured?->image_url)
                        <img src="{{ $featured->image_url }}" alt="{{ $featured->name }}">
                    @else
                        <img src="{{ asset('images/istockphoto-2198462919-612x612.jpg') }}" alt="Véhicule tout-terrain sur une route de montagne">
                    @endif
                </div>
                @if ($featured)
                    <div class="hero__tag">
                        <x-plate :plate="$featured->plate" />
                        <span class="strong">{{ $featured->name }}</span>
                        <span class="small muted num">@money($featured->loan_price) par jour, disponible aujourd’hui</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="section section--line" id="parc">
        <div class="container">
            <div class="section__head">
                <div>
                    <h2 class="h1">Disponibles en ce moment</h2>
                    <p class="muted" style="margin-top:6px">Ces véhicules peuvent partir dès aujourd’hui.</p>
                </div>
                <a href="{{ $cta }}" class="btn">Voir tout le parc</a>
            </div>

            @if ($vehicles->isEmpty())
                <div class="panel"><x-empty icon="car" title="Le parc se remplit" text="Aucun véhicule n’est disponible pour l’instant. Repassez bientôt ou appelez l’agence." /></div>
            @else
                <div class="car-grid">
                    @foreach ($vehicles as $vehicle)
                        <x-vehicle-card :vehicle="$vehicle" :href="auth()->check() ? route('client.vehicle.show', $vehicle) : route('login')" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="section section--line" id="fonctionnement">
        <div class="container">
            <div class="section__head">
                <h2 class="h1">Comment ça marche</h2>
            </div>
            <ol class="steps" style="list-style:none;margin:0;padding:0">
                <li class="step">
                    <h3 class="h3">Choisissez votre véhicule</h3>
                    <p>Comparez les prix de location à la journée et les prix de vente, photos et plaques à l’appui.</p>
                </li>
                <li class="step">
                    <h3 class="h3">Réservez en ligne</h3>
                    <p>Indiquez la durée de location ou réservez l’achat. Le véhicule est bloqué à votre nom.</p>
                </li>
                <li class="step">
                    <h3 class="h3">Passez à l’agence</h3>
                    <p>Une fois la demande confirmée, réglez sur place et repartez avec les clés.</p>
                </li>
            </ol>
        </div>
    </section>

    <section class="section section--line" id="agence">
        <div class="container agency">
            <div>
                <h2 class="h1">L’agence</h2>
                <p class="lead" style="margin-top:var(--s3)">Une équipe sur place pour la remise des clés, les retours et les paiements.</p>
                <dl class="agency__list">
                    <div><x-icon name="pin" /><dt>Adresse</dt><dd>Simon, arrondissement des Cayes, Haïti</dd></div>
                    <div><x-icon name="phone" /><dt>Téléphone</dt><dd><a href="tel:+50940160335">+509 40 16 03 35</a></dd></div>
                    <div><x-icon name="mail" /><dt>E-mail</dt><dd><a href="mailto:contact@autogestion.ht">contact@autogestion.ht</a></dd></div>
                    <div><x-icon name="clock" /><dt>Horaires</dt><dd>Lundi au vendredi, 8 h à 17 h<br><span class="muted" style="font-weight:400">Samedi sur rendez-vous</span></dd></div>
                </dl>
            </div>
            <div class="agency__map">
                <iframe title="Carte de l’agence à Simon, Les Cayes" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        src="https://maps.google.com/maps?q=Simon,%20Cayes,%20Haiti&t=&z=13&ie=UTF-8&iwloc=&output=embed"></iframe>
            </div>
        </div>
    </section>
</main>

<footer class="site-foot">
    <div class="container site-foot__in">
        <span>© {{ date('Y') }} AutoGestion, Simon, Les Cayes</span>
        <span><a href="{{ route('login') }}">Espace client</a></span>
    </div>
</footer>
@endsection
