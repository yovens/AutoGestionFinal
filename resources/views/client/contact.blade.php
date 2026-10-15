@extends('layouts.client')

@section('title', 'Contact')

@section('content')
<div class="page__head">
    <div>
        <h1 class="h1">Écrire à l’agence</h1>
        <p class="muted">Une question sur une location, un paiement ou un véhicule ? Réponse par e-mail à {{ auth()->user()->email }}.</p>
    </div>
</div>

<div class="grid-main">
    <section class="panel">
        <form method="POST" action="{{ route('client.contact.send') }}">
            @csrf
            <div class="panel__body stack">
                <x-field name="subject" label="Objet" placeholder="Ex. Retour du véhicule" required maxlength="255" />
                <x-field name="message" type="textarea" label="Message" rows="7" required />
            </div>
            <div class="panel__foot row row--end"><button type="submit" class="btn btn--primary"><x-icon name="send" size="sm" /> Envoyer</button></div>
        </form>
    </section>

    <aside class="stack">
        <div class="panel">
            <div class="panel__body">
                <h2 class="h3">Par téléphone</h2>
                <p style="margin-top:6px"><a href="tel:+50940160335" class="strong">+509 40 16 03 35</a></p>
                <p class="small muted">Lundi au vendredi, 8 h à 17 h</p>
                <hr class="divider" style="margin:var(--s4) 0">
                <h2 class="h3">À l’agence</h2>
                <p class="small" style="margin-top:6px">Simon, arrondissement des Cayes</p>
            </div>
        </div>

        @if ($history->isNotEmpty())
            <div class="panel">
                <div class="panel__head"><h2 class="h3">Vos derniers messages</h2></div>
                <div class="queue">
                    @foreach ($history as $m)
                        <div class="queue__item">
                            <div class="queue__main">
                                <div class="queue__title">{{ $m->subject }}</div>
                                <div class="queue__meta">{{ $m->created_at->translatedFormat('j M Y') }}</div>
                            </div>
                            <x-badge :tone="$m->is_read ? 'ok' : 'wait'">{{ $m->is_read ? 'Lu' : 'Envoyé' }}</x-badge>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </aside>
</div>
@endsection
