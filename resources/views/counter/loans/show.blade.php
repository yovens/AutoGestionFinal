@extends('layouts.admin')

@php
    $L = \App\Models\LoanCart::class;
    $v = $loan->vehicle;
    $out = in_array($loan->status, $L::OUT);
    $total = max(1, $loan->duration_days);
    $elapsed = $loan->start_date ? min($total, max(0, $loan->start_date->diffInDays(now()))) : 0;
    $isAdmin = auth()->user()->isAdmin();
@endphp

@section('title', $loan->reference)

@section('crumbs')
    <a href="{{ route('counter.loans.index') }}">Locations</a>
    <x-icon name="chevron-right" size="sm" />
    <span aria-current="page">{{ $loan->reference }}</span>
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
                        <div class="ticket__ref num">{{ $loan->reference }}</div>
                    </div>
                    <x-status :model="$loan" />
                </div>

                <div class="row" style="margin-top:var(--s5);align-items:center">
                    @if ($v?->image_url)<img src="{{ $v->image_url }}" alt="" class="thumb" style="width:120px;height:80px">@endif
                    <div class="grow">
                        <x-plate :plate="$v?->plate ?? '—'" />
                        <div class="h2" style="margin-top:6px">{{ $v?->name ?? 'Véhicule supprimé' }}</div>
                        @if ($v && $isAdmin)<a href="{{ route('admin.vehicles.show', $v) }}" class="small">Fiche du véhicule</a>@endif
                    </div>
                    @if ($loan->status === $L::READY)
                        <span class="pickup-code"><small>Code</small>{{ $loan->pickup_code }}</span>
                    @endif
                </div>

                @if ($loan->start_date)
                    <div class="timeline" style="margin-top:var(--s5)">
                        <div class="timeline__bar"><div class="timeline__fill {{ $loan->status === $L::EXPIRED ? 'timeline__fill--alert' : '' }}" style="width: {{ in_array($loan->status, [$L::RETURNED, $L::EXPIRED]) ? 100 : round($elapsed / $total * 100) }}%"></div></div>
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
                    <dd>@if ($loan->user)<a href="{{ route('counter.clients.show', $loan->user) }}">{{ $loan->user->nom }}</a>@else Compte supprimé @endif</dd>
                    <dt>Téléphone</dt><dd>@if ($loan->user?->telephone)<a href="tel:{{ preg_replace('/\s+/', '', $loan->user->telephone) }}">{{ $loan->user->telephone }}</a>@else — @endif</dd>
                    <dt>Demande faite le</dt><dd>{{ $loan->created_at->translatedFormat('j M Y, H:i') }}</dd>
                    <dt>Durée</dt><dd>{{ $loan->duration_days }} jour(s) à @money($v?->loan_price ?? 0)</dd>
                    @if ($loan->status === $L::EXPIRED)<dt>Retard</dt><dd style="color:var(--alert)">{{ $loan->daysLate() }} jour(s)</dd>@endif
                    @if ($loan->status === $L::PENDING_RENEWAL)<dt>Prolongation demandée</dt><dd>+{{ $loan->renewal_requested_days }} jour(s)</dd>@endif
                </dl>
                <div class="row row--between" style="margin-top:var(--s5);padding-top:var(--s4);border-top:1px solid var(--line-soft)">
                    <span class="muted">Montant total</span>
                    <span class="ticket__amount num">@money($loan->total_amount)</span>
                </div>
            </div>
        </article>

        @if ($loan->picked_up_at)
            <section class="panel">
                <div class="panel__head"><h2 class="h3">Départ et retour</h2></div>
                <div class="panel__body grid-2" style="gap:var(--s5)">
                    <div>
                        <h3 class="small strong" style="margin-bottom:var(--s2)">Remise des clés</h3>
                        <dl class="dl">
                            <dt>Le</dt><dd>{{ $loan->picked_up_at->translatedFormat('j M Y, H:i') }}</dd>
                            <dt>Par</dt><dd>{{ $loan->checkoutAgent?->nom ?? '—' }}</dd>
                            <dt>Kilométrage</dt><dd>{{ $loan->km_out !== null ? number_format($loan->km_out, 0, ',', ' ') . ' km' : '—' }}</dd>
                            <dt>Carburant</dt><dd><x-fuel-read :level="$loan->fuel_out" /></dd>
                            <dt>Pièce</dt><dd>{{ $loan->id_document ?: 'Vérifiée' }}</dd>
                            <dt>Caution</dt><dd>@money($loan->deposit)</dd>
                        </dl>
                        @if ($loan->condition_out)<p class="small" style="margin-top:var(--s3)"><span class="muted">État :</span> {{ $loan->condition_out }}</p>@endif
                    </div>
                    <div>
                        <h3 class="small strong" style="margin-bottom:var(--s2)">Retour</h3>
                        @if ($loan->returned_at)
                            <dl class="dl">
                                <dt>Le</dt><dd>{{ $loan->returned_at->translatedFormat('j M Y, H:i') }}</dd>
                                <dt>Par</dt><dd>{{ $loan->checkinAgent?->nom ?? '—' }}</dd>
                                <dt>Kilométrage</dt><dd>{{ $loan->km_in !== null ? number_format($loan->km_in, 0, ',', ' ') . ' km' : '—' }}</dd>
                                <dt>Parcouru</dt><dd>{{ $loan->km_in !== null && $loan->km_out !== null ? number_format($loan->km_in - $loan->km_out, 0, ',', ' ') . ' km' : '—' }}</dd>
                                <dt>Carburant</dt><dd><x-fuel-read :level="$loan->fuel_in" /></dd>
                                <dt>Caution</dt><dd>{{ $loan->deposit ? ($loan->deposit_returned ? 'Rendue' : 'Retenue') : '—' }}</dd>
                            </dl>
                            @if ($loan->condition_in)<p class="small" style="margin-top:var(--s3)"><span class="muted">État :</span> {{ $loan->condition_in }}</p>@endif
                        @else
                            <p class="small muted">Le véhicule n’est pas encore revenu.</p>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        <section class="panel panel--flush">
            <div class="panel__head"><h2 class="h3">Paiements et tickets</h2></div>
            @if ($loan->transactions->isEmpty() && $loan->tickets->isEmpty())
                <x-empty icon="receipt" title="Rien encore" text="Le paiement est enregistré à la remise des clés, avec le bon de sortie." />
            @else
                <div class="table-wrap"><table class="table">
                    <thead><tr><th>Opération</th><th>Date</th><th>Mode</th><th>Statut</th><th class="right">Montant</th></tr></thead>
                    <tbody>
                        @foreach ($loan->transactions as $t)
                            <tr>
                                <td><span class="strong">{{ $t->note ?: $t->type_label }}</span><span class="table__sub num">{{ $t->reference }}{{ $t->recorder ? ', par ' . $t->recorder->nom : '' }}</span></td>
                                <td class="nowrap">{{ $t->updated_at->translatedFormat('j M Y, H:i') }}</td>
                                <td>{{ $t->method_label }}</td>
                                <td><x-status :model="$t" /></td>
                                <td class="right strong num">@money($t->amount)</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
                @if ($loan->tickets->isNotEmpty())
                    <div class="panel__foot row">
                        @foreach ($loan->tickets as $tk)
                            <a href="{{ route('counter.tickets.show', $tk) }}" class="btn btn--sm"><x-icon name="printer" size="sm" /> {{ $tk->title }} {{ $tk->code }}</a>
                        @endforeach
                    </div>
                @endif
            @endif
        </section>
    </div>

    <aside class="stack">
        @if (in_array($loan->status, [$L::PENDING, $L::READY, $L::PENDING_RENEWAL]) || $out)
            <section class="panel">
                <div class="panel__head"><h2 class="h3">Que faire maintenant</h2></div>
                <div class="panel__body stack">
                    <p class="small muted">
                        @switch($loan->status)
                            @case($L::PENDING) Demande faite en ligne. Confirmez si le véhicule est prêt : le client reçoit son code de retrait. @break
                            @case($L::READY) Le client présente le code <strong>{{ $loan->pickup_code }}</strong> à l’agence pour récupérer les clés. @break
                            @case($L::EXPIRED) Le véhicule devait revenir le {{ $loan->end_date->translatedFormat('j M') }}. Appelez le client ou réceptionnez le retour. @break
                            @case($L::PENDING_RENEWAL) Le client demande {{ $loan->renewal_requested_days }} jour(s) de plus. @break
                            @default Le véhicule est chez le client jusqu’au {{ $loan->end_date?->translatedFormat('j M') }}.
                        @endswitch
                    </p>
                    <div class="row row--tight">@include('counter.loans.partials.actions')</div>
                    @if ($loan->status === $L::READY)
                        <form method="POST" action="{{ route('counter.loans.cancel', $loan) }}" data-confirm="Le client ne viendra pas ? La location est annulée et le véhicule remis en stock.">
                            @csrf<button type="submit" class="btn btn--sm btn--ghost btn--danger">Le client ne vient pas : annuler</button>
                        </form>
                    @endif
                </div>
            </section>
        @endif

        @if ($out)
            <section class="panel">
                <div class="panel__head"><h2 class="h3">Prolonger au comptoir</h2></div>
                <form method="POST" action="{{ route('counter.loans.extend', $loan) }}" data-booking data-price="{{ $v?->loan_price ?? 0 }}">
                    @csrf
                    <div class="panel__body stack">
                        <div class="field">
                            <label class="field__label" for="extend-days">Jours supplémentaires</label>
                            <div class="input-group" style="max-width:220px">
                                <button type="button" class="btn" data-step="-1" aria-label="Un jour de moins"><x-icon name="minus" size="sm" /></button>
                                <input class="input" id="extend-days" type="number" name="additional_days" min="1" max="365" value="{{ old('additional_days', 1) }}" data-days>
                                <button type="button" class="btn" data-step="1" aria-label="Un jour de plus"><x-icon name="plus" size="sm" /></button>
                            </div>
                            @error('additional_days')<span class="field__error">{{ $message }}</span>@enderror
                        </div>
                        <div class="total-line"><span class="muted">Supplément</span><span class="total-line__value num" data-total>—</span></div>
                        <x-methods id-prefix="ext" required />
                    </div>
                    <div class="panel__foot row row--end"><button type="submit" class="btn btn--primary">Encaisser et prolonger</button></div>
                </form>
            </section>
        @endif

        @if ($isAdmin)
            <section class="panel">
                <div class="panel__body">
                    <form method="POST" action="{{ route('counter.loans.destroy', $loan) }}" data-confirm="Supprimer définitivement cette location ?{{ in_array($loan->status, $L::HOLDING) ? ' Le véhicule sera remis en stock.' : '' }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn--danger btn--block"><x-icon name="trash" size="sm" /> Supprimer la location</button>
                    </form>
                    <p class="tiny muted" style="margin-top:var(--s2)">Réservé aux erreurs de saisie. Pour un véhicule rendu, utilisez « Réceptionner ».</p>
                </div>
            </section>
        @endif
    </aside>
</div>
@endsection
