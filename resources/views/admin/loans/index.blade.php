@extends('admin.layout')

@section('title', 'Gestion Locations - AutoGestion Admin')
@section('page_title', 'Gestion des Locations')
@section('page_icon') <i class="bi bi-calendar-check me-2"></i> @endsection

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <p class="intro" style="color: var(--text-muted, #94a3b8); font-size: 15px; margin: 0; max-width: 800px; line-height: 1.6;">
            Gérer toutes les demandes de location de véhicules, y compris l'approbation, le suivi des statuts en temps réel et l'ajout direct en local.
        </p>
        
        {{-- Bouton pou afiche/kache fòm nan --}}
        <button type="button" id="toggleFormBtn" class="btn-custom success" style="padding: 10px 20px; font-weight: 600;" onclick="toggleAddForm()">
            <i class="bi bi-plus-circle-fill"></i> Créer une Location
        </button>
    </div>

    {{-- MESSAGE DE SUCCÈS OU ERREUR --}}
    @if(session('success'))
        <div class="alert alert-success bg-success-subtle text-success border-0 mb-4" style="border-radius: 8px; padding: 12px 16px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger bg-danger-subtle text-danger border-0 mb-4" style="border-radius: 8px; padding: 12px 16px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- =================================================================== --}}
    {{-- === FORMULAIRE D'AJOUT DIRECT (INTEGRÉ DANS LA PAGE, CACHÉ PAR DEFAUT) === --}}
    {{-- =================================================================== --}}
    <div id="addLoanFormContainer" style="display: none; background: var(--panel-bg, #1e293b); border: 1px solid var(--panel-border, #334155); border-radius: 14px; padding: 24px; margin-bottom: 30px; animation: fadeIn 0.3s ease-in-out;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--panel-border, #334155); padding-bottom: 12px;">
            <h5 style="color: var(--text-main, #fff); font-weight: 700; margin: 0;">
                <i class="bi bi-plus-circle me-2 text-success"></i> Nouvelle Location (Création Directe en Local)
            </h5>
            <button type="button" class="btn-action-icon" onclick="toggleAddForm()" title="Fermer le formulaire">
                <i class="bi bi-x-lg" style="color: var(--text-muted, #94a3b8);"></i>
            </button>
        </div>

        {{-- 🔥 Ranje pou l itilize admin.loans.store fòmèlman piske rout ou a se admin --}}
        <form method="POST" action="{{ route('admin.loans.store') }}">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px;">
                
                {{-- Sélection du Client --}}
                <div>
                    <label class="form-label" style="color: var(--text-muted, #94a3b8); font-weight: 500; font-size: 14px; margin-bottom: 8px; display: block;">Sélectionner le Client</label>
                    <select name="user_id" class="form-select custom-input" style="width: 100%;" required>
                        <option value="" disabled selected>Choisir un client...</option>
                        @foreach(\App\Models\User::where('role', '!=', 'admin')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->nom ?? $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sélection du Véhicule --}}
                <div>
                    <label class="form-label" style="color: var(--text-muted, #94a3b8); font-weight: 500; font-size: 14px; margin-bottom: 8px; display: block;">Sélectionner le Véhicule</label>
                    <select name="vehicle_id" class="form-select custom-input" style="width: 100%;" required>
                        <option value="" disabled selected>Choisir un véhicule disponible...</option>
                        @foreach(\App\Models\Vehicle::where('quantity', '>', 0)->where('status', 1)->get() as $vehicle)
                            <option value="{{ $vehicle->id }}">
                                {{ $vehicle->brand }} {{ $vehicle->model }} — {{ number_format($vehicle->loan_price, 0) }} USD/jour (Stock: {{ $vehicle->quantity }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Durée de la location --}}
                <div>
                    <label class="form-label" style="color: var(--text-muted, #94a3b8); font-weight: 500; font-size: 14px; margin-bottom: 8px; display: block;">Durée de la location (en jours)</label>
                    <input type="number" name="duration_days" class="form-control custom-input" style="width: 100%;" min="1" value="5" required>
                </div>

            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--panel-border, #334155); padding-top: 16px;">
                <button type="button" class="btn-custom danger" style="padding: 8px 16px;" onclick="toggleAddForm()">Annuler</button>
                <button type="submit" class="btn-custom success" style="padding: 8px 16px; font-weight: 600;">
                    <i class="bi bi-check-lg"></i> Enregistrer & Approuver la location
                </button>
            </div>
        </form>
    </div>

    {{-- TABLEAU DE GESTION APPLICATIF DES LOCATIONS --}}
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Véhicule</th>
                    <th>Durée</th>
                    <th>Date Début</th>
                    <th>Date Fin</th>
                    <th style="white-space: nowrap;">Montant</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $loan)
                <tr>
                    <td style="color: var(--text-muted, #94a3b8);">#{{ $loan->id }}</td>
                    <td style="font-weight: 600; color: var(--text-main, #fff); white-space: nowrap;">
                        {{ $loan->user->nom ?? $loan->user->name ?? 'Client inconnu' }}
                    </td>
                    <td style="color: var(--text-main, #cbd5e1); white-space: nowrap;">
                        {{ $loan->vehicle->brand ?? '' }} {{ $loan->vehicle->model ?? '' }}
                    </td>
                    <td style="color: var(--text-main, #cbd5e1); white-space: nowrap;">
                        {{ $loan->duration_days }} jours
                    </td>
                    <td style="color: var(--text-main, #cbd5e1); white-space: nowrap;">
                        {{ $loan->start_date ? \Carbon\Carbon::parse($loan->start_date)->format('d/m/Y') : 'Non démarrée' }}
                    </td>
                    <td style="color: var(--text-main, #cbd5e1); white-space: nowrap;">
                        {{ $loan->end_date ? \Carbon\Carbon::parse($loan->end_date)->format('d/m/Y') : 'Non démarrée' }}
                    </td>
                    <td style="font-weight: 700; color: var(--brand-primary, #3b82f6); white-space: nowrap;">
                        {{ number_format($loan->total_amount, 0, '.', ' ') }} USD
                    </td>
                    <td>
                        @if($loan->status == 'pending')
                            <span class="status-badge warning" style="white-space: nowrap;">
                                <i class="bi bi-hourglass-split"></i> En attente
                            </span>
                        @elseif($loan->status == 'approved')
                            <span class="status-badge success" style="white-space: nowrap;">
                                <i class="bi bi-check-circle-fill"></i> Approuvé
                            </span>
                        @elseif($loan->status == 'expired')
                            <span class="status-badge danger" style="white-space: nowrap;">
                                <i class="bi bi-exclamation-octagon-fill"></i> Expiré
                            </span>
                        @else
                            <span class="status-badge danger" style="white-space: nowrap;">
                                <i class="bi bi-x-circle-fill"></i> Refusé
                            </span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <a href="{{ route('admin.loans.show', $loan->id) }}" class="btn-action-icon" title="Voir tous les détails">
                                <i class="bi bi-eye" style="color: var(--brand-primary, #3b82f6);"></i>
                            </a>

                            @if($loan->status == 'pending')
                                <form method="POST" action="{{ route('admin.loans.update', $loan->id) }}" style="display: inline;">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn-action-icon" title="Approuver la demande">
                                        <i class="bi bi-check-lg" style="color: var(--brand-success, #22c55e);"></i>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.loans.update', $loan->id) }}" style="display: inline;">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn-action-icon" title="Refuser la demande">
                                        <i class="bi bi-ban" style="color: var(--text-muted, #94a3b8);"></i>
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.loans.destroy', $loan->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette location ?');" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-custom danger" style="padding: 6px 12px; font-size: 13px; font-weight: 600;">
                                    <i class="bi bi-trash-fill"></i> 
                                </button>
                            </form>



                            @if($loan->status == 'pending_renewal')
