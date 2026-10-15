{{-- Bouton aksyon pou yon lokasyon selon estati li. Itilize nan dashboard, lis ak detay. --}}
@php $L = \App\Models\LoanCart::class; @endphp
@switch($loan->status)
    @case($L::PENDING)
        <form method="POST" action="{{ route('admin.loans.approve', $loan) }}" class="inline-form">
            @csrf
            <button type="submit" class="btn btn--sm btn--ok"><x-icon name="check" size="sm" /> Approuver</button>
        </form>
        <form method="POST" action="{{ route('admin.loans.reject', $loan) }}" class="inline-form" data-confirm="Refuser la demande de {{ $loan->user?->nom }} ? Le véhicule sera remis en stock.">
            @csrf
            <button type="submit" class="btn btn--sm btn--danger">Refuser</button>
        </form>
        @break
    @case($L::PENDING_RENEWAL)
        <form method="POST" action="{{ route('admin.loans.renewal.approve', $loan) }}" class="inline-form">
            @csrf
            <button type="submit" class="btn btn--sm btn--ok"><x-icon name="check" size="sm" /> Accepter +{{ $loan->renewal_requested_days }} j</button>
        </form>
        <form method="POST" action="{{ route('admin.loans.renewal.decline', $loan) }}" class="inline-form">
            @csrf
            <button type="submit" class="btn btn--sm">Refuser</button>
        </form>
        @break
    @case($L::APPROVED)
    @case($L::EXPIRED)
        <form method="POST" action="{{ route('admin.loans.return', $loan) }}" class="inline-form" data-confirm="Confirmer le retour de {{ $loan->vehicle?->name }} ({{ $loan->vehicle?->plate }}) ?">
            @csrf
            <button type="submit" class="btn btn--sm {{ $loan->status === $L::EXPIRED ? 'btn--primary' : '' }}"><x-icon name="return" size="sm" /> Retour reçu</button>
        </form>
        @break
@endswitch
