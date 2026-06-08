@extends('layouts.client')

@section('title', 'Contact Admin')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-5"><i class="fas fa-paper-plane me-2"></i> Contacter l’Administrateur</h2>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
         <div class="card contact-card p-4 p-md-5">
                @if(session('success'))
                    <div class="alert alert-success mb-4" style="border-radius: 10px;">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif
                
              <form method="POST" action="{{ route('client.contact.send') }}">
    @csrf
    <div class="mb-3">
        <label for="subject" class="form-label">Objet</label>
        <input type="text" id="subject" name="subject" class="form-control" placeholder="Ex: Problème avec mon véhicule..." required>
    </div>
    
    <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea id="message" name="message" rows="6" class="form-control" placeholder="Décrivez votre demande ici..." required></textarea>
    </div>
    
    <button type="submit" class="btn-submit">Envoyer le message</button>
</form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* 1. On s'assure que le wrapper ne dépasse jamais */
    .container {
        display: flex;
        justify-content: center; /* Centre horizontalement */
        width: 100%;
        padding: 20px;
    }

    /* 2. On force la carte à une largeur maximale stricte */
    .contact-card {
        width: 100%;             /* Prend la largeur dispo */
        max-width: 500px;       /* Limite la largeur à 500px (plus serré) */
        background-color: var(--card-bg); 
        border-radius: 20px; 
        padding: 30px; 
        box-shadow: var(--shadow);
        box-sizing: border-box; /* Crucial */
    }

    /* 3. Les champs doivent être 100% de la carte */
    .form-control { 
        display: block;
        width: 100%; 
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background-color: var(--bg-color);
        color: var(--text-color);
        box-sizing: border-box; /* Empêche le padding de sortir du cadre */
    }

    .btn-submit { 
        width: 100%;
        padding: 12px;
        background-color: var(--primary);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
    }
</style>



@endsection
