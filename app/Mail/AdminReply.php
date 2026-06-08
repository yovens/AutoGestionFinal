<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminReply extends Mailable
{
    use Queueable, SerializesModels;

    public $replyMessage;

    // Nou pase mesaj la nan konstrukteur a
    public function __construct($replyMessage)
    {
        $this->replyMessage = $replyMessage;
    }

    public function build()
    {
        return $this->subject('Réponse à votre message')
                    ->view('emails.admin_reply'); // Sa a se gade paj ki pral gen konsepsyon imel la
    }
}