<?php

namespace App\Support;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Voye yon notifikasyon san janm bloke operasyon an (eg. si Gmail oswa Twilio pa reponn).
 * Retounen true si li pati, false si gen erè (erè a ekri nan log la).
 */
class Notifier
{
    public static function send(?object $notifiable, Notification $notification): bool
    {
        if (!$notifiable) {
            return false;
        }

        try {
            $notifiable->notify($notification);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Notification non envoyée', ['notification' => class_basename($notification), 'error' => $e->getMessage()]);

            return false;
        }
    }
}
