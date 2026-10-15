@extends('layouts.admin')

@section('title', 'Clients')

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Clients</h1>
        <p class="muted">Retrouvez un client, son historique et ses locations en cours.</p>
    </div>
    <div class="page-head__actions">
        <button type="button" class="btn btn--primary" data-dialog-open="new"><x-icon name="plus" size="sm" /> Enregistrer un client</button>
    </div>
</div>

<form method="GET" class="toolbar" role="search">
    <div class="input-icon toolbar__search">
        <x-icon name="search" size="sm" />
        <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Nom, téléphone ou e-mail" aria-label="Rechercher un client">
    </div>
    @if ($search)<a href="{{ route('counter.clients.index') }}" class="btn btn--ghost">Effacer</a>@endif
</form>

<section class="panel panel--flush">
    @if ($clients->isEmpty())
        <x-empty icon="users" title="Aucun client trouvé" text="Vérifiez l’orthographe ou enregistrez le client.">
            <button type="button" class="btn" data-dialog-open="new">Enregistrer un client</button>
        </x-empty>
    @else
        <div class="table-wrap"><table class="table">
            <thead><tr><th>Nom</th><th>Téléphone</th><th class="right">Locations</th><th>En cours</th><th class="right"><span class="sr-only">Actions</span></th></tr></thead>
            <tbody>
                @foreach ($clients as $c)
                    <tr>
                        <td><div class="person"><x-avatar :user="$c" /><div class="person__text"><a href="{{ route('counter.clients.show', $c) }}" class="table__link">{{ $c->nom }}</a><span class="table__sub">{{ str_ends_with($c->email, '.local') ? 'Sans e-mail' : $c->email }}</span></div></div></td>
                        <td class="nowrap">{{ $c->telephone ?: '—' }}</td>
                        <td class="right num">{{ $c->loans_count }}</td>
                        <td>@if ($c->active_count)<x-badge tone="ok">{{ $c->active_count }}</x-badge>@else<span class="muted">—</span>@endif @if ($c->isBlocked()) <x-badge tone="alert">Suspendu</x-badge>@endif</td>
                        <td><div class="actions"><a href="{{ route('counter.loans.create', ['client' => $c->id]) }}" class="btn btn--sm">Nouvelle location</a></div></td>
                    </tr>
                @endforeach
            </tbody>
        </table></div>
    @endif
</section>

<x-dialog id="new" title="Enregistrer un client" size="sm" :open-on-load="$errors->any()">
    <form method="POST" action="{{ route('counter.clients.store') }}">
        @csrf
        <div class="modal__body stack">
            <x-field name="nom" label="Nom complet" required />
            <x-field name="telephone" type="tel" label="Téléphone" placeholder="+509" required />
            <x-field name="email" type="email" label="E-mail" hint="Facultatif. Avec un e-mail, le client pourra se connecter à son espace." />
        </div>
        <div class="modal__foot">
            <button type="button" class="btn" data-dialog-close>Annuler</button>
            <button type="submit" class="btn btn--primary">Enregistrer</button>
        </div>
    </form>
</x-dialog>
@endsection
