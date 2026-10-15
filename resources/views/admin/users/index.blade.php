@extends('layouts.admin')

@section('title', 'Comptes')

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Comptes</h1>
        <p class="muted">Clients et membres de l’équipe. Un compte suspendu ne peut plus se connecter.</p>
    </div>
    <div class="page-head__actions">
        <button type="button" class="btn btn--primary" data-dialog-open="new"><x-icon name="plus" size="sm" /> Créer un compte</button>
    </div>
</div>

<form method="GET" class="toolbar" role="search">
    <nav class="tabs" aria-label="Filtrer par rôle">
        <a href="{{ route('admin.users.index', array_filter(['q' => $search])) }}" @if (!$role) aria-current="page" @endif>Tous</a>
        <a href="{{ route('admin.users.index', array_filter(['role' => 'CLIENT', 'q' => $search])) }}" @if ($role === 'CLIENT') aria-current="page" @endif>Clients</a>
        <a href="{{ route('admin.users.index', array_filter(['role' => 'ADMIN', 'q' => $search])) }}" @if ($role === 'ADMIN') aria-current="page" @endif>Administrateurs</a>
    </nav>
    @if ($role)<input type="hidden" name="role" value="{{ $role }}">@endif
    <div class="input-icon toolbar__search">
        <x-icon name="search" size="sm" />
        <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Nom, e-mail ou téléphone" aria-label="Rechercher un compte">
    </div>
</form>

<section class="panel panel--flush">
    @if ($users->isEmpty())
        <x-empty icon="users" title="Aucun compte trouvé" text="Vérifiez l’orthographe ou retirez le filtre." />
    @else
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Nom</th><th>Téléphone</th><th>Rôle</th><th class="right">Locations</th><th>Statut</th><th class="right"><span class="sr-only">Actions</span></th></tr></thead>
                <tbody>
                    @foreach ($users as $u)
                        <tr>
                            <td>
                                <div class="person">
                                    <x-avatar :user="$u" />
                                    <div class="person__text">
                                        <a href="{{ route('admin.users.show', $u) }}" class="table__link">{{ $u->nom }}</a>
                                        <span class="table__sub">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="nowrap">{{ $u->telephone ?: '—' }}</td>
                            <td><x-badge :tone="$u->isAdmin() ? 'brand' : 'off'" class="badge--plain">{{ $u->role_label }}</x-badge></td>
                            <td class="right num">{{ $u->loans_count }}</td>
                            <td>
                                @if ($u->isBlocked())<x-badge tone="alert">Suspendu</x-badge>@else<x-badge tone="ok">Actif</x-badge>@endif
                            </td>
                            <td>
                                <div class="actions">
                                    @unless ($u->is(auth()->user()))
                                        <form method="POST" action="{{ route('admin.users.block', $u) }}" class="inline-form"
                                              data-confirm="{{ $u->isBlocked() ? 'Réactiver' : 'Suspendre' }} le compte de {{ $u->nom }} ?">
                                            @csrf
                                            <button type="submit" class="btn btn--sm btn--ghost">{{ $u->isBlocked() ? 'Réactiver' : 'Suspendre' }}</button>
                                        </form>
                                    @endunless
                                    <a href="{{ route('admin.users.show', $u) }}" class="btn btn--sm btn--ghost btn--icon" aria-label="Voir le compte"><x-icon name="chevron-right" size="sm" /></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>

<x-dialog id="new" title="Créer un compte" :open-on-load="$errors->any()">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="modal__body stack">
            <x-field name="nom" label="Nom complet" required />
            <div class="form-grid">
                <x-field name="email" type="email" label="E-mail" required />
                <x-field name="telephone" type="tel" label="Téléphone" placeholder="+509" />
            </div>
            <div class="form-grid">
                <x-field name="password" type="password" label="Mot de passe provisoire" autocomplete="new-password" hint="8 caractères minimum" required />
                <x-field name="role" type="select" label="Rôle" :value="'CLIENT'" :options="['CLIENT' => 'Client', 'ADMIN' => 'Administrateur']" />
            </div>
        </div>
        <div class="modal__foot">
            <button type="button" class="btn" data-dialog-close>Annuler</button>
            <button type="submit" class="btn btn--primary">Créer le compte</button>
        </div>
    </form>
</x-dialog>
@endsection
