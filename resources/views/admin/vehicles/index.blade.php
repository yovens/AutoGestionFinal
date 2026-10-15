@extends('layouts.admin')

@section('title', 'Parc automobile')

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Parc automobile</h1>
        <p class="muted">{{ $vehicles->count() }} modèle(s), {{ $vehicles->sum('quantity') }} exemplaire(s) disponible(s), {{ $vehicles->sum('out_count') }} sorti(s) ou réservé(s).</p>
    </div>
    <div class="page-head__actions">
        <button type="button" class="btn btn--primary" data-dialog-open="new"><x-icon name="plus" size="sm" /> Ajouter un véhicule</button>
    </div>
</div>

<form method="GET" class="toolbar" role="search">
    <div class="input-icon toolbar__search">
        <x-icon name="search" size="sm" />
        <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Marque, modèle ou plaque" aria-label="Rechercher un véhicule">
    </div>
    @if ($search)<a href="{{ route('admin.vehicles.index') }}" class="btn btn--ghost">Effacer</a>@endif
</form>

<section class="panel panel--flush">
    @if ($vehicles->isEmpty())
        <x-empty icon="car" :title="$search ? 'Aucun véhicule pour « ' . $search . ' »' : 'Le parc est vide'" text="Ajoutez un premier véhicule avec sa photo, ses prix et son stock.">
            <button type="button" class="btn btn--primary" data-dialog-open="new">Ajouter un véhicule</button>
        </x-empty>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Véhicule</th><th>Plaque</th><th class="right">Vente</th><th class="right">Location / jour</th><th>Stock</th><th class="right"><span class="sr-only">Actions</span></th></tr>
                </thead>
                <tbody>
                    @foreach ($vehicles as $v)
                        <tr>
                            <td>
                                <div class="person">
                                    @if ($v->image_url)<img src="{{ $v->image_url }}" alt="" class="thumb" loading="lazy">@else<span class="thumb"></span>@endif
                                    <div class="person__text">
                                        <a href="{{ route('admin.vehicles.show', $v) }}" class="table__link">{{ $v->name }}</a>
                                        <span class="table__sub">{{ $v->year }}</span>
                                    </div>
                                </div>
                            </td>
                            <td><x-plate :plate="$v->plate" /></td>
                            <td class="right num">@money($v->price)</td>
                            <td class="right num">@money($v->loan_price)</td>
                            <td>
                                @if ($v->quantity > 0)
                                    <x-badge tone="ok">{{ $v->quantity }} dispo.</x-badge>
                                @else
                                    <x-badge>Épuisé</x-badge>
                                @endif
                                @if ($v->out_count)<span class="table__sub">{{ $v->out_count }} sorti(s)</span>@endif
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.vehicles.show', $v) }}" class="btn btn--sm"><x-icon name="edit" size="sm" /> Modifier</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>

<x-dialog id="new" title="Ajouter un véhicule" :open-on-load="$errors->any()">
    <form method="POST" action="{{ route('admin.vehicles.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal__body">@include('admin.vehicles.partials.form')</div>
        <div class="modal__foot">
            <button type="button" class="btn" data-dialog-close>Annuler</button>
            <button type="submit" class="btn btn--primary">Ajouter au parc</button>
        </div>
    </form>
</x-dialog>
@endsection
