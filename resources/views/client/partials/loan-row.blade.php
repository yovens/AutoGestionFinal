@php
    $compact = $compact ?? false;
    $v = $loan->vehicle;
    $total = max(1, $loan->duration_days);
    $elapsed = $loan->start_date ? min($total, max(0, $loan->start_date->diffInDays(now()))) : 0;
    $pct = $loan->start_date ? round($elapsed / $total * 100) : 0;
@endphp
<div class="loan" id="loan-{{ $loan->id }}">
    @if ($v?->image_url)<img class="loan__img" src="{{ $v->image_url }}" alt="">@else<span class="loan__img"></span>@endif

    <div>
        <div class="row row--tight" style="margin-bottom:2px">
            <span class="loan__name">{{ $v?->name ?? 'Véhicule supprimé' }}</span>
            @if ($v)<x-plate :plate="$v->plate" />@endif
        </div>

        <div class="loan__meta">
            @switch($loan->status)
                @case(\App\Models\LoanCart::PENDING)
                    Demande du {{ $loan->created_at->translatedFormat('j F') }} pour {{ $loan->duration_days }} jour(s). L’agence doit la confirmer.
                    @break
                @case(\App\Models\LoanCart::APPROVED)
                    Du {{ $loan->start_date->translatedFormat('j F') }} au {{ $loan->end_date->translatedFormat('j F Y') }}.
                    <strong class="strong" style="color:var(--text)">{{ $loan->daysLeft() === 0 ? 'Retour aujourd’hui' : $loan->daysLeft() . ' jour(s) restant(s)' }}</strong>
                    @break
                @case(\App\Models\LoanCart::EXPIRED)
                    <span style="color:var(--alert);font-weight:600">Retour prévu le {{ $loan->end_date->translatedFormat('j F') }}, {{ $loan->daysLate() }} jour(s) de retard.</span>
                    Rapportez le véhicule ou demandez une prolongation.
                    @break
                @case(\App\Models\LoanCart::PENDING_RENEWAL)
                    Prolongation de {{ $loan->renewal_requested_days }} jour(s) demandée. Fin actuelle : {{ $loan->end_date?->translatedFormat('j F Y') }}.
                    @break
                @case(\App\Models\LoanCart::RETURNED)
                    Rendue le {{ $loan->returned_at?->translatedFormat('j F Y') }} après {{ $loan->duration_days }} jour(s).
                    @break
                @default
                    Demande du {{ $loan->created_at->translatedFormat('j F Y') }}.
            @endswitch
        </div>

        @if (in_array($loan->status, [\App\Models\LoanCart::APPROVED, \App\Models\LoanCart::EXPIRED, \App\Models\LoanCart::PENDING_RENEWAL]) && $loan->start_date)
            <div class="timeline" style="max-width:420px">
                <div class="timeline__bar" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100" aria-label="Avancement de la location">
                    <div class="timeline__fill {{ $loan->status === \App\Models\LoanCart::EXPIRED ? 'timeline__fill--alert' : '' }}" style="width: {{ $loan->status === \App\Models\LoanCart::EXPIRED ? 100 : $pct }}%"></div>
                </div>
            </div>
        @endif
    </div>

    <div class="loan__side">
        <x-status :model="$loan" />
        <span class="strong num">@money($loan->total_amount)</span>
        @if (!$compact && $loan->canRequestRenewal())
            <button type="button" class="btn btn--sm" data-dialog-open="renew-{{ $loan->id }}"><x-icon name="calendar" size="sm" /> Prolonger</button>
        @elseif ($compact && $loan->canRequestRenewal())
            <a href="{{ route('client.loan') }}#loan-{{ $loan->id }}" class="small">Prolonger</a>
        @endif
    </div>
</div>

@if (!$compact && $loan->canRequestRenewal())
    <x-dialog id="renew-{{ $loan->id }}" title="Prolonger la location" size="sm" :open-on-load="$errors->has('additional_days') && old('loan_id') == $loan->id">
        <form method="POST" action="{{ route('client.loan.renew', $loan->id) }}" data-booking data-price="{{ $v?->loan_price ?? 0 }}">
            @csrf
            <input type="hidden" name="loan_id" value="{{ $loan->id }}">
            <div class="modal__body stack">
                <p class="muted small">{{ $v?->name }}, @money($v?->loan_price ?? 0) par jour. L’agence valide la prolongation ; le supplément se règle sur place.</p>
                <div class="field">
                    <label class="field__label" for="days-{{ $loan->id }}">Jours supplémentaires</label>
                    <div class="input-group" style="max-width:220px">
                        <button type="button" class="btn" data-step="-1" aria-label="Un jour de moins"><x-icon name="minus" size="sm" /></button>
                        <input class="input" id="days-{{ $loan->id }}" type="number" name="additional_days" min="1" max="365" value="{{ old('additional_days', 3) }}" data-days inputmode="numeric">
                        <button type="button" class="btn" data-step="1" aria-label="Un jour de plus"><x-icon name="plus" size="sm" /></button>
                    </div>
                    @error('additional_days')<span class="field__error">{{ $message }}</span>@enderror
                </div>
                <div class="total-line"><span class="muted">Supplément estimé</span><span class="total-line__value num" data-total>—</span></div>
            </div>
            <div class="modal__foot">
                <button type="button" class="btn" data-dialog-close>Annuler</button>
                <button type="submit" class="btn btn--primary">Envoyer la demande</button>
            </div>
        </form>
    </x-dialog>
@endif
