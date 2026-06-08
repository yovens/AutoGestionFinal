@extends('client.layout')
@section('title','Détails Véhicule')

@section('content')
<div class="row">
    <div class="col-md-6">
        <img src="/storage/{{ $vehicle->image }}" class="img-fluid shadow rounded">
    </div>
    <div class="col-md-6">
        <h2>{{ $vehicle->brand }} - {{ $vehicle->model }}</h2>
        <p>Année : {{ $vehicle->year }}</p>
        <h3 class="fw-bold text-success">{{ number_format($vehicle->price) }} HTG</h3>
        <form method="POST" action="{{ route('client.cart.add',$vehicle->id) }}">
            @csrf
            <button class="btn btn-primary w-100 mb-2">Ajouter au Panier</button>
        </form>
        <a href="{{ route('client.loan.create',$vehicle->id) }}" class="btn btn-warning w-100">Louer</a>
    </div>
</div>
@endsection
