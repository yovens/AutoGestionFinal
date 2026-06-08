@extends('admin.layout')

@section('title', 'Détails Véhicule - AutoGestion Admin')
@section('page_title', 'Fiche Véhicule')

@section('content')

{{-- ================= FICHE PRINCIPALE VÉHICULE ================= --}}
<div class="card-panel" style="background: var(--panel-bg, #1e293b); padding: 24px; border-radius: 14px; border: 1px solid var(--panel-border, #334155); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);">
    <div class="vehicle-detail-header" style="display: flex; gap: 24px; align-items: center; flex-wrap: wrap;">
        
        <div style="width: 280px; height: 180px; border-radius: 12px; overflow: hidden; border: 1px solid var(--panel-border, #334155); background: var(--panel-hover, #1e293b); flex-shrink: 0;">
            <img src="/storage/{{ $vehicle->image }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Image du véhicule">
        </div>

        <div style="flex: 1; min-width: 250px;">
            <h2 style="font-size: 24px; font-weight: 700; margin: 0 0 12px 0; color: var(--text-main, #fff);">
                {{ $vehicle->brand }} <span style="color: var(--brand-primary, #3b82f6);">{{ $vehicle->model }}</span>
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px; margin-bottom: 16px; font-size: 14px;">
                <p style="margin: 0; color: var(--text-muted, #94a3b8);">
                    <strong style="color: var(--text-main, #cbd5e1);">Plaque :</strong> 
                    <code style="background: var(--panel-hover, #0f172a); padding: 2px 6px; border-radius: 4px; border: 1px solid var(--panel-border, #334155); color: #fff;">{{ $vehicle->plate }}</code>
                </p>
                <p style="margin: 0; color: var(--text-muted, #94a3b8);">
                    <strong style="color: var(--text-main, #cbd5e1);">Année :</strong> {{ $vehicle->year }}
                </p>
                <p style="margin: 0; color: var(--text-muted, #94a3b8);">
                    <strong style="color: var(--text-main, #cbd5e1);">Prix vente :</strong> 
                    <span style="color: var(--brand-warning, #f59e0b); font-weight: 700;">{{ number_format($vehicle->price, 0, '.', ' ') }} USD</span>
                </p>
                <p style="margin: 0; color: var(--text-muted, #94a3b8);">
                    <strong style="color: var(--text-main, #cbd5e1);">Prix location :</strong> 
                    <span style="color: var(--brand-primary, #3b82f6); font-weight: 700;">{{ number_format($vehicle->loan_price, 0, '.', ' ') }} USD</span><span style="font-size: 12px;">/j</span>
                </p>
            </div>

            <span class="status-badge {{ $vehicle->status ? 'success' : 'danger' }}">
                <i class="bi {{ $vehicle->status ? 'bi-check-circle' : 'bi-slash-circle' }}"></i>
                {{ $vehicle->status ? 'Disponible' : 'Indisponible' }}
            </span>
        </div>

    </div>
</div>

{{-- ================= HISTORIQUE DES LOCATIONS ================= --}}
<div class="card-panel" style="margin-top: 30px; background: var(--panel-bg, #1e293b); padding: 24px; border-radius: 14px; border: 1px solid var(--panel-border, #334155); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);">
    <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 16px 0; color: var(--text-main, #fff); display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-box-seam" style="color: var(--brand-primary, #3b82f6);"></i> Historique des Locations
    </h3>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Durée</th>
                    <th>Montant Total</th>
                    <th>Statut</th>
                    <th>Date d'enregistrement</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicle->loans as $loan)
                <tr>
                    <td style="color: var(--text-muted, #94a3b8);">#{{ $loan->id }}</td>
                    <td style="font-weight: 600; color: var(--text-main, #fff);">{{ $loan->user->nom ?? 'N/A' }}</td>
                    <td style="color: var(--text-main, #cbd5e1);">{{ $loan->duration_days }} jours</td>
                    <td style="font-weight: 700; color: var(--brand-primary, #3b82f6);">{{ number_format($loan->total_amount, 0, '.', ' ') }} USD</td>
                    <td>
                        <span class="status-badge {{ $loan->status == 'Validé' || $loan->status == 'Terminé' ? 'success' : 'danger' }}" style="font-size: 12px; padding: 4px 10px;">
                            {{ $loan->status }}
                        </span>
                    </td>
                    <td style="color: var(--text-muted, #94a3b8);">{{ $loan->created_at }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="100%" style="text-align: center; color: var(--text-muted, #94a3b8); padding: 32px;">
                        <i class="bi bi-inbox" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                        Aucune location enregistrée pour ce véhicule.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ================= HISTORIQUE DES TRANSACTIONS ================= --}}
<div class="card-panel" style="margin-top: 30px; background: var(--panel-bg, #1e293b); padding: 24px; border-radius: 14px; border: 1px solid var(--panel-border, #334155); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);">
    <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 16px 0; color: var(--text-main, #fff); display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-cash-coin" style="color: var(--brand-warning, #f59e0b);"></i> Historique des Transactions
    </h3>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Montant Transigé</th>
                    <th>Date de transaction</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicle->transactions as $t)
                <tr>
                    <td style="color: var(--text-muted, #94a3b8);">#{{ $t->id }}</td>
                    <td style="font-weight: 600; color: var(--text-main, #fff);">{{ $t->user->nom ?? 'N/A' }}</td>
                    <td style="font-weight: 700; color: var(--brand-warning, #f59e0b);">{{ number_format($t->montant, 0, '.', ' ') }} USD</td>
                    <td style="color: var(--text-muted, #94a3b8);">{{ $t->created_at }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="100%" style="text-align: center; color: var(--text-muted, #94a3b8); padding: 32px;">
                        <i class="bi bi-credit-card-mute" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                        Aucune transaction enregistrée pour ce véhicule.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- FEUILLE DE STYLE LOCALE POUR LA COHÉRENCE VISUELLE --}}
<style>
    .table-container {
        width: 100%;
        overflow-x: auto;
        background: var(--panel-hover, #0f172a);
        border-radius: 10px;
        border: 1px solid var(--panel-border, #334155);
        margin-top: 12px;
    }
    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }
    .custom-table th {
        background: var(--panel-hover, #0f172a);
        color: var(--text-muted, #94a3b8);
        padding: 14px 16px;
        font-weight: 600;
        border-bottom: 1px solid var(--panel-border, #334155);
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
    }
    .custom-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--panel-border, #334155);
        vertical-align: middle;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 9999px;
        font-size: 13px;
        font-weight: 500;
    }
    .status-badge.success { background: rgba(34, 197, 94, 0.15); color: #4ade80; }
    .status-badge.danger { background: rgba(239, 68, 68, 0.15); color: #f87171; }
</style>

@endsection