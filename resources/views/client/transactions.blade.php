@extends('layouts.client')
@section('title', 'Transactions')

@section('content')

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">📄 Mes Transactions</h2>
        <span style="color: var(--text-color); opacity: 0.7;">
            Total : {{ $transactions->count() }} transaction(s)
        </span>
    </div>

    @if($transactions->isEmpty())
        <div style="text-align: center; padding: 40px; color: var(--text-color);">
            <i class="fas fa-receipt" style="font-size: 3rem; opacity: 0.3; margin-bottom: 15px;"></i>
            <p>Aucune transaction enregistrée pour le moment.</p>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--bg-color);">
                        <th style="padding: 15px; text-align: left;">Type</th>
                        <th style="padding: 15px; text-align: left;">Véhicule</th>
                        <th style="padding: 15px; text-align: left;">Montant</th>
                        <th style="padding: 15px; text-align: left;">Date</th>
                        <th style="padding: 15px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                    <tr style="border-bottom: 1px solid rgba(128,128,128,0.1);">
                        <td style="padding: 15px;">
                            <span style="padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; font-weight: bold; color: #fff; 
                                   background-color: {{ $t->type === 'vente' ? '#10b981' : 'var(--primary)' }};">
                                {{ ucfirst($t->type) }}
                            </span>
                        </td>
                        <td style="padding: 15px; font-weight: 500;">
                            {{ $t->vehicle->brand }} <span style="opacity: 0.7;">{{ $t->vehicle->model }}</span>
                        </td>
                        <td style="padding: 15px;">{{ number_format($t->amount, 2) }} HTG</td>
                        <td style="padding: 15px; opacity: 0.8;">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                        <td style="padding: 15px; text-align: center;">
                            <button style="background: none; border: none; color: var(--primary); cursor: pointer;">
                                <i class="fas fa-file-invoice"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection