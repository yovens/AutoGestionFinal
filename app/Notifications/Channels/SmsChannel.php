<?php

namespace App\Notifications\Channels;

use App\Support\Sms;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $to = Sms::normalize($notifiable->routeNotificationFor('sms', $notification));

        if ($to && method_exists($notification, 'toSms')) {
            Sms::send($to, $notification->toSms($notifiable));
        }
    }
}
