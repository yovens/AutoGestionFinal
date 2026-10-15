<!doctype html>
<html lang="fr">
<head>
    @include('layouts.partials.head')
</head>
@php
    $me = auth()->user();
    $links = [
        ['route' => 'client.dashboard',    'match' => 'client.dashboard',              'label' => 'Accueil'],
        ['route' => 'client.vehicles',     'match' => ['client.vehicles', 'client.vehicle.show'], 'label' => 'Véhicules'],
        ['route' => 'client.loan',         'match' => 'client.loan',                   'label' => 'Mes locations'],
        ['route' => 'client.transactions', 'match' => 'client.transactions',           'label' => 'Paiements'],
        ['route' => 'client.contact',      'match' => 'client.contact',                'label' => 'Contact'],
    ];
@endphp
<body class="layout-client">
<a class="skip-link" href="#main">Aller au contenu</a>
<header class="site-head" id="site-head">
    <div class="container site-head__in">
        <a href="{{ route('client.dashboard') }}" class="logo">@include('layouts.partials.logo')</a>

        <nav class="site-nav site-nav--main" aria-label="Navigation principale">
            @foreach ($links as $l)
                <a href="{{ route($l['route']) }}" @if (request()->routeIs(...(array) $l['match'])) aria-current="page" @endif>{{ $l['label'] }}</a>
            @endforeach
        </nav>

        <div class="site-head__end">
            <a href="{{ route('client.cart') }}" class="btn btn--ghost btn--icon cart-link" aria-label="Ma sélection ({{ $nav['cart'] }})" title="Ma sélection">
                <x-icon name="bag" />
                @if ($nav['cart'] > 0)<span class="count">{{ $nav['cart'] }}</span>@endif
            </a>

            <details class="menu">
                <summary class="me-btn" aria-label="Mon compte">
                    <x-avatar :user="$me" style="--size:30px" />
                    <span class="me-btn__name small strong">{{ $me->first_name }}</span>
                    <x-icon name="chevron-down" size="sm" class="muted" />
                </summary>
                <div class="menu__list">
                    <div class="menu__head">
                        <div class="strong">{{ $me->nom }}</div>
                        <div class="tiny muted">{{ $me->email }}</div>
                    </div>
                    <a class="menu__item" href="{{ route('client.profile') }}"><x-icon name="user" size="sm" />Mon profil</a>
                    <a class="menu__item" href="{{ route('client.cart') }}"><x-icon name="bag" size="sm" />Ma sélection</a>
                    <a class="menu__item" href="{{ route('client.about') }}"><x-icon name="info" size="sm" />L’agence</a>
                    <div class="menu__sep"></div>
                    <x-theme-switch />
                    <div class="menu__sep"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="menu__item menu__item--danger"><x-icon name="logout" size="sm" />Se déconnecter</button>
                    </form>
                </div>
            </details>

            <button type="button" class="btn btn--ghost btn--icon nav-toggle" data-nav-toggle="#site-head" aria-expanded="false" aria-label="Menu">
                <x-icon name="menu" />
            </button>
        </div>
    </div>
</header>

<main class="page" id="main">
    <div class="container">
        <x-flash />
        @yield('content')
    </div>
</main>

<footer class="site-foot">
    <div class="container site-foot__in">
        <span>{{ $agency['name'] }}, {{ $agency['city'] }}</span>
        <span><a href="{{ \App\Support\Agency::telHref() }}">{{ $agency['phone'] }}</a>, {{ $agency['hours'] }}</span>
    </div>
</footer>
@stack('scripts')
</body>
</html>
