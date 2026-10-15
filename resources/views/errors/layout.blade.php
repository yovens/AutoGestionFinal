@extends('layouts.guest')

@section('body')
<main id="main" class="container" style="min-height:100vh;display:grid;place-items:center">
    <div class="empty" style="max-width:520px">
        <a href="{{ url('/') }}" class="logo" style="margin-bottom:var(--s6)">@include('layouts.partials.logo')</a>
        <x-plate :plate="'ERR-' . $code" size="lg" />
        <h1 class="h1" style="margin-top:var(--s5)">@yield('heading')</h1>
        <p class="empty__text">@yield('text')</p>
        <a href="{{ auth()->check() ? auth()->user()->homeRoute() : url('/') }}" class="btn btn--primary">Retour à l’accueil</a>
    </div>
</main>
@endsection
