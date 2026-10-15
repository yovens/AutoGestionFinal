@extends('layouts.admin')

@section('title', 'Sauvegardes')

@php $latest = $backups[0] ?? null; @endphp

@section('content')
<div class="page-head">
    <div class="page-head__text">
        <h1 class="h1">Sauvegardes</h1>
        <p class="muted">Copie complète de la base de données (clients, locations, paiements, tickets, journal), faite chaque nuit à 2 h. Conservées {{ $agency['backup_keep_days'] }} jours.</p>
    </div>
    <form method="POST" action="{{ route('super.backups.store') }}" class="page-head__actions">
        @csrf
        <button type="submit" class="btn btn--primary"><x-icon name="shield" size="sm" /> Sauvegarder maintenant</button>
    </form>
</div>

@if (!$latest)
    <div class="flash flash--alert" style="margin-bottom:var(--s5)"><x-icon name="alert" /><span>Aucune sauvegarde pour l’instant. Lancez-en une maintenant, puis vérifiez que la tâche planifiée tourne sur le serveur (voir plus bas).</span></div>
@elseif ($latest['date']->lt(now()->subDays(2)))
    <div class="flash flash--alert" style="margin-bottom:var(--s5)"><x-icon name="alert" /><span>La dernière sauvegarde date du {{ $latest['date']->translatedFormat('j F à H:i') }}. La tâche planifiée ne tourne probablement pas : voir les instructions plus bas.</span></div>
@endif

<div class="grid-main grid-main--wide-side">
    <section class="panel panel--flush">
        @if (empty($backups))
            <x-empty icon="shield" title="Aucune sauvegarde" text="Cliquez sur « Sauvegarder maintenant » pour créer la première." />
        @else
            <div class="table-wrap"><table class="table">
                <thead><tr><th>Sauvegarde</th><th>Date</th><th class="right">Taille</th><th class="right"><span class="sr-only">Actions</span></th></tr></thead>
                <tbody>
                    @foreach ($backups as $i => $b)
                        <tr>
                            <td><span class="strong num">{{ $b['name'] }}</span>@if ($i === 0)<span class="table__sub">La plus récente</span>@endif</td>
                            <td class="nowrap">{{ $b['date']->translatedFormat('j M Y, H:i') }}<span class="table__sub">{{ $b['date']->diffForHumans() }}</span></td>
                            <td class="right num">{{ \App\Support\Backup::humanSize($b['size']) }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('super.backups.download', $b['name']) }}" class="btn btn--sm">Télécharger</a>
                                    <form method="POST" action="{{ route('super.backups.destroy', $b['name']) }}" class="inline-form" data-confirm="Supprimer la sauvegarde {{ $b['name'] }} ?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn--sm btn--ghost btn--icon" aria-label="Supprimer"><x-icon name="trash" size="sm" /></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
        @endif
    </section>

    <aside class="stack">
        <section class="panel">
            <div class="panel__head"><h2 class="h3">Gardez une copie ailleurs</h2></div>
            <div class="panel__body stack small">
                <p>Une sauvegarde sur le même ordinateur ne protège pas d’un vol ou d’une panne de disque. Téléchargez régulièrement la plus récente sur une clé USB ou un drive.</p>
                <p class="muted">
                    @if ($agency['backup_email'])
                        Une copie part aussi chaque nuit par e-mail à <strong>{{ $agency['backup_email'] }}</strong>.
                    @else
                        Vous pouvez aussi recevoir une copie par e-mail chaque nuit : <a href="{{ route('super.settings') }}">Paramètres</a>.
                    @endif
                </p>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head"><h2 class="h3">Tâche planifiée</h2></div>
            <div class="panel__body stack small">
                <p>Les sauvegardes, rappels et retards tournent grâce au planificateur Laravel. Il doit être lancé chaque minute.</p>
                <p><strong>Serveur Linux</strong> (crontab) :</p>
                <code style="display:block;padding:8px 10px;background:var(--surface-2);border:1px solid var(--line);border-radius:6px;overflow-x:auto;white-space:nowrap">* * * * * cd {{ base_path() }} && php artisan schedule:run</code>
                <p><strong>Windows</strong> (Planificateur de tâches) : créez une tâche répétée chaque minute qui lance <code>php artisan schedule:run</code> dans le dossier du projet. En développement : <code>php artisan schedule:work</code>.</p>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head"><h2 class="h3">Restaurer</h2></div>
            <div class="panel__body stack small">
                <p>La restauration remplace toutes les données actuelles. Elle se fait en ligne de commande, après une sauvegarde de sécurité automatique :</p>
                <code style="display:block;padding:8px 10px;background:var(--surface-2);border:1px solid var(--line);border-radius:6px;overflow-x:auto;white-space:nowrap">php artisan app:restore {{ $latest['name'] ?? 'nom-du-fichier.sql.gz' }}</code>
            </div>
        </section>
    </aside>
</div>
@endsection
