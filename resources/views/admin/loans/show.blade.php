@extends('admin.layout')

@section('title', 'Détails Location #' . $loan->id)
@section('page_title', 'Détails de la Location #' . $loan->id)
@section('page_icon') <i class="bi bi-info-circle me-2" style="color: var(--brand-primary, #3b82f6);"></i> @endsection

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.loans.index') }}" style="color: #cbd5e1; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; font-weight: 500;">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
</div>

{{-- Alerte si la date est dépassée --}}
@if($loan->isExpired())
    <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fca5a5; padding: 16px; border-radius: 10px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
        <i class="bi bi-exclamation-triangle-fill" style="font-size: 20px; color: #ef4444;"></i>
        <div>
            <strong style="display: block; color: #fff; font-size: 15px;">Attention : Durée Échue !</strong>
            Le véhicule devait être retourné le {{ $loan->end_date ? $loan->end_date->format('d/m/Y') : 'N/A' }}. Veuillez contacter le client ou renouveler le contrat.
        </div>
    </div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px; margin-bottom: 24px;">
    
    {{-- Bloc 1 : Infos Locataires & Statut --}}
    <div class="info-card">
        <h3><i class="bi bi-person me-2" style="color: #3b82f6;"></i> Informations Client & Statut</h3>
        <hr style="border-color: var(--panel-border, #334155); margin: 15px 0;">
        
        <p><strong class="text-label">Nom du Client :</strong> <span class="text-value-white">{{ $loan->user->nom }}</span></p>
        <p><strong class="text-label">Email :</strong> <span class="text-value-white">{{ $loan->user->email }}</span></p>
        <p><strong class="text-label">Date de demande :</strong> <span class="text-value-white">{{ $loan->created_at->format('d/m/Y H:i') }}</span></p>
        
        <p style="margin-top: 20px;">
            <strong class="text-label">Statut Actuel :</strong>
            @if($loan->status == 'pending')
                <span class="status-badge warning"><i class="bi bi-hourglass-split"></i> En attente</span>
            @elseif($loan->status == 'approved')
                <span class="status-badge success"><i class="bi bi-check-circle-fill"></i> Actif / Approuvé</span>
            @elseif($loan->status == 'expired')
                <span class="status-badge danger"><i class="bi bi-exclamation-octagon-fill"></i> Expiré (Temps écoulé)</span>
            @else
                <span class="status-badge danger"><i class="bi bi-x-circle-fill"></i> Refusé</span>
            @endif
        </p>

        {{-- Actions Admin directes sur le statut --}}
        @if($loan->status == 'pending')
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <form method="POST" action="{{ route('admin.loans.update', $loan->id) }}" style="flex: 1;">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="btn-custom success" style="width: 100%; font-weight: 600;"><i class="bi bi-check-lg"></i> Approuver</button>
                </form>
                <form method="POST" action="{{ route('admin.loans.update', $loan->id) }}" style="flex: 1;">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="btn-custom danger" style="width: 100%; font-weight: 600;"><i class="bi bi-ban"></i> Refuser</button>
                </form>
            </div>
        @endif
    </div>

    {{-- Bloc 2 : Infos Véhicule & Période --}}
    <div class="info-card">
        <h3><i class="bi bi-car-front me-2" style="color: #3b82f6;"></i> Véhicule & Période</h3>
        <hr style="border-color: var(--panel-border, #334155); margin: 15px 0;">
        
        <p><strong class="text-label">Modèle :</strong> <span class="text-value-white" style="font-weight: 600; font-size: 15px;">{{ $loan->vehicle->brand }} {{ $loan->vehicle->model }}</span></p>
        <p><strong class="text-label">Plaque d'immatriculation :</strong> <code style="background: #0f172a; padding: 4px 8px; border-radius: 6px; color: #3b82f6; font-weight: 600;">{{ $loan->vehicle->plate }}</code></p>
        <p><strong class="text-label">Durée Initiale :</strong> <span class="text-value-white">{{ $loan->duration_days }} jours</span></p>
        
        <div style="background: #0f172a; padding: 14px; border-radius: 10px; margin-top: 15px; border: 1px solid var(--panel-border, #334155);">
            <p style="margin: 0 0 8px 0;"><strong class="text-label">Date Début :</strong> <span style="color: #4ade80; font-weight: 600;">{{ $loan->start_date ? $loan->start_date->format('d/m/Y') : 'Non démarrée' }}</span></p>
            <p style="margin: 0 0 8px 0;"><strong class="text-label">Date Fin :</strong> <span style="color: #f87171; font-weight: 600;">{{ $loan->end_date ? $loan->end_date->format('d/m/Y') : 'Non démarrée' }}</span></p>
            @if($loan->status === 'approved')
                <p style="margin: 8px 0 0 0; font-size: 13px; color: #3b82f6; border-top: 1px solid #1e293b; padding-top: 8px;">
                    <i class="bi bi-clock-history"></i> Jours restants : <strong style="color: #3b82f6; font-size: 14px;">{{ $loan->daysLeft() }} jours</strong>
                </p>
            @endif
        </div>

        <p style="margin-top: 20px; font-size: 16px; color: #fff;">
            <strong>Montant Total :</strong> <span style="color: #3b82f6; font-size: 20px; font-weight: 700;">{{ number_format($loan->total_amount, 0, '.', ' ') }} USD</span>
        </p>
    </div>
