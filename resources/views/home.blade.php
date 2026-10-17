@extends('layouts.guest')

@section('description', 'Location à la journée et vente de véhicules à ' . $agency['city'] . '. Réservez en ligne, récupérez à l’agence.')

@php
    $cta = auth()->check() ? auth()->user()->homeRoute() : route('register');
@endphp

@section('body')
<header class="site-head site-head--overlay" id="site-head" data-home-head>
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
    @php
        $featured = $vehicles->first();
        $heroImg = $featured?->image_url ?: asset('images/istockphoto-2198462919-612x612.jpg');
        $bandImg = $vehicles->get(4)?->image_url ?: $vehicles->last()?->image_url ?: asset('images/istockphoto-1412904420-612x612.jpg');
        $town = \Illuminate\Support\Str::before($agency['city'], ',');
        $carHref = fn ($v) => auth()->check() ? route('client.vehicle.show', $v) : route('login');
    @endphp

    {{-- 1. HERO: foto machin nan parèt atravè yon plak; li grandi jiskaske l ranpli ekran an pandan scroll la --}}
    <section class="hx" id="top" data-hero>
        <div class="hx__stage" data-hero-stage>
            <div class="hx__frame" data-hero-frame aria-hidden="true">
                <img src="{{ $heroImg }}" alt="" class="hx__img" data-hero-img fetchpriority="high">
                <div class="hx__shade" data-hero-shade></div>
            </div>

            <h1 class="hx__title" data-hero-title>
                <span class="hx__line">Les clés vous</span>
                <span class="hx__line">attendent à {{ $town }}.</span>
            </h1>

            <div class="hx__window" data-hero-window>
                <img src="{{ $heroImg }}" alt="{{ $featured?->name ?? 'Véhicule de l’agence' }}">
                <span class="hx__band" aria-hidden="true">HT</span>
            </div>

            @if ($featured)
                <p class="hx__caption" data-hero-caption>
                    <x-plate :plate="$featured->plate" />
                    <span>{{ $featured->name }}, @money($featured->loan_price) par jour</span>
                </p>
            @endif

            <div class="hx__hint" data-hero-hint aria-hidden="true"><span></span>Faites défiler</div>

            <div class="hx__end" data-hero-end>
                <div class="container">
                    <p class="hx__lead">Location à la journée et vente de véhicules. Réservez en ligne en deux minutes, récupérez les clés à l’agence. Le prix affiché est le prix payé.</p>
                    <div class="hero__actions">
                        <a href="#parc" class="btn btn--primary btn--lg">Voir les véhicules</a>
                        @guest<a href="{{ route('register') }}" class="btn btn--lg hx__btn-light">Créer un compte</a>@endguest
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. PAK LA: kat yo defile an orizontal pandan w ap desann --}}
    <section class="fleet" id="parc" data-fleet>
        <div class="fleet__pin" data-fleet-pin>
            <div class="container fleet__head">
                <div>
                    <h2 class="display fleet__title" data-reveal>Le parc, en ce moment</h2>
                    <p class="lead" style="margin-top:var(--s3)">{{ $stats['models'] }} véhicules, {{ $stats['brands'] }} marques. Ceux-ci peuvent partir aujourd’hui.</p>
                </div>
                <a href="{{ $cta }}" class="btn">Voir tout le parc</a>
            </div>

            @if ($vehicles->isEmpty())
                <div class="container"><div class="panel"><x-empty icon="car" title="Le parc se remplit" text="Aucun véhicule n’est disponible pour l’instant. Repassez bientôt ou appelez l’agence." /></div></div>
            @else
                <div class="fleet__viewport" data-fleet-viewport>
                    <ul class="fleet__track" data-fleet-track>
                        @foreach ($vehicles as $v)
                            <li class="fcard">
                                <a href="{{ $carHref($v) }}" class="fcard__link">
                                    <div class="fcard__media">@if ($v->image_url)<img src="{{ $v->image_url }}" alt="{{ $v->name }}" loading="lazy" data-fleet-img>@endif</div>
                                    <div class="fcard__body">
                                        <div class="fcard__top">
                                            <span class="muted small">{{ $v->brand }}, {{ $v->year }}</span>
                                            <x-plate :plate="$v->plate" />
                                        </div>
                                        <h3 class="fcard__name">{{ $v->model }}</h3>
                                        <div class="fcard__prices">
                                            <span><small>Location</small><strong class="num">@money($v->loan_price)</strong><small>/ jour</small></span>
                                            <span><small>Achat</small><strong class="num">@money($v->price)</strong></span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                        <li class="fcard fcard--more">
                            <a href="{{ $cta }}" class="fcard__link">
                                <span class="fcard__more-n num">{{ $stats['models'] }}</span>
                                <span class="fcard__more-t">véhicules au total. Comparez-les tous dans votre espace.</span>
                                <span class="btn btn--primary">Voir tout le parc</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="container"><div class="fleet__progress" aria-hidden="true"><i data-fleet-bar></i></div></div>
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
                <h2 class="h1" data-reveal>De l’écran aux clés, en quatre étapes</h2>
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
                            <p>L’équipe valide votre demande et vous recevez un code de retrait dans votre espace.</p>
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
                            <h3 class="h2">Récupérez les clés à l’agence</h3>
                            <p>Présentez votre code de retrait au comptoir, réglez sur place et repartez avec les clés et votre bon de sortie.</p>
                            <div class="jstep__demo jstep__demo--ticket">
                                <span class="pickup-code"><small>Code de retrait</small>K7P-29Q</span>
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

    {{-- 4. CHIF YO: foto parallax dèyè, chif ki konte --}}
    <section class="band" data-band>
        <div class="band__bg" aria-hidden="true"><img src="{{ $bandImg }}" alt="" loading="lazy" data-band-img></div>
        <div class="container band__in">
            <dl class="band__stats">
                <div><dt>véhicules au parc</dt><dd class="num" data-count="{{ $stats['models'] }}">{{ $stats['models'] }}</dd></div>
                <div><dt>marques</dt><dd class="num" data-count="{{ $stats['brands'] }}">{{ $stats['brands'] }}</dd></div>
                @if ($stats['fromDay'])
                    <div><dt>par jour, pour commencer</dt><dd class="num" data-count="{{ $stats['fromDay'] }}" data-suffix="&nbsp;$">{{ $stats['fromDay'] }}&nbsp;$</dd></div>
                @endif
                <div><dt>pour réserver en ligne</dt><dd class="num">24 h/24</dd></div>
            </dl>
        </div>
    </section>

    <section class="section section--line" id="agence">
        <div class="container agency">
            <div>
                <h2 class="h1" data-reveal>L’agence</h2>
                <p class="lead" style="margin-top:var(--s3)">Une équipe sur place pour la remise des clés, les retours et les paiements.</p>
                <dl class="agency__list">
                    <div><x-icon name="pin" /><dt>Adresse</dt><dd>{{ $agency['address'] }}</dd></div>
                    <div><x-icon name="phone" /><dt>Téléphone</dt><dd><a href="{{ \App\Support\Agency::telHref() }}">{{ $agency['phone'] }}</a></dd></div>
                    <div><x-icon name="mail" /><dt>E-mail</dt><dd><a href="mailto:{{ $agency['email'] }}">{{ $agency['email'] }}</a></dd></div>
                    <div><x-icon name="clock" /><dt>Horaires</dt><dd>{{ $agency['hours'] }}@if ($agency['hours_note'])<br><span class="muted" style="font-weight:400">{{ $agency['hours_note'] }}</span>@endif</dd></div>
                </dl>
            </div>
            <div class="agency__map">
                <iframe title="Carte de l’agence, {{ $agency['city'] }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        src="https://maps.google.com/maps?q={{ urlencode($agency['map_query'] ?: $agency['address']) }}&t=&z=13&ie=UTF-8&iwloc=&output=embed"></iframe>
            </div>
        </div>
    </section>

    {{-- 5. DÈNYE APÈL --}}
    <section class="outro">
        <div class="container outro__in">
            <h2 class="display outro__title" data-reveal>Prêt à prendre la route ?</h2>
            <div class="hero__actions" style="justify-content:center">
                <a href="{{ $cta }}" class="btn btn--primary btn--lg">{{ auth()->check() ? 'Choisir mon véhicule' : 'Créer mon compte' }}</a>
                <a href="{{ \App\Support\Agency::telHref() }}" class="btn btn--lg hx__btn-light">Appeler l’agence</a>
            </div>
        </div>
    </section>
</main>

<footer class="site-foot">
    <div class="container site-foot__in">
        <span>© {{ date('Y') }} {{ $agency['name'] }}, {{ $agency['city'] }}</span>
        <span><a href="{{ route('login') }}">Espace client</a></span>
    </div>
</footer>
@push('scripts')
    <script src="{{ asset('vendor/gsap/gsap.min.js') }}" defer></script>
    <script src="{{ asset('vendor/gsap/ScrollTrigger.min.js') }}" defer></script>
    <script src="{{ asset('vendor/gsap/SplitText.min.js') }}" defer></script>
    <script src="{{ asset('vendor/lenis/lenis.js') }}" defer></script>
    <script src="{{ asset('js/home.js') }}?v={{ filemtime(public_path('js/home.js')) }}" defer></script>
@endpush
@endsection
