@extends('layouts.client')

@section('title', 'Ma sélection')

@section('content')
<div class="page__head">
    <div>
        <h1 class="h1">Ma sélection</h1>
        <p class="muted">Les véhicules que vous gardez de côté. Rien n’est réservé tant que vous n’avez pas fait de demande.</p>
    </div>
</div>

<section class="panel">
    @forelse ($cartItems as $item)
        @php $v = $item->vehicle; @endphp
        <div class="loan">
            @if ($v->image_url)<img class="loan__img" src="{{ $v->image_url }}" alt="">@else<span class="loan__img"></span>@endif
            <div>
                <div class="row row--tight" style="margin-bottom:2px">
                    <a href="{{ route('client.vehicle.show', $v) }}" class="loan__name table__link">{{ $v->name }}</a>
                    <x-plate :plate="$v->plate" />
                </div>
                <div class="loan__meta num">{{ $v->year }}, location @money($v->loan_price) par jour, achat @money($v->price)</div>
                @unless ($v->is_available)
                    <x-badge style="margin-top:6px">Indisponible pour le moment</x-badge>
                @endunless
            </div>
            <div class="loan__side">
                <div class="row row--tight">
                    <form method="POST" action="{{ route('client.cart.delete', $item->id) }}" class="inline-form">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn--sm btn--ghost">Retirer</button>
                    </form>
                    <a href="{{ route('client.vehicle.show', $v) }}" class="btn btn--sm btn--primary" @unless ($v->is_available) aria-disabled="true" @endunless>Réserver</a>
                </div>
            </div>
        </div>
    @empty
        <x-empty icon="bag" title="Votre sélection est vide" text="Sur la fiche d’un véhicule, choisissez « Garder dans ma sélection » pour le comparer plus tard.">
            <a href="{{ route('client.vehicles') }}" class="btn btn--primary">Parcourir les véhicules</a>
        </x-empty>
    @endforelse
</section>
@endsection
