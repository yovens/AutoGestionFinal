{{-- Tablo lokasyon (lis + rechèch). --}}
@php $L = \App\Models\LoanCart::class; @endphp
<div class="table-wrap">
    <table class="table">
        <thead>
            <tr><th>Véhicule</th><th>Client</th><th>Période</th><th class="right">Montant</th><th>Statut</th><th class="right"><span class="sr-only">Actions</span></th></tr>
        </thead>
        <tbody>
            @foreach ($loans as $loan)
                <tr class="{{ $loan->status === $L::EXPIRED ? 'is-alert' : (in_array($loan->status, [$L::PENDING, $L::PENDING_RENEWAL, $L::READY]) ? 'is-wait' : '') }}">
                    <td>
                        <div class="row row--tight" style="flex-wrap:nowrap">
                            <x-plate :plate="$loan->vehicle?->plate ?? '—'" />
                            <a href="{{ route('counter.loans.show', $loan) }}" class="table__link nowrap">{{ $loan->vehicle?->name ?? 'Véhicule supprimé' }}</a>
                        </div>
                    </td>
                    <td class="nowrap">
                        @if ($loan->user)
                            <a href="{{ route('counter.clients.show', $loan->user) }}" class="table__link" style="font-weight:500">{{ $loan->user->nom }}</a>
                            <span class="table__sub">{{ $loan->user->telephone ?: $loan->user->email }}</span>
                        @else
                            <span class="muted">Compte supprimé</span>
                        @endif
                    </td>
                    <td class="nowrap">
                        @if ($loan->start_date)
                            {{ $loan->start_date->translatedFormat('j M') }} au {{ $loan->end_date->translatedFormat('j M') }}
                        @elseif ($loan->status === $L::READY)
                            Code <strong class="num">{{ $loan->pickup_code }}</strong>
                        @else
                            <span class="muted">Pas commencée</span>
                        @endif
                        <span class="table__sub">{{ $loan->duration_days }} jour(s)@if ($loan->status === $L::EXPIRED), <span style="color:var(--alert);font-weight:600">{{ $loan->daysLate() }} j de retard</span>@elseif ($loan->status === $L::PENDING_RENEWAL), +{{ $loan->renewal_requested_days }} j demandés @endif</span>
                    </td>
                    <td class="right strong num">@money($loan->total_amount)</td>
                    <td><x-status :model="$loan" /></td>
                    <td>
                        <div class="actions">
                            @include('counter.loans.partials.actions')
                            <a href="{{ route('counter.loans.show', $loan) }}" class="btn btn--sm btn--ghost btn--icon" aria-label="Voir la fiche"><x-icon name="chevron-right" size="sm" /></a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
