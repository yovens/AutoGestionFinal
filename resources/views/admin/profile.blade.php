@extends('admin.layout')

@section('title', 'Mon Profil - AutoGestion Admin')
@section('page_title', 'Mon Profil')
@section('page_icon') <i class="bi bi-person-circle me-2"></i> @endsection

@section('content')
    <p class="intro" style="color: var(--text-muted, #94a3b8); font-size: 15px; margin-bottom: 24px; max-width: 800px; line-height: 1.6;">
        Consultez et mettez à jour vos informations personnelles d'administrateur, modifiez votre avatar ou gérez votre sécurité.
    </p>

    {{-- AFICHAGE MESAJ SIKSÈ OSSA ERÈ --}}
    @if(session('success'))
        <div class="alert-custom success" style="margin-bottom: 24px;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-custom danger" style="margin-bottom: 24px;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-layout-grid">
        
        {{-- BLÒK ENFÒMASYON PESONÈL --}}
        <div class="profile-container">
            <h4 class="form-section-title"><i class="bi bi-person-gear me-2"></i> Gérer mes informations</h4>

            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- SECTION AVATAR ET UPLOAD --}}
                <div class="avatar-upload-section">
                    <div class="avatar-preview-wrapper">
                        <img id="current-profile-pic" src="{{ $adminProfilePic ?? 'https://via.placeholder.com/120/38bdf8/ffffff?text=AD' }}" alt="Image de profil">
                    </div>
                    <div style="flex-grow: 1;">
                        <label for="profile_image" class="form-label">Téléverser une nouvelle image</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                        <span class="form-help-text">Max 2MB. Format JPG, PNG oswa WEBP.</span>
                    </div>
                </div>

                {{-- CHAMPS DE SAISIE EN GRILLE --}}
                <div class="grid-form">
                    <div>
                        <label for="nom" class="form-label">Nom Complet</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="{{ $adminName }}" required>
                    </div>
                    <div>
                        <label for="role" class="form-label">Rôle applicatif</label>
                        <input type="text" class="form-control" id="role" value="{{ $adminRole }}" disabled>
                        <span class="form-help-text">Défini par le système.</span>
                    </div>
                </div>
                
                {{-- BIOGRAPHIE / TEXTAREA --}}
                <div style="margin-top: 20px;">
                    <label for="presentation" class="form-label">Présentation / Bio</label>
                    <textarea class="form-control" id="presentation" name="presentation" rows="4">{{ $adminPresentation }}</textarea>
                </div>

                {{-- SOUmission --}}
                <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-submit-action success-btn">
                        <i class="bi bi-save me-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>

        {{-- BLÒK CHANJMAN MODPAS (SEKIRITE) --}}
        <div class="profile-container">
            <h4 class="form-section-title" style="color: #ef4444;"><i class="bi bi-shield-lock me-2"></i> Sécurité & Mot de passe</h4>
            
            <form action="{{ route('admin.profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 20px;">
                    <label for="current_password" class="form-label">Mot de passe actuel</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="password" class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <span class="form-help-text">Minimum 8 caractères.</span>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>

                <div style="margin-top: 32px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-submit-action danger-btn">
                        <i class="bi bi-key me-2"></i> Modifier le mot de passe
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- STYLES HARMONISÉS --}}
    <style>
        .profile-layout-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 24px;
            align-items: start;
        }
        .profile-container {
            width: 100%;
            background: var(--panel-bg, #1e293b);
            border-radius: 14px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);
            border: 1px solid var(--panel-border, #334155);
            padding: 30px;
            box-sizing: border-box;
        }
        .form-section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--brand-primary, #3b82f6);
            margin-top: 0;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--panel-border, #334155);
            padding-bottom: 12px;
        }
        .avatar-upload-section {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--panel-border, #334155);
        }
        .avatar-preview-wrapper {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--brand-primary, #3b82f6);
            box-shadow: 0 0 12px rgba(59, 130, 246, 0.3);
            flex-shrink: 0;
        }
        .avatar-preview-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .grid-form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-main, #cbd5e1);
        }
        .form-control {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid var(--panel-border, #334155);
            background: var(--panel-hover, #0f172a);
            color: var(--text-main, #fff);
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--brand-primary, #3b82f6);
        }
        .form-control:disabled {
            background: rgba(15, 23, 42, 0.6);
            color: var(--text-muted, #94a3b8);
            cursor: not-allowed;
        }
        .form-help-text {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: var(--text-muted, #94a3b8);
        }
        .btn-submit-action {
            color: #fff;
            border: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            padding: 11px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }
        .btn-submit-action:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }
        .success-btn { background: #22c55e; }
        .danger-btn { background: #ef4444; }

        .alert-custom { padding: 14px 18px; border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .alert-custom.success { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2); }
        .alert-custom.danger { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); align-items: flex-start; }

        @media (max-width: 992px) {
            .profile-layout-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .grid-form { grid-template-columns: 1fr; }
            .avatar-upload-section { flex-direction: column; text-align: center; }
        }
    </style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const profileImageInput = document.getElementById('profile_image');
        const currentProfilePic = document.getElementById('current-profile-pic');
        
        if (profileImageInput) {
            profileImageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (currentProfilePic) {
                            currentProfilePic.src = e.target.result;
                        }
                        const sidebarPic = document.querySelector('.admin-profile .profile-pic');
                        if (sidebarPic) {
                            sidebarPic.src = e.target.result;
                        }
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endpush