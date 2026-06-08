@extends('layouts.app')
@section('title', 'Inscription')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-dark text-white text-center">
                <h4>Créer un compte</h4>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control mb-3">

                    <label>Email</label>
                    <input type="email" name="email" class="form-control mb-3">

                    <label>Mot de passe</label>
                    <input type="password" name="password" class="form-control mb-3">

                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control mb-3">

                    <button class="btn btn-success w-100">S'inscrire</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
