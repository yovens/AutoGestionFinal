@extends('admin.layout')

@section('title', 'Gestion Utilisateurs')
@section('page_title', 'Gestion des Utilisateurs')

@section('content')
    <p class="intro">
        Sur cette page, vous pouvez ajouter, modifier, bloquer ou supprimer les comptes utilisateurs et gérer leurs rôles sur la plateforme AutoGestion.
    </p>

    {{-- ZONE D'ACTION : BOUTON AJOUTER --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
        <button class="btn-action" style="background: var(--brand-primary); color: #fff; border: none; font-weight: 600; display: flex; align-items: center; gap: 8px; padding: 12px 20px; border-radius: 10px;" 
                onclick="openModal('createUserModal')">
            <i class="bi bi-person-plus-fill"></i> Ajouter Utilisateur
        </button>
    </div>

    {{-- TABLEAU DE GESTION APPLICATIF --}}
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td class="text-muted">#{{ $u->id }}</td>
                    <td style="font-weight: 600;">{{ $u->nom }}</td>
                    <td>{{ $u->email }}</td>
                    <td>
                        <span class="status-badge {{ $u->role == 'ADMIN' ? 'danger' : 'success' }}" style="font-size: 11px; text-transform: uppercase;">
                            <i class="bi {{ $u->role == 'ADMIN' ? 'bi-shield-focused' : 'bi-person' }}"></i> {{ $u->role }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge {{ $u->status == 'ACTIVE' ? 'success' : 'danger' }}">
                            <i class="bi {{ $u->status == 'ACTIVE' ? 'bi-check-circle' : 'bi-slash-circle' }}"></i> {{ $u->status }}
                        </span>
                    </td>
                    <td>
                        
                        <div style="display: flex; gap: 8px;">
                            {{-- Bouton Éditer --}}
                            <button class="btn-action" onclick="openModal('editUserModal{{$u->id}}')" title="Modifier">
                                <i class="bi bi-pencil-square" style="color: var(--brand-warning);"></i>
                            </button>
                            {{-- Bouton Voir détails --}}
<a href="{{ route('admin.users.show', $u->id) }}"
   class="btn-action"
   title="Voir détails">
    <i class="bi bi-eye-fill" style="color:#3b82f6;"></i>
</a>
                            {{-- Formulaire Verrouiller / Déverrouiller --}}
                            <form method="POST" action="{{ route('admin.users.block', $u->id) }}" style="display: inline;">
                                @csrf
                                <button class="btn-action" type="submit" title="{{ $u->status == 'ACTIVE' ? 'Bloquer l\'utilisateur' : 'Débloquer l\'utilisateur' }}">
                                    <i class="bi {{ $u->status == 'ACTIVE' ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>
                                </button>
                            </form>
                            
                            {{-- Formulaire Supprimer --}}
                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" style="display: inline;" onsubmit="return confirm('Supprimer définitivement cet utilisateur ?');">
                                @csrf @method('DELETE')
                                <button class="btn-action" type="submit" title="Supprimer" style="border-color: rgba(239, 68, 68, 0.2);">
                                    <i class="bi bi-trash-fill" style="color: var(--brand-danger);"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- MODAL DE MODIFICATION NATIVE (EDIT) --}}
                <div id="editUserModal{{$u->id}}" class="native-modal">
                    <div class="native-modal-content">
                        <div class="native-modal-header">
                            <h3>Modifier l'utilisateur</h3>
                            <button type="button" class="close-modal-btn" onclick="closeModal('editUserModal{{$u->id}}')">&times;</button>
                        </div>
                        <form method="POST" action="{{ route('admin.users.update', $u->id) }}">
                            @csrf @method('PUT')
                            <div class="native-modal-body">
                                <label style="display:block; margin-bottom: 6px; font-size: 13px; color: var(--text-muted);">Nom complet</label>
                                <input type="text" name="nom" value="{{ $u->nom }}" class="form-control" style="margin-bottom: 16px;" required>
                                
                                <label style="display:block; margin-bottom: 6px; font-size: 13px; color: var(--text-muted);">Adresse Email</label>
                                <input type="email" name="email" value="{{ $u->email }}" class="form-control" style="margin-bottom: 16px;" required>
                                
                                <label style="display:block; margin-bottom: 6px; font-size: 13px; color: var(--text-muted);">Rôle d'accès</label>
                                <select name="role" class="form-select" style="margin-bottom: 16px;">
                                    <option value="CLIENT" {{ $u->role == 'CLIENT' ? 'selected' : '' }}>CLIENT</option>
                                    <option value="ADMIN" {{ $u->role == 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                                </select>
                                
                                <label style="display:block; margin-bottom: 6px; font-size: 13px; color: var(--text-muted);">Statut du compte</label>
                                <select name="status" class="form-select">
                                    <option value="ACTIVE" {{ $u->status == 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                                    <option value="BLOCKED" {{ $u->status == 'BLOCKED' ? 'selected' : '' }}>BLOCKED</option>
                                </select>
                            </div>
                            <div class="native-modal-footer">
                                <button type="button" class="btn-action" onclick="closeModal('editUserModal{{$u->id}}')">Annuler</button>
                                <button type="submit" class="btn-action" style="background: var(--brand-success); color: #fff; border: none; font-weight: 600;">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MODAL DE CRÉATION NATIVE (CREATE) --}}
    <div id="createUserModal" class="native-modal">
        <div class="native-modal-content">
            <div class="native-modal-header">
                <h3>Ajouter un nouvel utilisateur</h3>
                <button type="button" class="close-modal-btn" onclick="closeModal('createUserModal')">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="native-modal-body">
                    <label style="display:block; margin-bottom: 6px; font-size: 13px; color: var(--text-muted);">Nom complet</label>
                    <input type="text" name="nom" class="form-control" style="margin-bottom: 16px;" placeholder="Ex: John Doe" required>
                    
                    <label style="display:block; margin-bottom: 6px; font-size: 13px; color: var(--text-muted);">Adresse Email</label>
                    <input type="email" name="email" class="form-control" style="margin-bottom: 16px;" placeholder="john@example.com" required>
                    
                    <label style="display:block; margin-bottom: 6px; font-size: 13px; color: var(--text-muted);">Mot de passe</label>
                    <input type="password" name="password" class="form-control" style="margin-bottom: 16px;" placeholder="••••••••" required>
                    
                    <label style="display:block; margin-bottom: 6px; font-size: 13px; color: var(--text-muted);">Rôle système</label>
                    <select name="role" class="form-select">
                        <option value="CLIENT">CLIENT</option>
                        <option value="ADMIN">ADMIN</option>
                    </select>
                </div>
                <div class="native-modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('createUserModal')">Annuler</button>
                    <button type="submit" class="btn-action" style="background: var(--brand-primary); color: #fff; border: none; font-weight: 600;">Créer le compte</button>
                </div>
            </form>
        </div>
    </div>

    {{-- STYLE INTERNE UNIQUEMENT POUR LA STRUCTURATION DES MODALS NATIVES --}}
    <style>
        .native-modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .native-modal.active { display: flex; }
        .native-modal-content {
            background: var(--panel-bg);
            border: 1px solid var(--panel-border);
            border-radius: 16px;
            width: 100%;
            max-width: 480px;
            box-shadow: var(--shadow-ui);
            animation: modalFadeIn 0.25s ease;
            overflow: hidden;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .native-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--panel-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .native-modal-header h3 { font-size: 18px; font-weight: 700; }
        .close-modal-btn {
            background: transparent; border: none; color: var(--text-muted);
            font-size: 28px; cursor: pointer; line-height: 1;
        }
        .close-modal-btn:hover { color: var(--text-main); }
        .native-modal-body { padding: 24px; }
        .native-modal-footer {
            padding: 16px 24px;
            background: var(--panel-hover);
            border-top: 1px solid var(--panel-border);
            display: flex; justify-content: flex-end; gap: 12px;
        }
    </style>
@endsection

@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if(modal) modal.classList.add('active');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if(modal) modal.classList.remove('active');
    }

    // Fermeture automatique en cliquant à l'extérieur de la boîte modale
    window.addEventListener('click', (e) => {
        if(e.target.classList.contains('native-modal')) {
            e.target.classList.remove('active');
        }
    });
</script>
@endpush



