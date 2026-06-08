@extends('layouts.client')

@section('title', 'Mon Panier')

@section('content')
<main class="panier-wrapper">
    <h2 class="panier-title"><i class="fas fa-shopping-cart"></i> Mon Panier</h2>

    @if($cartItems->isEmpty())
        <div class="empty-cart">Votre panier est vide.</div>
    @else
        <div class="panier-grid">
            <div class="items-list">
                @foreach($cartItems as $item)
                <div class="product-card">
                    <img src="{{ asset('storage/' . $item->vehicle->image) }}" alt="Véhicule">
                    <div class="info">
                        <h5>{{ $item->vehicle->brand }} {{ $item->vehicle->model }}</h5>
                        <p>{{ number_format($item->vehicle->price, 0) }} USD</p>
                    </div>
                    <form method="POST" action="{{ route('client.cart.delete', $item->id) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete">Supprimer</button>
                    </form>
                </div>
                @endforeach
            </div>

            <div class="summary-box">
                <h5>Résumé</h5>
                <hr>
                <div class="total">
                    <span>Total</span>
                    <strong>{{ number_format($cartItems->sum(fn($i) => $i->vehicle->price), 0) }} USD</strong>
                </div>
                <button class="btn-checkout">Passer à la caisse</button>
            </div>
        </div>
    @endif
</main>
@endsection



<style>
    /* 1. Conteneur principal : limité à 1000px, centré sur la page */
    .panier-wrapper {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .panier-title { margin-bottom: 25px; color: var(--text-color); }

    /* 2. Grille : 2 colonnes (liste à gauche, résumé à droite) */
    .panier-grid {
        display: grid;
        grid-template-columns: 1fr 350px; /* La liste prend tout l'espace, résumé fixe 350px */
        gap: 30px;
        align-items: start;
    }

    /* 3. Style des cartes de produits */
    .product-card {
        display: flex;
        align-items: center;
        background: var(--card-bg);
        padding: 15px;
        border-radius: 15px;
        margin-bottom: 15px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: var(--shadow);
    }

    .product-card img {
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
    }

    .info { flex-grow: 1; margin-left: 20px; }
    .info h5 { margin: 0; font-size: 1.1rem; }
    .info p { margin: 0; color: var(--primary); font-weight: bold; }

    /* 4. Style du résumé */
    .summary-box {
        background: var(--card-bg);
        padding: 25px;
        border-radius: 15px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: var(--shadow);
        position: sticky; /* Reste fixe au scroll */
        top: 20px;
    }

    .total { display: flex; justify-content: space-between; margin: 15px 0; font-size: 1.2rem; }

    /* 5. Boutons */
    .btn-delete { background: #fee2e2; color: #991b1b; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; transition: 0.2s; }
    .btn-delete:hover { background: #fecaca; }
    
    .btn-checkout { width: 100%; background: var(--primary); color: white; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.2s; }
    .btn-checkout:hover { opacity: 0.9; }

    /* 6. Responsive : Si écran petit (mobile), on empile tout */
    @media (max-width: 768px) {
        .panier-grid { grid-template-columns: 1fr; }
    }
</style>