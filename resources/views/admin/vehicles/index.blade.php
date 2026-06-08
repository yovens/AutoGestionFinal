@extends('admin.layout')

@section('title', 'Gestion Véhicules - AutoGestion Admin')
@section('page_title', 'Gestion des Véhicules')

@section('content')
    <p class="intro" style="color: var(--text-muted, #94a3b8); font-size: 15px; margin-bottom: 24px; max-width: 800px; line-height: 1.6;">
        Gérez l'inventaire de vos véhicules, y compris les détails techniques, les prix de vente et de location, ainsi que le statut de disponibilité en temps réel.
    </p>

    {{-- ZONE D'ACTION : BOUTON AJOUTER --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
        <button class="btn-action" style="background: var(--brand-success, #22c55e); color: #fff; border: none; font-weight: 600; display: flex; align-items: center; gap: 8px; padding: 12px 20px; border-radius: 10px; cursor: pointer;" 
                onclick="openModal('createVehicleModal')">
            <i class="bi bi-plus-circle-fill"></i> Ajouter Véhicule
        </button>
    </div>

    {{-- TABLEAU DE GESTION APPLICATIF --}}
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Visuel</th>
                    <th>Marque</th>
                    <th>Modèle</th>
                    <th>Plaque</th>
                    <th>Année</th>
                    <th style="white-space: nowrap;">Prix Vente</th>
                    <th style="white-space: nowrap;">Prix Location</th>
                    <th>Quantité</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicles as $v)
                <tr>
                    <td style="color: var(--text-muted, #94a3b8);">#{{ $v->id }}</td>
                    <td>
                        <div style="width: 60px; height: 40px; border-radius: 6px; overflow: hidden; border: 1px solid var(--panel-border, #334155); background: var(--panel-hover, #1e293b); flex-shrink: 0;">
                            <img src="/storage/{{ $v->image }}" style="width: 100%; height: 100%; object-fit: cover;" alt="image véhicule">
                        </div>
                    </td>
                    <td style="font-weight: 600; color: var(--text-main, #fff); white-space: nowrap;">{{ $v->brand }}</td>
                    <td style="white-space: nowrap; color: var(--text-main, #cbd5e1);">{{ $v->model }}</td>
                    <td>
                        <code style="background: var(--panel-hover, #1e293b); padding: 4px 8px; border-radius: 4px; font-size: 12px; border: 1px solid var(--panel-border, #334155); color: var(--text-main, #fff); white-space: nowrap;">
                            {{ $v->plate }}
                        </code>
                    </td>
                    <td style="color: var(--text-main, #cbd5e1);">{{ $v->year }}</td>
                    <td style="font-weight: 700; color: var(--brand-warning, #f59e0b); white-space: nowrap;">
                        {{ number_format($v->price, 0, '.', ' ') }} USD
                    </td>
                    <td style="font-weight: 700; color: var(--brand-primary, #3b82f6); white-space: nowrap;">
                        {{ number_format($v->loan_price, 0, '.', ' ') }} USD<span style="font-size: 11px; font-weight: 400; color: var(--text-muted, #94a3b8);">/j</span>
                    </td>
                    <td style="font-weight: 600; color: var(--text-main, #fff); text-align: center;">
                        {{ $v->quantity ?? 0 }}
                    </td>
                    <td>
                        <span class="status-badge {{ $v->status == 1 ? 'success' : 'danger' }}" style="white-space: nowrap;">
                            <i class="bi {{ $v->status == 1 ? 'bi-check-circle' : 'bi-slash-circle' }}"></i>
                            {{ $v->status == 1 ? 'Disponible' : 'Indisponible' }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('admin.vehicles.show', $v->id) }}" class="btn-action-icon" title="Voir détails">
                                <i class="bi bi-eye"></i>
                            </a>
                            
                            <button class="btn-action-icon" onclick="openModal('editVehicleModal{{$v->id}}')" title="Modifier">
                                <i class="bi bi-pencil" style="color: var(--brand-warning, #f59e0b);"></i>
                            </button>
                            
                            <form method="POST" action="{{ route('admin.vehicles.destroy', $v->id) }}" style="display: inline;" onsubmit="return confirm('Supprimer définitivement ce véhicule ?');">
                                @csrf @method('DELETE')
                                <button class="btn-action-icon" type="submit" title="Supprimer" style="border-color: rgba(239, 68, 68, 0.2); background: transparent;">
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

    {{-- ZONE DES MODALES DE MODIFICATION (HORS DU TABLEAU) --}}
    @foreach($vehicles as $v)
    <div id="editVehicleModal{{$v->id}}" class="native-modal">
        <div class="native-modal-content modal-lg">
            <div class="native-modal-header">
                <h3>Modifier le véhicule ({{ $v->plate }})</h3>
                <button type="button" class="close-modal-btn" onclick="closeModal('editVehicleModal{{$v->id}}')">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.vehicles.update', $v->id) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="native-modal-body grid-form">
                    <div>
                        <label class="form-label">Marque</label>
                        <input type="text" name="brand" value="{{ $v->brand }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Modèle</label>
                        <input type="text" name="model" value="{{ $v->model }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Plaque d'immatriculation</label>
                        <input type="text" name="plate" value="{{ $v->plate }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Année de fabrication</label>
                        <input type="number" name="year" value="{{ $v->year }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Prix de Vente (USD)</label>
                        <input type="number" name="price" value="{{ $v->price }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Prix de Location (USD / Jour)</label>
                        <input type="number" name="loan_price" value="{{ $v->loan_price }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Quantité en stock</label>
                        <input type="number" name="quantity" value="{{ $v->quantity ?? 1 }}" min="0" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Statut opérationnel</label>
                        <select name="status" class="form-select">
                            <option value="1" {{$v->status == 1 ? 'selected' : ''}}>Disponible</option>
                            <option value="0" {{$v->status == 0 ? 'selected' : ''}}>Indisponible</option>
                        </select>
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="form-label" style="color: var(--text-muted, #94a3b8);">Mettre à jour l'image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="native-modal-footer">
                    <button type="button" class="btn-action-secondary" onclick="closeModal('editVehicleModal{{$v->id}}')">Annuler</button>
                    <button type="submit" class="btn-action-primary" style="background: var(--brand-success, #22c55e);">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    {{-- MODAL AJOUTER VÉHICULE NATIVE --}}
    <div id="createVehicleModal" class="native-modal">
        <div class="native-modal-content modal-lg">
            <div class="native-modal-header">
                <h3>Ajouter un nouveau véhicule</h3>
                <button type="button" class="close-modal-btn" onclick="closeModal('createVehicleModal')">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.vehicles.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="native-modal-body grid-form">
                    <div>
                        <label class="form-label">Marque</label>
                        <input type="text" name="brand" class="form-control" placeholder="Ex: BMW" required>
                    </div>
                    <div>
                        <label class="form-label">Modèle</label>
                        <input type="text" name="model" class="form-control" placeholder="Ex: Série 5" required>
                    </div>
                    <div>
                        <label class="form-label">Plaque d'immatriculation</label>
                        <input type="text" name="plate" class="form-control" placeholder="Ex: AA-123-AA" required>
                    </div>
                    <div>
                        <label class="form-label">Année de fabrication</label>
                        <input type="number" name="year" class="form-control" placeholder="Ex: 2024" required>
                    </div>
                    <div>
                        <label class="form-label">Prix de Vente (USD)</label>
                        <input type="number" name="price" class="form-control" placeholder="Ex: 45000" required>
                    </div>
                    <div>
                        <label class="form-label">Prix de Location (USD / Jour)</label>
                        <input type="number" name="loan_price" class="form-control" placeholder="Ex: 85" required>
                    </div>
                    <div>
                        <label class="form-label">Quantité initiale</label>
                        <input type="number" name="quantity" class="form-control" value="1" min="0" required>
                    </div>
                    <div>
                        <label class="form-label">Statut initial</label>
                        <select name="status" class="form-select">
                            <option value="1">Disponible</option>
                            <option value="0">Indisponible</option>
                        </select>
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="form-label">Photographie du véhicule</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                </div>
                <div class="native-modal-footer">
                    <button type="button" class="btn-action-secondary" onclick="closeModal('createVehicleModal')">Annuler</button>
                    <button type="submit" class="btn-action-primary" style="background: var(--brand-primary, #3b82f6);">Créer le véhicule</button>
                </div>
            </form>
        </div>
    </div>

    {{-- STYLES CONVERTIS ET AMÉLIORÉS POUR LE DARK MODE --}}
    <style>
        .table-container {
    width: 100%;
    overflow-x: auto;
}

