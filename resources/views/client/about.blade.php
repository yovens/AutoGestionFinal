@extends('layouts.client')

@section('title', 'L’agence')

@section('content')
<div class="container--narrow" style="margin:0;padding:0">
    <h1 class="display" style="font-size:clamp(2rem,4vw,3rem)">Une agence locale pour louer et acheter en confiance.</h1>
    <p class="lead" style="margin-top:var(--s5)">
        AutoGestion vend et loue des véhicules de toutes catégories depuis Simon, aux Cayes.
        Notre objectif : rendre la voiture accessible, avec des prix affichés clairement et un vrai service sur place.
    </p>
</div>

<div class="grid-2" style="margin-top:var(--s7)">
    <section class="panel"><div class="panel__body">
        <h2 class="h2">Ce que nous faisons</h2>
        <p class="muted" style="margin-top:var(--s3)">
            Faciliter la mobilité et l’accès à la propriété d’un véhicule pour les familles et les entreprises haïtiennes,
            avec un large choix et une transparence totale des prix.
        </p>
    </div></section>
    <section class="panel"><div class="panel__body">
        <h2 class="h2">Comment nous travaillons</h2>
        <p class="muted" style="margin-top:var(--s3)">
            Les réservations se font en ligne, les clés et les paiements à l’agence. Chaque location et chaque achat
            a une référence que vous retrouvez dans votre espace.
        </p>
    </div></section>
    <section class="panel"><div class="panel__body">
        <h2 class="h2">Notre histoire</h2>
        <p class="muted" style="margin-top:var(--s3)">
            Fondée en 2025 par des passionnés d’automobile et de technologie, AutoGestion est née de l’envie de
            moderniser le marché local du véhicule, d’une petite flotte vers un parc qui couvre tous les besoins.
        </p>
    </div></section>
    <section class="panel"><div class="panel__body">
        <h2 class="h2">Nous trouver</h2>
        <dl class="agency__list" style="margin-top:var(--s3)">
            <div><x-icon name="pin" /><dt>Adresse</dt><dd>#123, rue des Véhicules, Simon, Les Cayes</dd></div>
            <div><x-icon name="phone" /><dt>Téléphone</dt><dd><a href="tel:+50940160335">+509 40 16 03 35</a></dd></div>
            <div><x-icon name="clock" /><dt>Horaires</dt><dd>Lundi au vendredi, 8 h à 17 h</dd></div>
        </dl>
    </div></section>
</div>
@endsection
