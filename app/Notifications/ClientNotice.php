<?php

namespace App\Notifications;

use App\Notifications\Channels\SmsChannel;
use App\Support\Agency;
use App\Support\Sms;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Baz pou tout mesaj kliyan yo: menm modèl e-mail, chanèl yo chwazi selon Paramètres
 * ak sa kliyan an genyen (e-mail reyèl, telefòn).
 */
abstract class ClientNotice extends Notification
{
    abstract protected function subject(): string;

    /** @return array{title:string, lines:array<int,string>, code?:?string, plate?:?string, action?:?array{0:string,1:string}} */
    abstract protected function content(object $notifiable): array;

    abstract public function toSms(object $notifiable): string;

    public function via(object $notifiable): array
    {
        $channels = [];

        if (Agency::get('notify_mail') && $this->hasRealEmail($notifiable)) {
            $channels[] = 'mail';
        }
        if (Agency::get('notify_sms') && Sms::normalize($notifiable->telephone ?? null)) {
            $channels[] = SmsChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject() . ' | ' . Agency::get('name'))
            ->view('emails.notice', $this->content($notifiable) + ['agency' => Agency::all(), 'name' => $notifiable->first_name ?? $notifiable->nom]);
    }

    /** Kliyan ki kreye nan comptoir san e-mail gen yon adrès entèn (.local): pa voye. */
    protected function hasRealEmail(object $notifiable): bool
    {
        $email = (string) ($notifiable->email ?? '');

        return $email !== '' && !str_ends_with($email, '.local');
    }

    protected function signature(): string
    {
        return Agency::get('name') . ', ' . Agency::get('phone');
    }
}
