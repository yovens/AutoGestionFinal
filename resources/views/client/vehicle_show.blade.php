@extends('layouts.client')

@section('title', $vehicle->brand . ' ' . $vehicle->model)

@section('content')
<style>
    /* Global Page Animations */
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-15px); } }

    .details-container {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 40px;
        max-width: 1200px;
        margin: 50px auto;
        padding: 40px;
        background: rgba(31, 42, 55, 0.4);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 30px;
        animation: fadeIn 0.8s ease-out;
    }

    .img-showcase { 
        width: 100%; height: 450px; object-fit: cover;
        border-radius: 25px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        animation: float 6s ease-in-out infinite;
        border: 2px solid rgba(255,255,255,0.05);
    }

    .specs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin: 30px 0; }
    
    .spec-item {
        background: var(--bg); padding: 15px; border-radius: 15px;
        text-align: center; border: 1px solid rgba(255,255,255,0.05);
        transition: 0.3s;
    }
    .spec-item:hover { transform: translateY(-5px); border-color: var(--secondary); background: rgba(255,255,255,0.05); }
    .spec-item i { color: var(--secondary); margin-bottom: 8px; display: block; font-size: 1.4rem; }

    .price-card {
        background: linear-gradient(145deg, var(--card), #161b22);
        padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);
    }

    /* Buttons Ultra Style */
    .btn-action {
        width: 100%; padding: 16px; margin-bottom: 12px; border-radius: 15px;
        border: none; font-weight: 800; cursor: pointer; transition: 0.4s;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .btn-loan { background: var(--secondary); color: #000; box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3); }
    .btn-purchase { background: var(--primary); color: white; box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3); }
    .btn-cart { background: transparent; border: 2px solid var(--text); color: var(--text); }
    
    .btn-action:hover:not(:disabled) { transform: scale(1.03); filter: brightness(1.2); }
    .btn-action:disabled { opacity: 0.3; cursor: not-allowed; filter: grayscale(1); }

    @media (max-width: 992px) { .details-container { grid-template-columns: 1fr; } }
</style>

<div class="details-container">
    {{-- Left Column --}}
    <div>
        <a href="{{ route('client.vehicles') }}" style="color: var(--text); text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Retour au catalogue
        </a>
        <h1 style="margin-top: 20px; font-size: 2.8rem; font-weight: 900;">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
        <img src="/storage/{{ $vehicle->image }}" class="img-showcase" alt="{{ $vehicle->model }}">
        
        <div class="specs-grid">
            <div class="spec-item"><i class="fas fa-calendar-alt"></i><p>Année</p><strong>{{ $vehicle->year }}</strong></div>
            <div class="spec-item"><i class="fas fa-layer-group"></i><p>Stock</p><strong>{{ $vehicle->quantity }}</strong></div>
            <div class="spec-item"><i class="fas fa-id-card"></i><p>Plaque</p><strong>{{ $vehicle->plate }}</strong></div>
        </div>
    </div>

    {{-- Right Column --}}
    <div>
        <div class="price-card">
            <h3 style="margin-bottom: 20px;">Options de Réservation</h3>
            <p>Location: <span style="color: var(--secondary); font-size: 1.4rem; font-weight: 800;">${{ number_format($vehicle->loan_price) }}/jour</span></p>
            <p style="margin-bottom: 25px;">Achat: <span style="color: var(--primary); font-size: 1.4rem; font-weight: 800;">${{ number_format($vehicle->price) }}</span></p>
            
            <form method="POST" action="{{ route('client.loan.start', $vehicle->id) }}">
                @csrf
                <button class="btn-action btn-loan" {{ $vehicle->quantity <= 0 ? 'disabled' : '' }}>
                    <i class="fas fa-key"></i> {{ $vehicle->quantity > 0 ? 'Louer Immédiatement' : 'Indisponible' }}
                </button>
            </form>
            
            <form method="POST" action="{{ route('client.purchase.start', $vehicle->id) }}">
                @csrf
                <button class="btn-action btn-purchase" {{ $vehicle->quantity <= 0 ? 'disabled' : '' }}>
                    <i class="fas fa-handshake"></i> {{ $vehicle->quantity > 0 ? 'Acheter maintenant' : 'Indisponible' }}
                </button>
            </form>

            <form method="POST" action="{{ route('client.cart.add', $vehicle->id) }}">
                @csrf
                <button type="submit" class="btn-action btn-cart" {{ $vehicle->quantity <= 0 ? 'disabled' : '' }}>
                    <i class="fas fa-shopping-cart"></i> Ajouter au panier
                </button>
            </form>
        </div>
    </div>
</div>
@endsection