<!doctype html>
<html lang="fr">
<head>
    @include('layouts.partials.head')
</head>
<body>
<a class="skip-link" href="#main">Aller au contenu</a>
@yield('body')
@stack('scripts')
</body>
</html>
