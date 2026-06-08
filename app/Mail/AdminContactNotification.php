<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminContactNotification extends Mailable
{
    use Queueable, SerializesModels;

    // Déclarez les propriétés publiques pour qu'elles soient disponibles dans la vue
    public $subject;
    public $clientMessage;
    public $clientName;
    public $clientEmail;

    /**
     * Crée une nouvelle instance de message.
     */
    public function __construct($subject, $message, $name, $email)
    {
        $this->subject = $subject;
        $this->clientMessage = $message;
        $this->clientName = $name;
        $this->clientEmail = $email;
    }

    /**
     * Construit le message.
     */
    public function build()
    {
        // Adresse e-mail de l'administrateur (peut être défini dans .env)
        $adminEmail = env('ADMIN_EMAIL', 'admin@votredomaine.com');

        return $this->subject('[CLIENT] Nouveau Contact : ' . $this->subject)
                    // L'e-mail apparaît comme venant du client (pour faciliter la réponse)
                    ->from($this->clientEmail, $this->clientName) 
                    ->replyTo($this->clientEmail, $this->clientName)
                    // Utiliser une vue Blade pour le corps de l'e-mail
                    ->view('emails.admin_contact_notification');
    }
}