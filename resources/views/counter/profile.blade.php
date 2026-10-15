@extends('layouts.admin')

@section('title', 'Mon profil')

@section('content')
<div class="page-head">
    <div class="person">
        <x-avatar :user="$user" size="lg" />
        <div>
            <h1 class="h1">{{ $user->nom }}</h1>
            <p class="muted">{{ $user->role_label }}, {{ $user->email }}</p>
        </div>
    </div>
</div>

<div class="grid-2">
    <section class="panel">
        <div class="panel__head"><h2 class="h3">Informations</h2></div>
        <form method="POST" action="{{ route('counter.profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="panel__body stack">
                <div class="row">
                    <span class="avatar avatar--lg">
                        <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="" @unless ($user->avatar_url) hidden @endunless>
                        @unless ($user->avatar_url){{ $user->initials }}@endunless
                    </span>
                    <x-field name="profile_image" type="file" label="Photo" accept="image/*" data-preview="#avatar-preview" hint="JPG, PNG ou WebP, 2 Mo max." class="grow" />
                </div>
                <x-field name="nom" label="Nom complet" :value="$user->nom" required />
                <div class="form-grid">
                    <x-field name="email" type="email" label="E-mail" :value="$user->email" required />
                    <x-field name="telephone" type="tel" label="Téléphone" :value="$user->telephone" />
                </div>
                <x-field name="bio" type="textarea" label="Présentation" :value="$user->bio" rows="3" maxlength="500" hint="Visible par l’équipe uniquement." />
            </div>
            <div class="panel__foot row row--end"><button type="submit" class="btn btn--primary">Enregistrer</button></div>
        </form>
    </section>

    <section class="panel">
        <div class="panel__head"><h2 class="h3">Mot de passe</h2></div>
        <form method="POST" action="{{ route('counter.profile.password') }}">
            @csrf @method('PUT')
            <div class="panel__body stack">
                <x-field name="current_password" type="password" label="Mot de passe actuel" autocomplete="current-password" required />
                <x-field name="password" type="password" label="Nouveau mot de passe" autocomplete="new-password" hint="8 caractères minimum" required />
                <x-field name="password_confirmation" type="password" label="Confirmez le nouveau mot de passe" autocomplete="new-password" required />
            </div>
            <div class="panel__foot row row--end"><button type="submit" class="btn">Changer le mot de passe</button></div>
        </form>
    </section>
</div>
@endsection
