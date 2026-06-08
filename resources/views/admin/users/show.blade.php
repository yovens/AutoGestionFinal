@extends('admin.layout')

@section('title','Détails utilisateur')
@section('page_title','Profil utilisateur')

@section('content')
<style>
    /* === 1. DESIGN THEME SYSTEM PREMIUM === */
    .admin-wrapper {
        font-family: 'Rajdhani', sans-serif;
        color: #ffffff;
        animation: fadeInPage 0.5s ease-in-out;
    }

    @keyframes fadeInPage {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* === 2. PROFILE HERO BANNER & INFO CARDS === */
    .profile-hero-card {
        background: rgba(10, 15, 26, 0.65);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(0, 242, 254, 0.2);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), inset 0 0 15px rgba(0, 242, 254, 0.05);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
    }

    .section-title {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #ffffff;
        text-shadow: 0 0 10px rgba(0, 242, 254, 0.3);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .section-title i {
        color: #00f2fe;
    }

    .cyber-divider {
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, rgba(0, 242, 254, 0.4), transparent);
        margin: 15px 0 25px 0;
    }

    /* Kriyasyon yon Grid solid pou enfòmasyon yo */
    .meta-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }

    .info-tile {
        background: rgba(4, 8, 20, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.05);
        padding: 16px 20px;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .info-tile:hover {
        border-color: rgba(0, 242, 254, 0.4);
        box-shadow: 0 0 15px rgba(0, 242, 254, 0.1);
        transform: translateY(-2px);
    }

    .tile-label {
        color: #64748b;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .tile-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #ffffff;
    }

    /* === 3. BADGES STATUS AVANSE === */
    .badge-custom {
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-block;
    }
    .badge-admin { background: rgba(127, 0, 255, 0.2); color: #bc7fff; border: 1px solid rgba(127, 0, 255, 0.4); }
    .badge-client { background: rgba(0, 242, 254, 0.15); color: #00f2fe; border: 1px solid rgba(0, 242, 254, 0.3); }
    .badge-active { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
    .badge-suspended { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }

    /* === 4. TABLES HIGH-TECH CORPORATE === */
    .table-responsive-cyber {
        background: rgba(10, 15, 26, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
    }

    .cyber-table {
        width: 100%;
        margin-bottom: 0;
        color: #e2e8f0;
        border-collapse: collapse;
    }

    .cyber-table thead {
        background: rgba(4, 8, 20, 0.8);
    }

    .cyber-table th {
        font-family: 'Orbitron', sans-serif;
        padding: 16px 20px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #00f2fe;
        border-bottom: 2px solid rgba(0, 242, 254, 0.2);
        font-weight: 700;
    }

    .cyber-table td {
        padding: 16px 20px;
        font-size: 1rem;
        font-weight: 600;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        vertical-align: middle;
        transition: all 0.2s;
    }

    .cyber-table tbody tr {
        transition: all 0.2s ease;
    }

    .cyber-table tbody tr:hover {
        background: rgba(0, 242, 254, 0.03);
    }

    .empty-row-visual {
        padding: 40px !important;
        text-align: center;
        color: #64748b;
        font-style: italic;
        font-size: 1.05rem;
    }
    
    .text-amount {
        color: #ffc300;
        font-weight: 700;
        text-shadow: 0 0 5px rgba(255, 195, 0, 0.2);
    }
</style>

<div class="admin-wrapper">

    <div class="profile-hero-card">
        <h2 class="section-title">
            <i class="bi bi-person-bounding-box"></i> Informations Système Client
        </h2>
        <div class="cyber-divider"></div>

        <div class="meta-info-grid">
            <div class="info-tile">
                <div class="tile-label">UID Identifiant</div>
                <div class="tile-value" style="color: #00f2fe;">#{{ $user->id }}</div>
            </div>

            <div class="info-tile">
                <div class="tile-label">Nom Complet</div>
                <div class="tile-value">{{ $user->nom }}</div>
            </div>

            <div class="info-tile">
                <div class="tile-label">Adresse Email</div>
                <div class="tile-value" style="font-size: 0.95rem; word-break: break-all;">{{ $user->email }}</div>
            </div>

            <div class="info-tile">
                <div class="tile-label">Téléphone Contact</div>
                <div class="tile-value">{{ $user->telephone ?? 'Non renseigné' }}</div>
            </div>

            <div class="info-tile">
                <div class="tile-label">Privilège Accès</div>
                <div class="mt-1">
                    @if(strtoupper($user->role) === 'ADMIN')
                        <span class="badge-custom badge-admin"><i class="bi bi-shield-lock me-1"></i> {{ $user->role }}</span>
                    @else
                        <span class="badge-custom badge-client"><i class="bi bi-person me-1"></i> {{ $user->role }}</span>
                    @endif
                </div>
            </div>

            <div class="info-tile">
                <div class="tile-label">Statut Compte</div>
                <div class="mt-1">
                    @if(strtolower($user->status) === 'active' || strtolower($user->status) === 'actif' || strtolower($user->status) === 'active_form')
                        <span class="badge-custom badge-active"><i class="bi bi-check2-circle me-1"></i> {{ $user->status }}</span>
                    @else
                        <span class="badge-custom badge-suspended"><i class="bi bi-x-circle me-1"></i> {{ $user->status }}</span>
                    @endif
                </div>
            </div>

            <div class="info-tile" style="grid-column: span repeat(auto-fit, minmax(240px, 1fr));">
                <div class="tile-label">Enregistrement Système</div>
                <div class="tile-value" style="font-size: 1rem; color: #94a3b8;"><i class="bi bi-calendar3 me-2"></i>{{ $user->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="profile-hero-card">
        <h3 class="section-title">
            <i class="bi bi-cpu-fill"></i> Historique des Transactions
        </h3>
        <div class="cyber-divider"></div>

        <div class="table-responsive-cyber">
            <table class="cyber-table">
                <thead>
                    <tr>
                        <th>ID ID</th>
                        <th>Montant Flux</th>
                        <th>Type Transaction</th>
                        <th>Horodatage (Date)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->transactions as $t)
                        <tr>
                            <td style="color: #00f2fe;">#{{ $t->id }}</td>
                            <td class="text-amount">{{ $t->montant ?? '-' }} USD</td>
                            <td>
                                <span style="text-transform: uppercase; letter-spacing: 0.5px;">{{ $t->type ?? '-' }}</span>
                            </td>
                            <td style="color: #64748b;">{{ $t->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-row-visual">
                                <i class="bi bi-exclamation-triangle me-2"></i> Aucune donnée de transaction détectée sur ce compte.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="profile-hero-card">
        <h3 class="section-title">
            <i class="bi bi-speedometer2"></i> Contrats de Locations Actifs
        </h3>
        <div class="cyber-divider"></div>

        <div class="table-responsive-cyber">
            <table class="cyber-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Véhicule Spécifié</th>
                        <th>Durée Contrat</th>
                        <th>Montant Global</th>
                        <th>Statut Operational</th>
                        <th>Date Émission</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->loans as $loan)
                        <tr>
                            <td style="color: #00f2fe;">#{{ $loan->id }}</td>
                            <td style="font-weight: 700;">
                                <i class="bi bi-car-front me-2" style="color: #00f2fe;"></i>
                                {{ $loan->vehicle->name ?? $loan->vehicle->model ?? 'N/A' }}
                            </td>
                            <td>
                                <span class="badge bg-dark text-white px-2 py-1 border border-secondary">{{ $loan->duration_days }} jours</span>
                            </td>
                            <td class="text-amount">{{ $loan->total_amount }} USD</td>
                            <td>
                                <span class="badge-custom badge-active" style="font-size: 0.8rem;">
                                    {{ $loan->status }}
                                </span>
                            </td>
                            <td style="color: #64748b;">{{ $loan->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-row-visual">
                                <i class="bi bi-terminal me-2"></i> Aucun enregistrement de location en cours pour cet utilisateur.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection