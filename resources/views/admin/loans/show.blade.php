@extends('layouts.admin')

@php
    $L = \App\Models\LoanCart::class;
    $v = $loan->vehicle;
    $out = in_array($loan->status, [$L::APPROVED, $L::EXPIRED, $L::PENDING_RENEWAL]);
    $total = max(1, $loan->duration_days);
    $elapsed = $loan->start_date ? min($total, max(0, $loan->start_date->diffInDays(now()))) : 0;
@endphp

@section('title', 'Location n° ' . $loan->id)

@section('crumbs')
    <a href="{{ route('admin.loans.index') }}">Locations</a>
    <x-icon name="chevron-right" size="sm" />
    <span aria-current="page">N° {{ $loan->id }}</span>
@endsection

@section('content')
<div class="grid-main grid-main--wide-side">
    <div class="stack">
        {{-- Fich lokasyon an fòm tikè --}}
        <article class="ticket">
            <div class="ticket__top">
                <div class="row row--between" style="align-items:flex-start">
                    <div>
                        <div class="small muted">Location</div>
                        <div class="ticket__ref num">LOC-{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <x-status :model="$loan" />
                </div>

                <div class="row" style="margin-top:var(--s5);align-items:center">
                    @if ($v?->image_url)<img src="{{ $v->image_url }}" alt="" class="thumb" style="width:120px;height:80px">@endif
                    <div>
                        <x-plate :plate="$v?->plate ?? '—'" />
                        <div class="h2" style="margin-top:6px">{{ $v?->name ?? 'Véhicule supprimé' }}</div>
                        @if ($v)<a href="{{ route('admin.vehicles.show', $v) }}" class="small">Fiche du véhicule</a>@endif
                    </div>
                </div>

                @if ($loan->start_date)
                    <div class="timeline" style="margin-top:var(--s5)">
                        <div class="timeline__bar"><div class="timeline__fill {{ $loan->status === $L::EXPIRED ? 'timeline__fill--alert' : '' }}" style="width: {{ $loan->status === $L::RETURNED || $loan->status === $L::EXPIRED ? 100 : round($elapsed / $total * 100) }}%"></div></div>
                        <div class="timeline__ends">
                            <span>Départ {{ $loan->start_date->translatedFormat('j M Y') }}</span>
                            <span>Retour prévu {{ $loan->end_date->translatedFormat('j M Y') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="ticket__tear" aria-hidden="true"></div>

            <div class="ticket__bottom">
                <dl class="dl">
                    <dt>Client</dt>
                    <dd>@if ($loan->user)<a href="{{ route('admin.users.show', $loan->user) }}">{{ $loan->user->nom }}</a>@else Compte supprimé @endif</dd>
                    <dt>Téléphone</dt><dd>{{ $loan->user?->telephone ?: '—' }}</dd>
                    <dt>Demande faite le</dt><dd>{{ $loan->created_at->translatedFormat('j M Y, H:i') }}</dd>
                    <dt>Durée</dt><dd>{{ $loan->duration_days }} jour(s) à @money($v?->loan_price ?? 0)</dd>
                    @if ($loan->status === $L::EXPIRED)<dt>Retard</dt><dd style="color:var(--alert)">{{ $loan->daysLate() }} jour(s)</dd>@endif
                    @if ($loan->status === $L::PENDING_RENEWAL)<dt>Prolongation demandée</dt><dd>+{{ $loan->renewal_requested_days }} jour(s)</dd>@endif
                    @if ($loan->returned_at)<dt>Rendue le</dt><dd>{{ $loan->returned_at->translatedFormat('j M Y, H:i') }}</dd>@endif
                </dl>
                <div class="row row--between" style="margin-top:var(--s5);padding-top:var(--s4);border-top:1px solid var(--line-soft)">
                    <span class="muted">Montant total</span>
                    <span class="ticket__amount num">@money($loan->total_amount)</span>
                </div>
            </div>
        </article>

        <section class="panel panel--flush">
            <div class="panel__head"><h2 class="h3">Paiements liés</h2></div>
            @if ($loan->transactions->isEmpty())
                <x-empty icon="receipt" title="Aucun paiement" text="Le paiement est enregistré automatiquement à l’approbation et à chaque prolongation." />
            @else
                <div class="table-wrap"><table class="table">
                    <thead><tr><th>Référence</th><th>Date</th><th>Statut</th><th class="right">Montant</th></tr></thead>
                    <tbody>
                        @foreach ($loan->transactions as $t)
                            <tr>
                                <td><a href="{{ route('admin.transactions.show', $t) }}" class="table__link num">{{ $t->reference }}</a></td>
                                <td>{{ $t->created_at->translatedFormat('j M Y') }}</td>
                                <td><x-status :model="$t" /></td>
                                <td class="right strong num">@money($t->amount)</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            @endif
        </section>
    </div>

    <aside class="stack">
        @if (in_array($loan->status, [$L::PENDING, $L::PENDING_RENEWAL]) || $out)
            <section class="panel">
                <div class="panel__head"><h2 class="h3">Actions</h2></div>
                <div class="panel__body row row--tight">@include('admin.loans.partials.actions')</div>
            </section>
        @endif

        @if ($out)
            <section class="panel">
                <div class="panel__head"><h2 class="h3">Prolonger directement</h2></div>
                <form method="POST" action="{{ route('admin.loans.extend', $loan) }}" data-booking data-price="{{ $v?->loan_price ?? 0 }}">
                    @csrf
                    <div class="panel__body stack">
                        <div class="field">
                            <label class="field__label" for="extend-days">Jours supplémentaires</label>
                            <div class="input-group" style="max-width:220px">
                                <button type="button" class="btn" data-step="-1" aria-label="Un jour de moins"><x-icon name="minus" size="sm" /></button>
                                <input class="input" id="extend-days" type="number" name="additional_days" min="1" max="365" value="{{ old('additional_days', $loan->renewal_requested_days ?? 1) }}" data-days>
                                <button type="button" class="btn" data-step="1" aria-label="Un jour de plus"><x-icon name="plus" size="sm" /></button>
                            </div>
                            @error('additional_days')<span class="field__error">{{ $message }}</span>@enderror
                        </div>
                        <div class="total-line"><span class="muted">Supplément encaissé</span><span class="total-line__value num" data-total>—</span></div>
                    </div>
                    <div class="panel__foot row row--end"><button type="submit" class="btn btn--primary">Prolonger et encaisser</button></div>
                </form>
            </section>
        @endif

        <section class="panel">
            <div class="panel__body">
                <form method="POST" action="{{ route('admin.loans.destroy', $loan) }}" data-confirm="Supprimer définitivement cette location ?{{ in_array($loan->status, $L::HOLDING) ? ' Le véhicule sera remis en stock.' : '' }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn--danger btn--block"><x-icon name="trash" size="sm" /> Supprimer la location</button>
                </form>
                <p class="tiny muted" style="margin-top:var(--s2)">À utiliser pour une erreur de saisie. Pour un véhicule rendu, utilisez « Retour reçu ».</p>
            </div>
        </section>
    </aside>
</div>
@endsection
