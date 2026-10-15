<!doctype html>
<html lang="fr">
<head>
    @include('layouts.partials.head')
</head>
@php
    $me = auth()->user();
    // Meni an chanje selon wòl la: admin wè tout bagay, sekretè a wè sèlman comptoir la.
    $groups = $me->isAdmin()
        ? [
            [
                ['route' => 'admin.dashboard',          'match' => 'admin.dashboard',      'icon' => 'dashboard', 'label' => 'Tableau de bord'],
                ['route' => 'counter.index',            'match' => ['counter.index', 'counter.search'], 'icon' => 'key', 'label' => 'Comptoir', 'count' => $nav['counter'] ?? 0],
                ['route' => 'counter.loans.index',      'match' => 'counter.loans.*',      'icon' => 'calendar',  'label' => 'Locations',  'count' => $nav['loans'] ?? 0],
                ['route' => 'admin.transactions.index', 'match' => 'admin.transactions.*', 'icon' => 'receipt',   'label' => 'Paiements',  'count' => $nav['payments'] ?? 0],
                ['route' => 'admin.vehicles.index',     'match' => 'admin.vehicles.*',     'icon' => 'car',       'label' => 'Parc automobile'],
                ['route' => 'admin.users.index',        'match' => 'admin.users.*',        'icon' => 'users',     'label' => 'Comptes'],
                ['route' => 'admin.contacts.index',     'match' => 'admin.contacts.*',     'icon' => 'mail',      'label' => 'Messages',   'count' => $nav['messages'] ?? 0],
            ],
            [
                ['route' => 'counter.tickets.index',    'match' => 'counter.tickets.*',    'icon' => 'printer',   'label' => 'Tickets'],
                ['route' => 'counter.cash',             'match' => 'counter.cash',         'icon' => 'tag',       'label' => 'Caisse du jour'],
            ],
        ]
        : [
            [
                ['route' => 'counter.index',            'match' => ['counter.index', 'counter.search'], 'icon' => 'key', 'label' => 'Comptoir', 'count' => $nav['counter'] ?? 0],
                ['route' => 'counter.loans.create',     'match' => 'counter.loans.create', 'icon' => 'plus',      'label' => 'Nouvelle location'],
                ['route' => 'counter.loans.index',      'match' => ['counter.loans.index', 'counter.loans.show', 'counter.loans.checkout', 'counter.loans.checkin'], 'icon' => 'calendar', 'label' => 'Locations', 'count' => $nav['loans'] ?? 0],
                ['route' => 'counter.clients.index',    'match' => 'counter.clients.*',    'icon' => 'users',     'label' => 'Clients'],
            ],
            [
                ['route' => 'counter.tickets.index',    'match' => 'counter.tickets.*',    'icon' => 'printer',   'label' => 'Tickets'],
                ['route' => 'counter.cash',             'match' => 'counter.cash',         'icon' => 'tag',       'label' => 'Ma caisse du jour'],
            ],
        ];
@endphp
<body>
<a class="skip-link" href="#main">Aller au contenu</a>
<div class="admin" id="admin">
    <aside class="rail" aria-label="Navigation principale">
        <a href="{{ $me->homeRoute() }}" class="logo rail__brand">@include('layouts.partials.logo')</a>

        <nav class="rail__nav">
            @foreach ($groups as $links)
                <div class="rail__group">
                    @foreach ($links as $l)
                        <a href="{{ route($l['route']) }}" class="rail__link" @if (request()->routeIs(...(array) $l['match'])) aria-current="page" @endif>
                            <x-icon :name="$l['icon']" />
                            <span>{{ $l['label'] }}</span>
                            @if (!empty($l['count']))<span class="count" title="À traiter">{{ $l['count'] }}</span>@endif
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>

        <div class="rail__foot">
            <a href="{{ route('counter.profile') }}" class="rail__me" @if (request()->routeIs('counter.profile')) aria-current="page" @endif>
                <x-avatar :user="$me" />
                <span class="person__text">
                    <span class="person__name" style="display:block">{{ $me->nom }}</span>
                    <span class="rail__role">{{ $me->role_label }}</span>
                </span>
            </a>
            <x-theme-switch />
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rail__link rail__logout"><x-icon name="logout" /><span>Se déconnecter</span></button>
            </form>
        </div>
    </aside>
    <div class="rail-scrim" data-nav-close></div>

    <div class="admin__main">
        <header class="topbar">
            <button type="button" class="btn btn--ghost btn--icon topbar__menu" data-nav-toggle="#admin" data-nav-class="is-nav-open" aria-expanded="false" aria-label="Ouvrir le menu">
                <x-icon name="menu" />
            </button>
            <nav class="crumbs" aria-label="Fil d’Ariane">
                @hasSection('crumbs')
                    @yield('crumbs')
                @else
                    <span aria-current="page">@yield('title')</span>
                @endif
            </nav>
            <div class="topbar__end">@yield('topbar')</div>
        </header>

        <main class="content" id="main">
            <x-flash />
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
