@extends('layouts.admin')

@section('title', 'Journal d’activité')

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Journal d’activité</h1>
        <p class="muted">Qui a fait quoi, et quand : confirmations, remises de clés, retours, encaissements, changements de prix, de rôles et suppressions.</p>
    </div>
</div>

<form method="GET" class="panel filters" style="margin-bottom:var(--s5);grid-template-columns:minmax(180px,2fr) repeat(4,minmax(130px,1fr)) auto">
    <div class="field">
        <label class="field__label" for="q">Recherche</label>
        <div class="input-icon"><x-icon name="search" size="sm" /><input class="input" type="search" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Plaque, LOC-, nom..."></div>
    </div>
    <div class="field">
        <label class="field__label" for="user">Personne</label>
        <select class="select" id="user" name="user">
            <option value="">Tout le monde</option>
            @foreach ($staff as $u)<option value="{{ $u->id }}" @selected(($filters['user'] ?? '') == $u->id)>{{ $u->nom }}</option>@endforeach
        </select>
    </div>
    <div class="field">
        <label class="field__label" for="group">Domaine</label>
        <select class="select" id="group" name="group">
            <option value="">Tous</option>
            @foreach (\App\Models\ActivityLog::GROUPS as $k => $label)<option value="{{ $k }}" @selected(($filters['group'] ?? '') === $k)>{{ $label }}</option>@endforeach
        </select>
    </div>
    <div class="field"><label class="field__label" for="from">Du</label><input class="input" type="date" id="from" name="from" value="{{ $filters['from'] ?? '' }}"></div>
    <div class="field"><label class="field__label" for="to">Au</label><input class="input" type="date" id="to" name="to" value="{{ $filters['to'] ?? '' }}"></div>
    <div class="row row--tight">
        <button class="btn btn--primary"><x-icon name="filter" size="sm" /> Filtrer</button>
        @if (array_filter($filters))<a href="{{ route('super.activity') }}" class="btn btn--ghost">Effacer</a>@endif
    </div>
</form>

<section class="panel panel--flush">
    @if ($logs->isEmpty())
        <x-empty icon="clock" title="Rien dans le journal pour ces critères" text="Les actions du personnel sont enregistrées automatiquement à partir de maintenant." />
    @else
        <div class="table-wrap"><table class="table">
            <thead><tr><th>Quand</th><th>Qui</th><th>Domaine</th><th>Action</th></tr></thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td class="nowrap"><span class="num">{{ $log->created_at->translatedFormat('j M Y') }}</span><span class="table__sub num">{{ $log->created_at->format('H:i') }}</span></td>
                        <td class="nowrap">
                            @if ($log->user)
                                <div class="person"><x-avatar :user="$log->user" style="--size:28px" /><div class="person__text"><span class="strong">{{ $log->user->nom }}</span><span class="table__sub">{{ $log->user->role_label }}</span></div></div>
                            @else
                                <span class="muted">Système</span>
                            @endif
                        </td>
                        <td><x-badge :tone="$log->tone">{{ $log->group_label }}</x-badge></td>
                        <td style="min-width:320px">
                            {{ $log->description }}
                            @if ($log->subject_type === (new \App\Models\LoanCart)->getMorphClass() && $log->subject_id)
                                <a href="{{ route('counter.loans.show', $log->subject_id) }}" class="small">Voir</a>
                            @elseif ($log->subject_type === (new \App\Models\Transaction)->getMorphClass() && $log->subject_id)
                                <a href="{{ route('admin.transactions.show', $log->subject_id) }}" class="small">Voir</a>
                            @elseif ($log->subject_type === (new \App\Models\Vehicle)->getMorphClass() && $log->subject_id)
                                <a href="{{ route('admin.vehicles.show', $log->subject_id) }}" class="small">Voir</a>
                            @elseif ($log->subject_type === (new \App\Models\User)->getMorphClass() && $log->subject_id && $log->action !== 'auth.login')
                                <a href="{{ route('admin.users.show', $log->subject_id) }}" class="small">Voir</a>
                            @endif
                            @if ($log->ip)<span class="table__sub">IP {{ $log->ip }}</span>@endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table></div>
        {{ $logs->links('partials.pagination') }}
    @endif
</section>
@endsection
