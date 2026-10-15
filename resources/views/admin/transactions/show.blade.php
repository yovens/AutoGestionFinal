@extends('layouts.admin')

@php $T = \App\Models\Transaction::class; @endphp

@section('title', 'Reçu ' . $transaction->reference)

@section('crumbs')
    <a href="{{ route('admin.transactions.index') }}">Paiements</a>
    <x-icon name="chevron-right" size="sm" />
    <span aria-current="page">{{ $transaction->reference }}</span>
@endsection

@section('topbar')
    <button type="button" class="btn btn--sm no-print" onclick="window.print()"><x-icon name="printer" size="sm" /> Imprimer</button>
@endsection

@section('content')
<div class="grid-main">
    <article class="ticket" style="max-width:640px">
        <div class="ticket__top">
            <div class="row row--between" style="align-items:flex-start">
                <div class="logo">@include('layouts.partials.logo')</div>
                <x-status :model="$transaction" />
            </div>
            <div class="row row--between" style="margin-top:var(--s6);align-items:flex-end">
                <div>
                    <div class="small muted">Reçu</div>
                    <div class="ticket__ref num">{{ $transaction->reference }}</div>
                    <div class="small muted">{{ $transaction->created_at->translatedFormat('j F Y, H:i') }}</div>
                </div>
                <div style="text-align:right">
                    <div class="small muted">{{ $transaction->type_label }}</div>
                    <div class="ticket__amount num">@money($transaction->amount)</div>
                </div>
            </div>
        </div>

        <div class="ticket__tear" aria-hidden="true"></div>

        <div class="ticket__bottom">
            <dl class="dl">
                <dt>Client</dt>
                <dd>@if ($transaction->user)<a href="{{ route('admin.users.show', $transaction->user) }}">{{ $transaction->user->nom }}</a>@else Compte supprimé @endif</dd>
                <dt>Contact</dt><dd>{{ $transaction->user?->telephone ?: $transaction->user?->email ?? '—' }}</dd>
                <dt>Véhicule</dt>
                <dd>
                    @if ($transaction->vehicle)
                        <a href="{{ route('admin.vehicles.show', $transaction->vehicle) }}">{{ $transaction->vehicle->name }}</a>
                        <x-plate :plate="$transaction->vehicle->plate" style="margin-left:6px" />
                    @else — @endif
                </dd>
                @if ($transaction->loan)
                    <dt>Location liée</dt>
                    <dd><a href="{{ route('counter.loans.show', $transaction->loan) }}">{{ $transaction->loan->reference }}</a></dd>
                @endif
                @if ($transaction->note)<dt>Motif</dt><dd>{{ $transaction->note }}</dd>@endif
                <dt>Mode</dt><dd>{{ $transaction->method_label }}</dd>
                @if ($transaction->recorder)<dt>Encaissé par</dt><dd>{{ $transaction->recorder->nom }}</dd>@endif
                @foreach ($transaction->tickets as $tk)
                    <dt>Ticket</dt><dd><a href="{{ route('counter.tickets.show', $tk) }}">{{ $tk->code }}</a></dd>
                @endforeach
            </dl>
            <p class="tiny muted" style="margin-top:var(--s5)">{{ $agency['name'] }}, {{ $agency['city'] }}. {{ $agency['phone'] }}.</p>
        </div>
    </article>

    <aside class="stack no-print">
        @if ($transaction->status === $T::PENDING)
            <section class="panel">
                <div class="panel__head"><h2 class="h3">Achat en attente</h2></div>
                <div class="panel__body stack">
                    <p class="small muted">Le véhicule est réservé au client. Encaissez quand le paiement est reçu : le bon de remise s’imprime ensuite.</p>
                    <button type="button" class="btn btn--ok btn--block" data-dialog-open="sale-{{ $transaction->id }}"><x-icon name="check" size="sm" /> Encaisser</button>
                </div>
            </section>
            @include('counter.partials.sale-dialog', ['t' => $transaction])
        @endif
        @if (auth()->user()->isSuperAdmin())
        <section class="panel">
            <div class="panel__body">
                <form method="POST" action="{{ route('admin.transactions.destroy', $transaction) }}" data-confirm="Supprimer définitivement ce paiement ?">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn--danger btn--block"><x-icon name="trash" size="sm" /> Supprimer</button>
                </form>
                <p class="tiny muted" style="margin-top:var(--s2)">Réservé aux erreurs de saisie : le montant disparaît des totaux. L’opération reste inscrite au journal.</p>
            </div>
        </section>
        @endif
    </aside>
</div>
@endsection
