<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Voye SMS. Driver nan config/services.php (SMS_DRIVER):
 *  - log    : mesaj la ekri nan storage/logs/laravel.log (tès, devlopman)
 *  - twilio : voye vre atravè API Twilio
 */
class Sms
{
    /** Mete nimewo a nan fòma entènasyonal (+509XXXXXXXX). Retounen null si li pa valab. */
    public static function normalize(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if ($digits === '') {
            return null;
        }

        $country = (string) config('services.sms.country', '509');
        if (strlen($digits) === 8) {
            $digits = $country . $digits; // nimewo lokal ayisyen 8 chif
        }

        return strlen($digits) >= 10 ? '+' . $digits : null;
    }

    public static function send(string $to, string $message): void
    {
        $driver = config('services.sms.driver', 'log');

        if ($driver === 'twilio') {
            $c = config('services.sms.twilio');
            if (empty($c['sid']) || empty($c['token']) || empty($c['from'])) {
                throw new RuntimeException('Twilio n’est pas configuré (TWILIO_SID, TWILIO_TOKEN, TWILIO_FROM).');
            }

            $response = Http::asForm()->withBasicAuth($c['sid'], $c['token'])->timeout(15)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$c['sid']}/Messages.json", [
                    'To' => $to, 'From' => $c['from'], 'Body' => $message,
                ]);

            if ($response->failed()) {
                throw new RuntimeException('Twilio : ' . ($response->json('message') ?? $response->status()));
            }

            return;
        }

        Log::info("[SMS → {$to}] {$message}");
    }
}
