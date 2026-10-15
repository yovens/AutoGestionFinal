<?php

namespace App\Notifications;

use App\Models\LoanCart;
use App\Support\Agency;

/** Lokasyon an konfime: kliyan an resevwa kòd retrè li. */
class LoanConfirmed extends ClientNotice
{
    public function __construct(public LoanCart $loan)
    {
    }

    protected function subject(): string
    {
        return "Location confirmée, code {$this->loan->pickup_code}";
    }

    protected function content(object $notifiable): array
    {
        $v = $this->loan->vehicle;

        return [
            'title' => 'Votre location est confirmée',
            'lines' => [
                "Le véhicule {$v?->name} vous attend pour {$this->loan->duration_days} jour(s).",
                'Présentez ce code au comptoir de l’agence, avec une pièce d’identité. Le paiement se fait sur place.',
                'Adresse : ' . Agency::get('address') . '. Horaires : ' . Agency::get('hours') . '.',
            ],
            'code'   => $this->loan->pickup_code,
            'plate'  => $v?->plate,
            'action' => ['Voir ma location', route('client.loan')],
        ];
    }

    public function toSms(object $notifiable): string
    {
        return Agency::get('name') . " : location confirmée ({$this->loan->vehicle?->name}). "
            . "Code de retrait {$this->loan->pickup_code}. A présenter à l'agence avec une pièce d'identité. " . Agency::get('phone');
    }
}
