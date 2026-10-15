@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Messages</h1>
        <p class="muted">Questions envoyées par les clients depuis leur espace. La réponse part par e-mail.</p>
    </div>
</div>

<div class="toolbar">
    <nav class="tabs" aria-label="Filtrer">
        <a href="{{ route('admin.contacts.index') }}" @if (!$filter) aria-current="page" @endif>Tous</a>
        <a href="{{ route('admin.contacts.index', ['filter' => 'unread']) }}" @if ($filter === 'unread') aria-current="page" @endif>Non lus</a>
    </nav>
</div>

@if ($messages->isEmpty())
    <section class="panel"><x-empty icon="mail" title="Aucun message" text="Les messages envoyés depuis la page Contact du client apparaîtront ici." /></section>
@else
    <div class="stack">
        @foreach ($messages as $m)
            <article class="panel" style="{{ $m->is_read ? '' : 'border-left:3px solid var(--brand)' }}">
                <div class="panel__head">
                    <div class="person">
                        <x-avatar :user="$m->user" />
                        <div class="person__text">
                            <div class="person__name">{{ $m->subject }}</div>
                            <div class="small muted">
                                {{ $m->user?->nom ?? 'Compte supprimé' }}{{ $m->user ? ', ' . $m->user->email : '' }}, {{ $m->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    <div class="row row--tight">
                        @unless ($m->is_read)
                            <x-badge tone="brand">Nouveau</x-badge>
                            <form method="POST" action="{{ route('admin.contacts.read', $m) }}" class="inline-form">
                                @csrf<button type="submit" class="btn btn--sm btn--ghost">Marquer comme lu</button>
                            </form>
                        @endunless
                        <form method="POST" action="{{ route('admin.contacts.destroy', $m) }}" class="inline-form" data-confirm="Supprimer ce message ?">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn--sm btn--ghost btn--icon" aria-label="Supprimer"><x-icon name="trash" size="sm" /></button>
                        </form>
                    </div>
                </div>
                <div class="panel__body">
                    <p style="white-space:pre-line;max-width:75ch">{{ $m->message }}</p>
                    @if ($m->user)
                        <details style="margin-top:var(--s4)">
                            <summary class="btn btn--sm" style="list-style:none;display:inline-flex"><x-icon name="send" size="sm" /> Répondre</summary>
                            <form method="POST" action="{{ route('admin.contacts.reply', $m) }}" class="stack" style="margin-top:var(--s3);max-width:640px">
                                @csrf
                                <x-field name="message" type="textarea" :label="'Réponse à ' . $m->user->email" rows="5" required :value="''" />
                                <div><button type="submit" class="btn btn--primary">Envoyer la réponse</button></div>
                            </form>
                        </details>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endif
@endsection
