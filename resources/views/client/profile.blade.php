@extends('layouts.client') {{-- Sèvi ak layout client ou an --}}

@section('title', 'Mon Profil')

@section('content')
<div class="container" style="margin-top: 30px;">
    <h2><i class="fas fa-user-circle"></i> Mon Profil</h2>
    <p style="color: var(--text-color); opacity: 0.7;">Gérez vos informations personnelles et votre sécurité.</p>

    {{-- AFICHAGE MESAJ --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="profile-layout-grid">
        {{-- BLÒK ENFÒMASYON --}}
        <div class="card">
            <h4 class="form-section-title">Mes Informations</h4>
<form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') {{-- Sa a enpòtan anpil pou wout UPDATE --}}
    
    <div class="avatar-upload-section">
        <div class="avatar-preview-wrapper">
            {{-- Sèvi ak $user ki soti nan compact('user') --}}
            <img id="current-profile-pic" 
                 src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : 'https://via.placeholder.com/120' }}" 
                 alt="Avatar">
        </div>
        <div>
            <label class="form-label">Changer de photo</label>
            <input type="file" name="profile_image" class="form-control" accept="image/*">
        </div>
    </div>

    <label class="form-label">Nom Complet</label>
    <input type="text" name="nom" class="form-control" value="{{ $user->nom }}" required>

    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>

    <button type="submit" class="btn-submit">Enregistrer</button>
</form>
@if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #a7f3d0;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #fecaca;">
        @foreach($errors->all() as $error)
            <p><i class="fas fa-exclamation-circle"></i> {{ $error }}</p>
        @endforeach
    </div>
@endif
        </div>

       {{-- BLÒK MODPAS --}}
<div class="card">
    <h4 class="form-section-title" style="color: #ef4444;">Sécurité</h4>
    
    <form action="{{ route('client.profile.password') }}" method="POST">
        @if($errors->any())
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #fecaca;">
        @foreach($errors->all() as $error)
            <p><i class="fas fa-exclamation-circle"></i> {{ $error }}</p>
        @endforeach
    </div>
@endif
        @csrf
        @method('PUT') 
        
        <label class="form-label">Mot de passe actuel</label>
        <input type="password" name="current_password" class="form-control" required>

        <label class="form-label">Nouveau mot de passe</label>
        <input type="password" name="password" class="form-control" required>

        <button type="submit" class="btn-submit" style="background: #ef4444;">
            Modifier le mot de passe
        </button>
        
    </form>
</div>


</form>
        </div>
    </div>
</div>

<style>
    .profile-layout-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
    .avatar-preview-wrapper { width: 100px; height: 100px; border-radius: 50%; overflow: hidden; margin-bottom: 10px; }
    .avatar-preview-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    @media (max-width: 768px) { .profile-layout-grid { grid-template-columns: 1fr; } }
</style>
<script>
    document.querySelector('input[name="profile_image"]').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('current-profile-pic').src = event.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endsection