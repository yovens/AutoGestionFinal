@extends('admin.layout')

@section('title', 'Détails Transaction #' . $transaction->id . ' - AutoGestion Admin')
@section('page_title')
    <a href="{{ route('admin.transactions.index') }}" class="btn-action-icon me-3" style="text-decoration: none;">
        <i class="bi bi-arrow-left" style="color: var(--text-main, #fff);"></i>
    </a>
    Détails de la Transaction #{{ $transaction->id }}
@endsection

@section('content')
    <div style="max-width: 700px; margin: 0 auto; background: var(--panel-bg, #1e293b); border: 1px solid var(--panel-border, #334155); border-radius: 16px; padding: 32px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);">
        
        {{-- Header invoice style --}}
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--panel-border, #334155); padding-bottom: 20px; margin-bottom: 24px;">
            <div>
                <h4 style="color: var(--text-main, #fff); font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-receipt text-primary"></i> REÇU DE TRANSACTION
                </h4>
                <p style="color: var(--text-muted, #94a3b8); font-size: 13px; margin: 4px 0 0 0;">AutoGestion S.A. System</p>
            </div>
            <div>
                <span class="status-badge {{ $transaction->type == 'vente' ? 'primary' : 'info' }}" style="font-size: 14px; padding: 8px 16px;">
                    <i class="bi {{ $transaction->type == 'vente' ? 'bi-cart-check' : 'bi-calendar-range' }}"></i>
                    {{ ucfirst($transaction->type) }}
                </span>
            </div>
        </div>

        {{-- Kò enfòmasyon yo --}}
        <div style="display: flex; flex-direction: column; gap: 18px; margin-bottom: 32px;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #334155; padding-bottom: 12px;">
                <span style="color: var(--text-muted, #94a3b8); font-weight: 500;">Identifiant unique :</span>
                <strong style="color: #60a5fa; font-family: monospace; font-size: 15px;">#{{ $transaction->id }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #334155; padding-bottom: 12px;">
                <span style="color: var(--text-muted, #94a3b8); font-weight: 500;">Nom du Client :</span>
                <strong style="color: var(--text-main, #fff);">{{ $transaction->user->nom ?? $transaction->user->name ?? 'Inconnu' }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #334155; padding-bottom: 12px;">
                <span style="color: var(--text-muted, #94a3b8); font-weight: 500;">Adresse Email :</span>
                <span style="color: #cbd5e1;">{{ $transaction->user->email ?? 'N/A' }}</span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #334155; padding-bottom: 12px;">
                <span style="color: var(--text-muted, #94a3b8); font-weight: 500;">Véhicule concerné :</span>
                <strong style="color: #e2e8f0;">{{ $transaction->vehicle->brand ?? '' }} {{ $transaction->vehicle->model ?? 'N/A' }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #334155; padding-bottom: 12px;">
                <span style="color: var(--text-muted, #94a3b8); font-weight: 500;">Date de l'opération :</span>
                <span style="color: #cbd5e1;"><i class="bi bi-calendar3 me-1"></i> {{ $transaction->created_at->format('d/m/Y à H:i:s') }}</span>
            </div>

            {{-- Gwo blòk Montant anba nèt --}}
            <div style="background: var(--panel-hover, #0f172a); padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-top: 10px; border: 1px solid var(--panel-border, #334155);">
                <span style="color: var(--text-main, #fff); font-weight: 600; font-size: 16px;">Montant Net Payé :</span>
                <strong style="color: var(--brand-success, #22c55e); font-size: 24px; font-weight: 800;">
                    {{ number_format($transaction->amount, 0, '.', ' ') }} USD
                </strong>
            </div>
        </div>

        {{-- Bouton aksyon yo --}}
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--panel-border, #334155); padding-top: 20px;">
            <a href="{{ route('admin.transactions.index') }}" class="btn-custom danger" style="padding: 10px 20px; text-decoration: none;">
                <i class="bi bi-arrow-left-circle"></i> Retour à la liste
            </a>

            <button type="button" class="btn-custom success" style="padding: 10px 20px;" onclick="window.print();">
                <i class="bi bi-printer"></i> Imprimer le reçu
            </button>
        </div>

    </div>

    <style>
        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 9999px; font-size: 13px; font-weight: 500; }
        .status-badge.primary { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        .status-badge.info { background: rgba(6, 182, 212, 0.15); color: #22d3ee; }
        
        .btn-custom { border: none; padding: 11px 14px; border-radius: 8px; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; color: #ffffff; justify-content: center; }
        .btn-custom:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); }
        .btn-custom.success { background: #22c55e; }
        .btn-custom.danger { background: #ef4444; }

        .btn-action-icon { background: var(--panel-bg, #1e293b); border: 1px solid var(--panel-border, #334155); padding: 8px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .btn-action-icon:hover { background: var(--panel-hover, #0f172a); }
    </style>
@endsection