</div>

{{-- Section de Renouvellement --}}
@if(in_array($loan->status, ['approved', 'expired']))
<div class="info-card" style="max-width: 500px;">
    <h3><i class="bi bi-arrow-repeat me-2" style="color: #fbbf24;"></i> Renouveler la location</h3>
    <p style="color: #e2e8f0; font-size: 13px; margin-bottom: 15px;">Ajoutez des jours supplémentaires à ce contrat de location.</p>
    
    <form method="POST" action="{{ route('admin.loans.renew', $loan->id) }}">
        @csrf
        <div style="display: flex; gap: 12px; align-items: center;">
            <div style="flex: 1;">
                <input type="number" name="additional_days" min="1" value="1" class="form-input" required>
            </div>
            <button type="submit" class="btn-custom warning" style="padding: 12px 20px; font-weight: 600;"><i class="bi bi-plus-circle"></i> Renouveler</button>
        </div>
    </form>
</div>
@endif

<style>
/* Kad enfòmasyon yo (Cards) */
.info-card {
    background: #1e293b;
    border: 1px solid #334155;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
}
.info-card h3 {
    margin: 0;
    color: #ffffff; /* Tit yo ap pwofondeman blanch */
    font-size: 17px;
    font-weight: 600;
}

/* Tèks labèl ak valè pou yo pa pal */
.text-label {
    color: #94a3b8; /* Gri klè pou tit jaden an */
    font-size: 14px;
}
.text-value-white {
    color: #ffffff !important; /* Blanch nèf pou done yo ka byen parèt */
    font-weight: 500;
}

/* Jaden pou antre chif yo (Input) */
.form-input {
    width: 100%;
    background: #0f172a;
    border: 1px solid #334155;
    color: #ffffff;
    padding: 11px;
    border-radius: 8px;
    outline: none;
    font-size: 15px;
    font-weight: 600;
    transition: border-color 0.2s;
}
.form-input:focus {
    border-color: #3b82f6;
}

/* Bouton Custom yo */
.btn-custom {
    border: none;
    padding: 11px 14px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
    color: #ffffff;
    justify-content: center;
}
.btn-custom:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.btn-custom.success { background: #22c55e; }
.btn-custom.success:hover { background: #16a34a; }

.btn-custom.danger { background: #ef4444; }
.btn-custom.danger:hover { background: #dc2626; }

.btn-custom.warning { background: #f59e0b; color: #000000; } /* Nwa sou jòn pou pi bon kontras */
.btn-custom.warning:hover { background: #d97706; }

/* Badges pou montre Estati yo ak koulè ki flash */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 9999px;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
}
.status-badge.success { background: rgba(34, 197, 94, 0.25); color: #4ade80; }
.status-badge.warning { background: rgba(245, 158, 11, 0.25); color: #fbbf24; }
.status-badge.danger { background: rgba(239, 68, 68, 0.25); color: #f87171; }
</style>
@endsection