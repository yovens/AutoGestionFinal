<?php

namespace App\Notifications;

use App\Support\Agency;

/** Mesaj tès ke super admin nan voye bay tèt li depi Paramètres. */
class TestNotice extends ClientNotice
{
    protected function subject(): string
    {
        return 'Test des notifications';
    }

    protected function content(object $notifiable): array
    {
        return [
            'title' => 'Les notifications fonctionnent',
            'lines' => ['Ce message de test confirme que vos clients recevront les codes de retrait et les rappels de retour.'],
            'code'  => 'K7P-29Q',
        ];
    }

    public function toSms(object $notifiable): string
    {
        return Agency::get('name') . ' : test des SMS réussi. Vos clients recevront leurs codes et rappels.';
    }
}
