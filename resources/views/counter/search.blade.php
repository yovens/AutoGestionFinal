@extends('layouts.admin')

@section('title', 'Recherche')

@section('crumbs')
    <a href="{{ route('counter.index') }}">Comptoir</a>
    <x-icon name="chevron-right" size="sm" />
    <span aria-current="page">« {{ $q }} »</span>
@endsection

@section('content')
<form method="GET" action="{{ route('counter.search') }}" class="lookup" role="search" style="margin-bottom:var(--s5)">
    <label class="lookup__field">
        <span class="sr-only">Rechercher</span>
        <x-icon name="search" />
        <input class="lookup__input" type="search" name="q" value="{{ $q }}" placeholder="Code de retrait, plaque, nom ou téléphone" autocomplete="off">
    </label>
    <button type="submit" class="btn btn--primary">Rechercher</button>
</form>

@if ($loans->isEmpty() && $clients->isEmpty())
    <section class="panel">
        <x-empty icon="search" title="Aucun résultat pour « {{ $q }} »" text="Vérifiez le code de retrait ou cherchez par nom, téléphone ou plaque. Pour un nouveau client, créez-lui un compte.">
            <a href="{{ route('counter.loans.create') }}" class="btn btn--primary">Location sur place</a>
        </x-empty>
    </section>
@else
    <div class="stack-lg">
        @if ($clients->isNotEmpty())
            <section class="panel panel--flush">
                <div class="panel__head"><h2 class="h3">Clients</h2><span class="small muted">{{ $clients->count() }}</span></div>
                <div class="table-wrap"><table class="table">
                    <thead><tr><th>Nom</th><th>Téléphone</th><th class="right">Locations</th><th class="right"><span class="sr-only">Actions</span></th></tr></thead>
                    <tbody>
                        @foreach ($clients as $c)
                            <tr>
                                <td><div class="person"><x-avatar :user="$c" /><div class="person__text"><a href="{{ route('counter.clients.show', $c) }}" class="table__link">{{ $c->nom }}</a><span class="table__sub">{{ $c->email }}</span></div></div></td>
                                <td class="nowrap">{{ $c->telephone ?: '—' }}</td>
                                <td class="right num">{{ $c->loans_count }}</td>
                                <td><div class="actions"><a href="{{ route('counter.loans.create', ['client' => $c->id]) }}" class="btn btn--sm">Nouvelle location</a></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            </section>
        @endif

        @if ($loans->isNotEmpty())
            <section class="panel panel--flush">
                <div class="panel__head"><h2 class="h3">Locations</h2><span class="small muted">{{ $loans->count() }}</span></div>
                @include('counter.loans.partials.table', ['loans' => $loans])
            </section>
        @endif
    </div>
@endif
@endsection
