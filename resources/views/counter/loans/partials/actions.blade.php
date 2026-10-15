{{-- Bouton aksyon pou yon lokasyon selon estati li (comptoir, lis, dashboard admin). --}}
@php $L = \App\Models\LoanCart::class; @endphp
@switch($loan->status)
    @case($L::PENDING)
        <form method="POST" action="{{ route('counter.loans.approve', $loan) }}" class="inline-form">
            @csrf
            <button type="submit" class="btn btn--sm btn--ok"><x-icon name="check" size="sm" /> Confirmer</button>
        </form>
        <form method="POST" action="{{ route('counter.loans.reject', $loan) }}" class="inline-form" data-confirm="Refuser la demande de {{ $loan->user?->nom }} ? Le véhicule sera remis en stock.">
            @csrf
            <button type="submit" class="btn btn--sm btn--danger">Refuser</button>
        </form>
        @break
    @case($L::READY)
        <a href="{{ route('counter.loans.checkout', $loan) }}" class="btn btn--sm btn--primary"><x-icon name="key" size="sm" /> Remettre les clés</a>
        @break
    @case($L::PENDING_RENEWAL)
        <form method="POST" action="{{ route('counter.loans.renewal.approve', $loan) }}" class="inline-form">
            @csrf
            <button type="submit" class="btn btn--sm btn--ok"><x-icon name="check" size="sm" /> Accepter +{{ $loan->renewal_requested_days }} j</button>
        </form>
        <form method="POST" action="{{ route('counter.loans.renewal.decline', $loan) }}" class="inline-form">
            @csrf
            <button type="submit" class="btn btn--sm">Refuser</button>
        </form>
        <a href="{{ route('counter.loans.checkin', $loan) }}" class="btn btn--sm"><x-icon name="return" size="sm" /> Retour</a>
        @break
    @case($L::APPROVED)
    @case($L::EXPIRED)
        <a href="{{ route('counter.loans.checkin', $loan) }}" class="btn btn--sm {{ $loan->status === $L::EXPIRED ? 'btn--primary' : '' }}"><x-icon name="return" size="sm" /> Réceptionner</a>
        @break
@endswitch
