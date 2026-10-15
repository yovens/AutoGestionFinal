<?php

namespace App\Notifications;

use App\Models\LoanCart;
use App\Support\Agency;

/** Rapèl jou anvan retou a. */
class ReturnReminder extends ClientNotice
{
    public function __construct(public LoanCart $loan)
    {
    }

    protected function subject(): string
    {
        return 'Retour du véhicule demain';
    }

    protected function content(object $notifiable): array
    {
        $v = $this->loan->vehicle;

        return [
            'title' => 'Votre location se termine demain',
            'lines' => [
                "Merci de rapporter le véhicule {$v?->name} le " . $this->loan->end_date->translatedFormat('l j F') . ' à l’agence.',
                'Besoin de quelques jours de plus ? Demandez une prolongation depuis votre espace ou appelez-nous avant la date de retour.',
            ],
            'plate'  => $v?->plate,
            'action' => ['Prolonger ma location', route('client.loan')],
        ];
    }

    public function toSms(object $notifiable): string
    {
        return Agency::get('name') . " : rappel, retour du véhicule {$this->loan->vehicle?->plate} demain ("
            . $this->loan->end_date->translatedFormat('j M') . '). Prolongation possible depuis votre espace ou au ' . Agency::get('phone');
    }
}
