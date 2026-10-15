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

    @php
        $demo = $vehicles->first();
        $demoDays = 3;
    @endphp
    {{-- Parcours: yon ti machin desann wout la pandan moun nan ap defile, li pase sou chak etap epi li pake devan ajans lan. --}}
    <section class="section section--line journey" id="fonctionnement" data-journey>
        <div class="container">
            <div class="journey__head">
                <h2 class="h1">De l’écran aux clés, en quatre étapes</h2>
                <p class="lead" style="margin-top:var(--s3)">Suivez la route : c’est exactement le chemin de votre location.</p>
            </div>

            <div class="journey__track" data-journey-track>
                <svg class="journey__road" aria-hidden="true" focusable="false">
                    <path class="journey__asphalt" />
                    <path class="journey__lane" />
                    <path class="journey__done" />
                </svg>

                <div class="journey__car" aria-hidden="true" data-journey-car>
                    <svg viewBox="0 0 44 76" width="44" height="76">
                        <rect x="1" y="12" width="5" height="14" rx="2" class="car-wheel" />
                        <rect x="38" y="12" width="5" height="14" rx="2" class="car-wheel" />
                        <rect x="1" y="50" width="5" height="14" rx="2" class="car-wheel" />
                        <rect x="38" y="50" width="5" height="14" rx="2" class="car-wheel" />
                        <rect x="4" y="2" width="36" height="72" rx="12" class="car-body" />
                        <rect x="9" y="10" width="26" height="9" rx="3" class="car-glass" />
                        <rect x="9" y="22" width="26" height="26" rx="5" class="car-roof" />
                        <path d="M9 52h26l-3 9H12z" class="car-glass" />
                        <rect x="8" y="3" width="7" height="3" rx="1.5" class="car-tail" />
                        <rect x="29" y="3" width="7" height="3" rx="1.5" class="car-tail" />
                        <rect x="8" y="69" width="8" height="3.5" rx="1.5" class="car-light" />
                        <rect x="28" y="69" width="8" height="3.5" rx="1.5" class="car-light" />
                    </svg>
                </div>

                <ol class="journey__steps">
                    <li class="jstep" data-journey-step>
                        <span class="jstep__node" data-journey-node>1</span>
                        <div class="jstep__card">
                            <h3 class="h2">Choisissez votre véhicule</h3>
                            <p>Location à la journée ou achat : les prix sont affichés, photos et plaques à l’appui.</p>
                            @if ($demo)
                                <div class="jstep__demo jstep__demo--car">
                                    @if ($demo->image_url)<img src="{{ $demo->image_url }}" alt="" loading="lazy">@endif
                                    <div>
                                        <x-plate :plate="$demo->plate" />
                                        <div class="strong" style="margin-top:6px">{{ $demo->name }}</div>
                                        <div class="small muted num">@money($demo->loan_price) / jour, @money($demo->price) à l’achat</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </li>

                    <li class="jstep" data-journey-step>
                        <span class="jstep__node" data-journey-node>2</span>
                        <div class="jstep__card">
                            <h3 class="h2">Réservez en ligne</h3>
                            <p>Indiquez le nombre de jours ou réservez l’achat. Le véhicule est aussitôt bloqué à votre nom.</p>
                            @if ($demo)
                                <div class="jstep__demo">
                                    <div class="row row--between">
                                        <span class="muted small">Durée</span>
                                        <span class="jstep__days"><span>−</span><strong class="num">{{ $demoDays }}</strong><span>+</span></span>
                                    </div>
                                    <div class="total-line" style="margin-top:var(--s3);padding-bottom:0">
                                        <span class="muted">Total pour {{ $demoDays }} jours</span>
                                        <span class="total-line__value num">@money($demo->loan_price * $demoDays)</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </li>

                    <li class="jstep" data-journey-step>
                        <span class="jstep__node" data-journey-node>3</span>
                        <div class="jstep__card">
                            <h3 class="h2">L’agence confirme</h3>
                            <p>L’équipe valide votre demande. Dates, montant et retour prévu apparaissent dans votre espace.</p>
                            <div class="jstep__demo">
                                <div class="row row--between">
                                    <span class="strong">{{ $demo?->name ?? 'Votre véhicule' }}</span>
                                    <x-badge tone="ok">En cours</x-badge>
                                </div>
                                <div class="timeline">
                                    <div class="timeline__bar"><div class="timeline__fill" style="width:35%"></div></div>
                                    <div class="timeline__ends"><span>Départ aujourd’hui</span><span>Retour dans {{ $demoDays }} jours</span></div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="jstep jstep--last" data-journey-step>
                        <span class="jstep__node jstep__node--park" data-journey-node>P</span>
                        <div class="jstep__card">
                            <h3 class="h2">Récupérez les clés à Simon</h3>
                            <p>Réglez sur place, signez, et repartez. Au retour, l’agence réceptionne le véhicule en quelques minutes.</p>
                            <div class="jstep__demo jstep__demo--ticket">
                                <div>
                                    <div class="small muted">Location</div>
                                    <div class="ticket__ref num" style="font-size:1.2rem">LOC-00042</div>
                                </div>
                                <x-icon name="key" size="lg" />
                            </div>
                        </div>
                    </li>
                </ol>
            </div>

            <div class="journey__cta">
                <a href="{{ $cta }}" class="btn btn--primary btn--lg">{{ auth()->check() ? 'Choisir mon véhicule' : 'Créer mon compte et réserver' }}</a>
            </div>
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
