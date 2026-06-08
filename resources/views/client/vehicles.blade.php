@extends('layouts.client')

@section('title', 'Véhicules')

@section('content')
<style>
    /* Styles espesifik pou paj sa a */
    .page-header-vehicles {
        font-size: 2rem; font-weight: 800; margin-bottom: 30px; text-align: center;
        color: var(--text);
    }

    /* Filtre (Custom Flexbox) */
    .filter-section {
        background: var(--card); padding: 20px; border-radius: 15px;
        display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 40px;
        border: 1px solid rgba(128,128,128,0.2);
    }
    .filter-item { flex: 1; min-width: 150px; }
    .filter-item input, .filter-item select {
        width: 100%; padding: 10px; border-radius: 8px;
        background: var(--bg); border: 1px solid #475569; color: var(--text);
    }

    /* Grid Layout (Custom CSS Grid) */
    .vehicle-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
    }

    /* Card Design */
    .vehicle-card {
        background: var(--card); border-radius: 20px; overflow: hidden;
        transition: 0.3s; border: 1px solid rgba(128,128,128,0.1);
    }
    .vehicle-card:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
    
    .img-wrapper { position: relative; height: 220px; }
    .img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    .price-tag {
        position: absolute; bottom: 15px; right: 0; background: var(--primary);
        color: white; padding: 8px 20px; border-radius: 15px 0 0 15px; font-weight: bold;
    }

    .card-content { padding: 20px; }
    .details-row { display: flex; justify-content: space-between; margin: 15px 0; color: #94a3b8; font-size: 0.9rem; }
    
    .btn-details {
        display: block; text-align: center; background: var(--primary);
        color: white; padding: 12px; border-radius: 10px; text-decoration: none;
    }
</style>

<h2 class="page-header-vehicles"><i class="fas fa-road"></i> Notre Flotte</h2>

{{-- Filtres --}}
<form method="GET" action="{{ route('client.vehicles') }}" class="filter-section">
    <div class="filter-item"><input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}"></div>
    <div class="filter-item">
        <select name="brand">
            <option value="">Toutes les marques</option>
            @foreach($brands as $b) <option value="{{ $b }}">{{ $b }}</option> @endforeach
        </select>
    </div>
    <button type="submit" style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px;">Filtrer</button>
</form>

{{-- Grid --}}
<div class="vehicle-grid">
    @foreach($vehicles as $v)
    <div class="vehicle-card">
        <div class="img-wrapper">
            <img src="/storage/{{ $v->image }}" alt="{{ $v->model }}">
            <span class="price-tag">{{ number_format($v->price) }} USD/jour</span>
        </div>
        <div class="card-content">
            <h3>{{ $v->brand }} {{ $v->model }}</h3>
            <div class="details-row">
                <span><i class="fas fa-calendar-alt"></i> {{ $v->year }}</span>
                <span><i class="fas fa-cogs"></i> Auto</span>
            </div>
            <a href="{{ route('client.vehicle.show', $v->id) }}" class="btn-details">Voir Détails</a>
        </div>
    </div>
    @endforeach
</div>
@endsection

<style>
    /* 1. Animation Antre (Staggered Effect) */
    .vehicle-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
        perspective: 1000px; /* Pou efè 3D */
    }

    /* 2. Glassmorphism Card */
    .vehicle-card {
        background: var(--card);
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
    }

    /* 3. Efè Glow nan Hover */
    .vehicle-card:hover {
        transform: translateY(-15px) rotateY(5deg);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 15px var(--primary);
    }

    .img-wrapper { position: relative; height: 220px; overflow: hidden; }
    .img-wrapper img { 
        width: 100%; height: 100%; object-fit: cover;
        transition: transform 0.5s ease;
    }
    .vehicle-card:hover img { transform: scale(1.1); }

    .price-tag {
        position: absolute; bottom: 20px; right: 0;
        background: var(--primary); color: white;
        padding: 10px 25px; border-radius: 20px 0 0 20px;
        font-weight: 800; z-index: 2;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    /* 4. Bouton Animé */
    .btn-details {
        display: block; text-align: center; background: var(--primary);
        color: white; padding: 14px; border-radius: 12px;
        text-decoration: none; font-weight: 600;
        transition: background 0.3s, transform 0.2s;
        position: relative; overflow: hidden;
    }
    .btn-details:hover { transform: scale(1.03); filter: brightness(1.2); }

    /* Animation antre paj la */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .vehicle-card { animation: fadeInUp 0.6s ease forwards; opacity: 0; }
</style>