@if($loan->status == 'pending_renewal')
    <div style="background: #fef3c7; padding: 5px; border-radius: 6px; margin-bottom: 5px; font-size: 12px; color: #92400e;">
        <strong>Demande:</strong> {{ $loan->renewal_requested_days }} jours
    </div>
<form method="POST" action="{{ route('admin.loans.approve-renewal', $loan->id) }}">
    @csrf
    <div class="renewal-form-container">
        <input type="number" 
               name="additional_days" 
               value="{{ $loan->renewal_requested_days }}" 
               class="form-control" 
               style="width: 70px;" 
               required>
        
        <button type="submit" class="btn btn-success btn-sm">Approuver</button>
   
    </div>
    <p class="mt-2 mb-0" style="font-size: 13px; color: #4a5568;">
        Nouveau total: 
        <strong style="color: #2d3748;">
            {{ number_format($loan->vehicle->loan_price * ($loan->duration_days + $loan->renewal_requested_days), 2) }} USD
        </strong>
    </p>
</form>
@endif
@endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- KÒD JAVASCRIPT POU BASKILE FÒM NAN --}}
    <script>
        function toggleAddForm() {
            var formContainer = document.getElementById('addLoanFormContainer');
            var btn = document.getElementById('toggleFormBtn');
            
            if (formContainer.style.display === 'none') {
                formContainer.style.display = 'block';
                btn.innerHTML = '<i class="bi bi-dash-circle-fill"></i> Fermer le formulaire';
                btn.className = 'btn-custom danger';
            } else {
                formContainer.style.display = 'none';
                btn.innerHTML = '<i class="bi bi-plus-circle-fill"></i> Créer une Location';
                btn.className = 'btn-custom success';
            }
        }
    </script>

    {{-- STRUCTURE DE STYLES COMPATIBLE DARK MODE --}}
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .table-container {
            width: 100%;
            overflow-x: auto;
            background: var(--panel-bg, #1e293b);
            border-radius: 14px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);
            border: 1px solid var(--panel-border, #334155);
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
            padding: 16px;
            font-weight: 600;
            border-bottom: 1px solid var(--panel-border, #334155);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        .custom-table td {
            padding: 16px;
            border-bottom: 1px solid var(--panel-border, #334155);
            vertical-align: middle;
        }
        .btn-action-icon {
            background: var(--panel-bg, #1e293b);
            border: 1px solid var(--panel-border, #334155);
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-action-icon:hover { background: var(--panel-hover, #0f172a); transform: translateY(-1px); }
        
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
        .status-badge.warning { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .status-badge.danger { background: rgba(239, 68, 68, 0.15); color: #f87171; }

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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .btn-custom.success { background: #22c55e; }
        .btn-custom.success:hover { background: #16a34a; }
        .btn-custom.danger { background: #ef4444; }
        .btn-custom.danger:hover { background: #dc2626; }

        .custom-input {
            background-color: var(--panel-hover, #0f172a) !important;
            border: 1px solid var(--panel-border, #334155) !important;
            color: var(--text-main, #fff) !important;
            border-radius: 8px !important;
            padding: 10px 12px;
            display: block;
        }
        .custom-input:focus {
            border-color: var(--brand-primary, #3b82f6) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
        }
        .form-select.custom-input option {
            background-color: #0f172a;
            color: #fff;
        }
        .renewal-form-container {
    display: flex;
    align-items: center; /* Aliyen yo vètikalman nan mitan */
    gap: 8px;           /* Mete yon ti espas ant input ak bouton */
}

.btn-success {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
}

.btn-success:hover {
    background-color: #218838;
}
    </style>
@endsection