.custom-table {
    min-width: 1100px; /* ou 100% + plus si nécessaire */
    width: 100%;
    table-layout: auto;
}
        .table-container {
            width: 100%;
            overflow-x: auto; /* Permet le scroll horizontal */
            background: var(--panel-bg, #1e293b);
            border-radius: 14px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);
            border: 1px solid var(--panel-border, #334155);
        }
        .custom-table {
            width: 100%;
            min-width: 1050px; /* 👀 FORCE LE TABLEAU À GARDER UNE LARGEUR MINIMUM POUR ÉVITER QU'IL S'ÉCRASE */
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
        .status-badge.danger { background: rgba(239, 68, 68, 0.15); color: #f87171; }

        .form-control, .form-select {
            width: 100%; padding: 10px 12px; border: 1px solid var(--panel-border, #334155); 
            background: var(--panel-hover, #0f172a); color: var(--text-main, #fff); border-radius: 8px; font-size: 14px; box-sizing: border-box;
        }
        .form-control:focus, .form-select:focus { outline: none; border-color: var(--brand-primary, #3b82f6); }

        .native-modal {
            display: none; 
            opacity: 0;
            visibility: hidden;
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); z-index: 9999;
            align-items: center; justify-content: center;
            transition: opacity 0.2s ease, visibility 0.2s;
        }
        .native-modal.active { 
            display: flex; 
            opacity: 1;
            visibility: visible;
        }
        .native-modal-content {
            background: var(--panel-bg, #1e293b); border-radius: 16px; width: 90%; max-width: 480px;
            border: 1px solid var(--panel-border, #334155);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.4); transform: scale(0.95); transition: transform 0.2s ease; overflow: hidden;
        }
        .native-modal.active .native-modal-content { transform: scale(1); }
        .native-modal-content.modal-lg { max-width: 640px; }
        
        .native-modal-header { padding: 20px 24px; border-bottom: 1px solid var(--panel-border, #334155); display: flex; justify-content: space-between; align-items: center; }
        .native-modal-header h3 { font-size: 18px; font-weight: 700; margin: 0; color: var(--text-main, #fff); }
        .close-modal-btn { background: transparent; border: none; color: var(--text-muted, #94a3b8); font-size: 24px; cursor: pointer; }
        .close-modal-btn:hover { color: var(--text-main, #fff); }
        .native-modal-body { padding: 24px; }
        .native-modal-footer { padding: 16px 24px; background: var(--panel-hover, #0f172a); border-top: 1px solid var(--panel-border, #334155); display: flex; justify-content: flex-end; gap: 12px; }
        .grid-form { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .form-label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500; color: var(--text-main, #cbd5e1); }
        
        .btn-action-primary, .btn-action-secondary {
            padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; border: none; color: #fff;
        }
        .btn-action-secondary { background: var(--panel-hover, #0f172a); color: var(--text-main, #fff); border: 1px solid var(--panel-border, #334155); }
        @media (max-width: 640px) { .grid-form { grid-template-columns: 1fr; } }
    </style>
@endsection

@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if(modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if(modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    window.addEventListener('click', (e) => {
        if(e.target.classList.contains('native-modal')) {
            closeModal(e.target.id);
        }
    });
</script>
@endpush