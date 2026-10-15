@extends('layouts.guest')

@php $isLogin = ($mode ?? 'login') === 'login'; @endphp

@section('title', $isLogin ? 'Connexion' : 'Créer un compte')

@section('body')
<div class="auth">
    <aside class="auth__visual">
        <img src="{{ asset('images/istockphoto-1412904420-612x612.jpg') }}" alt="">
        <a href="{{ route('home') }}" class="logo">@include('layouts.partials.logo')</a>
        <div>
            <p class="auth__quote">Les clés vous attendent à Simon.</p>
            <p class="auth__note">Suivez vos locations, prolongez-les en un clic et retrouvez tous vos reçus au même endroit.</p>
        </div>
    </aside>

    <main class="auth__panel" id="main">
        <div class="auth__form">
            <nav class="auth__switch" aria-label="Choix du formulaire">
                <a href="{{ route('login') }}" @if ($isLogin) aria-current="page" @endif>Se connecter</a>
                <a href="{{ route('register') }}" @unless ($isLogin) aria-current="page" @endunless>Créer un compte</a>
            </nav>

            <h1 class="h1" style="margin-bottom:6px">{{ $isLogin ? 'Bon retour' : 'Ouvrir un compte client' }}</h1>
            <p class="muted" style="margin-bottom:var(--s5)">
                {{ $isLogin ? 'Connectez-vous pour réserver ou suivre vos locations.' : 'Gratuit, et nécessaire pour réserver un véhicule.' }}
            </p>

            @include('components.flash', ['hideErrors' => true])

            @if ($isLogin)
                <form method="POST" action="{{ route('login.submit') }}" class="stack" novalidate>
                    @csrf
                    <x-field name="email" type="email" label="E-mail" autocomplete="email" required autofocus />
                    <x-field name="password" type="password" label="Mot de passe" autocomplete="current-password" required />
                    <label class="check"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Rester connecté sur cet appareil</label>
                    <button type="submit" class="btn btn--primary btn--lg btn--block">Se connecter</button>
                </form>
            @else
                <form method="POST" action="{{ route('register.submit') }}" class="stack" novalidate>
                    @csrf
                    <x-field name="nom" label="Nom complet" autocomplete="name" required autofocus />
                    <div class="form-grid">
                        <x-field name="email" type="email" label="E-mail" autocomplete="email" required />
                        <x-field name="telephone" type="tel" label="Téléphone" autocomplete="tel" placeholder="+509" hint="Facultatif" />
                    </div>
                    <x-field name="password" type="password" label="Mot de passe" autocomplete="new-password" hint="8 caractères minimum" required />
                    <x-field name="password_confirmation" type="password" label="Confirmez le mot de passe" autocomplete="new-password" required />
                    <button type="submit" class="btn btn--primary btn--lg btn--block">Créer mon compte</button>
                </form>
            @endif

            <div class="or">ou</div>
            <a href="{{ route('google.redirect') }}" class="btn btn--lg btn--block"><x-icon name="google" /> Continuer avec Google</a>

            <p class="small muted" style="margin-top:var(--s5);text-align:center">
                <a href="{{ route('home') }}">Retour à l’accueil</a>
            </p>
        </div>
    </main>
</div>
@endsection
