<?php

namespace App\Notifications;

use App\Models\LoanCart;
use App\Support\Agency;

/** Machin nan pa retounen nan dat la. */
class LateNotice extends ClientNotice
{
    public function __construct(public LoanCart $loan)
    {
    }

    protected function subject(): string
    {
        return 'Retour du véhicule en retard';
    }

    protected function content(object $notifiable): array
    {
        $v = $this->loan->vehicle;
        $percent = (int) Agency::get('late_fee_percent', 100);

        return [
            'title' => 'Le véhicule n’a pas encore été rendu',
            'lines' => [
                "Le retour du véhicule {$v?->name} était prévu le " . $this->loan->end_date->translatedFormat('l j F') . '.',
                $percent > 0
                    ? 'Chaque jour de retard est facturé au retour (' . $percent . ' % du prix par jour). Rapportez le véhicule dès que possible ou contactez-nous pour prolonger.'
                    : 'Rapportez le véhicule dès que possible ou contactez-nous pour prolonger.',
            ],
            'plate'  => $v?->plate,
            'action' => ['Voir ma location', route('client.loan')],
        ];
    }

    public function toSms(object $notifiable): string
    {
        return Agency::get('name') . " : le véhicule {$this->loan->vehicle?->plate} devait être rendu le "
            . $this->loan->end_date->translatedFormat('j M') . '. Merci de le rapporter ou de nous appeler : ' . Agency::get('phone');
    }
}
