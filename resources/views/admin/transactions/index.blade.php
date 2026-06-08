@extends('admin.layout')

@section('title', 'Gestion Transactions - AutoGestion Admin')
@section('page_title', 'Gestion des Transactions')
@section('page_icon') <i class="bi bi-receipt me-2"></i> @endsection

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <p class="intro" style="color: var(--text-muted, #94a3b8); font-size: 15px; margin: 0; max-width: 800px; line-height: 1.6;">
            Consultez l'historique de toutes les opérations financières, y compris les ventes et les paiements de location, ou ajoutez-en une manuellement.
        </p>

        <button type="button" id="toggleFormBtn" class="btn-custom success" style="padding: 10px 20px; font-weight: 600;" onclick="toggleAddForm()">
            <i class="bi bi-plus-circle-fill"></i> Créer une Transaction
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success-subtle text-success border-0 mb-4" style="border-radius: 8px; padding: 12px 16px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- FORMULAIRE D'AJOUT DIRECT --}}
    <div id="addTransactionFormContainer" style="display: none; background: var(--panel-bg, #1e293b); border: 1px solid var(--panel-border, #334155); border-radius: 14px; padding: 24px; margin-bottom: 30px; animation: fadeIn 0.3s ease-in-out;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--panel-border, #334155); padding-bottom: 12px;">
            <h5 style="color: var(--text-main, #fff); font-weight: 700; margin: 0;">
                <i class="bi bi-plus-circle me-2 text-success"></i> Nouvelle Transaction Enregistrée
            </h5>
            <button type="button" class="btn-action-icon" onclick="toggleAddForm()">
                <i class="bi bi-x-lg" style="color: var(--text-muted, #94a3b8);"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.transactions.store') }}">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px;">
                <div>
                    <label class="form-label" style="color: var(--text-muted, #94a3b8); font-weight: 500; font-size: 14px; margin-bottom: 8px; display: block;">Sélectionner le Client</label>
                    <select name="user_id" class="form-select custom-input" style="width: 100%;" required>
                        <option value="" disabled selected>Choisir un client...</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}">{{ $user->nom ?? $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label" style="color: var(--text-muted, #94a3b8); font-weight: 500; font-size: 14px; margin-bottom: 8px; display: block;">Véhicule Associé</label>
                    <select name="vehicle_id" class="form-select custom-input" style="width: 100%;" required>
                        <option value="" disabled selected>Choisir un véhicule...</option>
                        @foreach(\App\Models\Vehicle::all() as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->brand }} {{ $vehicle->model }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label" style="color: var(--text-muted, #94a3b8); font-weight: 500; font-size: 14px; margin-bottom: 8px; display: block;">Montant (USD)</label>
                    <input type="number" name="amount" class="form-control custom-input" style="width: 100%;" min="1" placeholder="Ex: 1500" required>
                </div>

                <div>
                    <label class="form-label" style="color: var(--text-muted, #94a3b8); font-weight: 500; font-size: 14px; margin-bottom: 8px; display: block;">Type d'Opération</label>
                    <select name="type" class="form-select custom-input" style="width: 100%;" required>
                        <option value="location">Location</option>
                        <option value="vente">Vente</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--panel-border, #334155); padding-top: 16px;">
                <button type="button" class="btn-custom danger" style="padding: 8px 16px;" onclick="toggleAddForm()">Annuler</button>
                <button type="submit" class="btn-custom success" style="padding: 8px 16px; font-weight: 600;">
                    <i class="bi bi-check-lg"></i> Enregistrer la Transaction
                </button>
            </div>
        </form>
    </div>

    {{-- TABLEAU --}}
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Véhicule</th>
                    <th>Montant</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $t)
                <tr>
                    <td style="color: var(--text-muted, #94a3b8);">#{{ $t->id }}</td>
                    <td style="font-weight: 600; color: var(--text-main, #fff); white-space: nowrap;">
                        {{ $t->user->nom ?? $t->user->name ?? 'Inconnu' }}
                    </td>
                    <td style="color: var(--text-main, #cbd5e1); white-space: nowrap;">
                        {{ $t->vehicle->brand ?? '' }} {{ $t->vehicle->model ?? '' }}
                    </td>
                    <td style="font-weight: 700; color: var(--brand-success, #22c55e); white-space: nowrap;">
                        {{ number_format($t->amount, 0, '.', ' ') }} USD
                    </td>
                    <td>
                        <span class="status-badge {{ $t->type == 'vente' ? 'primary' : 'info' }}">
                            <i class="bi {{ $t->type == 'vente' ? 'bi-cart-check' : 'bi-calendar-range' }}"></i>
                            {{ ucfirst($t->type) }}
                        </span>
                    </td>
                    <td style="color: var(--text-muted, #94a3b8); white-space: nowrap;">
                        {{ $t->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            {{-- Redireksyon vè nouvo paj SHOW a --}}
                            <a href="{{ route('admin.transactions.show', $t->id) }}" class="btn-action-icon" title="Voir les détails">
                                <i class="bi bi-eye" style="color: var(--brand-primary, #3b82f6);"></i>
                            </a>

                            <form method="POST" action="{{ route('admin.transactions.destroy', $t->id) }}" style="display: inline;" onsubmit="return confirm('Confirmer la suppression ?');">
                                @csrf @method('DELETE')
                                <button class="btn-action-icon" type="submit" title="Supprimer la transaction" style="border-color: rgba(239, 68, 68, 0.2);">
                                    <i class="bi bi-trash" style="color: var(--brand-danger, #ef4444);"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        function toggleAddForm() {
            var formContainer = document.getElementById('addTransactionFormContainer');
            var btn = document.getElementById('toggleFormBtn');
            if (formContainer.style.display === 'none') {
                formContainer.style.display = 'block';
                btn.innerHTML = '<i class="bi bi-dash-circle-fill"></i> Fermer le formulaire';
                btn.className = 'btn-custom danger';
            } else {
                formContainer.style.display = 'none';
                btn.innerHTML = '<i class="bi bi-plus-circle-fill"></i> Créer une Transaction';
                btn.className = 'btn-custom success';
            }
        }
    </script>

    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .table-container { width: 100%; overflow-x: auto; background: var(--panel-bg, #1e293b); border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2); border: 1px solid var(--panel-border, #334155); }
        .custom-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        .custom-table th { background: var(--panel-hover, #0f172a); color: var(--text-muted, #94a3b8); padding: 16px; font-weight: 600; border-bottom: 1px solid var(--panel-border, #334155); text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        .custom-table td { padding: 16px; border-bottom: 1px solid var(--panel-border, #334155); vertical-align: middle; }
        .btn-action-icon { background: var(--panel-bg, #1e293b); border: 1px solid var(--panel-border, #334155); padding: 8px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; text-decoration: none; }
        .btn-action-icon:hover { background: var(--panel-hover, #0f172a); transform: translateY(-1px); }
        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 9999px; font-size: 13px; font-weight: 500; white-space: nowrap; }
        .status-badge.primary { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        .status-badge.info { background: rgba(6, 182, 212, 0.15); color: #22d3ee; }
        .btn-custom { border: none; padding: 11px 14px; border-radius: 8px; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; color: #ffffff; justify-content: center; }
        .btn-custom:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); }
        .btn-custom.success { background: #22c55e; }
        .btn-custom.danger { background: #ef4444; }
        .custom-input { background-color: var(--panel-hover, #0f172a) !important; border: 1px solid var(--panel-border, #334155) !important; color: var(--text-main, #fff) !important; border-radius: 8px !important; padding: 10px 12px; display: block; }
    </style>
@endsection