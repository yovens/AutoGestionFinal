<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Support\Agency;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('super.settings', ['settings' => Agency::all()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:60',
            'city'             => 'required|string|max:80',
            'address'          => 'required|string|max:160',
            'phone'            => 'required|string|max:30',
            'email'            => 'required|email|max:120',
            'hours'            => 'required|string|max:120',
            'hours_note'       => 'nullable|string|max:120',
            'map_query'        => 'nullable|string|max:120',
            'late_fee_percent' => 'required|integer|min:0|max:500',
            'default_deposit'  => 'required|integer|min:0',
            'methods'          => 'required|array|min:1',
            'methods.*'        => [Rule::in(array_keys(Transaction::METHODS))],
            'ticket_footer'    => 'nullable|string|max:200',
            'notify_mail'      => 'nullable|boolean',
            'notify_sms'       => 'nullable|boolean',
            'backup_keep_days' => 'required|integer|min:1|max:365',
            'backup_email'     => 'nullable|email|max:120',
        ], ['methods.required' => 'Gardez au moins un mode de paiement.'], [
            'name' => 'nom de l’agence', 'city' => 'ville', 'address' => 'adresse', 'phone' => 'téléphone',
            'hours' => 'horaires', 'late_fee_percent' => 'frais de retard', 'default_deposit' => 'caution',
        ]);

        $before = Agency::all();
        $data['methods'] = array_values(array_intersect(array_keys(Transaction::METHODS), $data['methods']));
        $data['hours_note'] ??= '';
        $data['map_query'] ??= '';
        $data['ticket_footer'] ??= '';
        $data['late_fee_percent'] = (int) $data['late_fee_percent'];
        $data['default_deposit'] = (int) $data['default_deposit'];
        $data['notify_mail'] = $request->boolean('notify_mail');
        $data['notify_sms'] = $request->boolean('notify_sms');
        $data['backup_keep_days'] = (int) $data['backup_keep_days'];
        $data['backup_email'] ??= '';

        Agency::set($data);

        $changed = collect($data)->filter(fn ($v, $k) => ($before[$k] ?? null) != $v)->keys();
        if ($changed->isNotEmpty()) {
            Audit::log('settings.update', 'Paramètres modifiés : ' . $changed->implode(', '), null,
                $changed->mapWithKeys(fn ($k) => [$k => [$before[$k] ?? null, $data[$k]]])->all());
        }

        return back()->with('success', 'Paramètres enregistrés.');
    }

    /** Voye yon mesaj tès bay super admin nan li menm (e-mail + SMS selon paramèt yo). */
    public function testNotification(Request $request)
    {
        $me = $request->user();
        $channels = (new \App\Notifications\TestNotice)->via($me);

        if (!$channels) {
            return back()->with('error', 'Aucun canal actif : activez les e-mails ou les SMS, et vérifiez votre e-mail / téléphone dans votre profil.');
        }

        $sent = \App\Support\Notifier::send($me, new \App\Notifications\TestNotice);
        $names = collect($channels)->map(fn ($c) => $c === 'mail' ? "e-mail ({$me->email})" : "SMS ({$me->telephone})")->implode(' et ');

        return $sent
            ? back()->with('success', "Test envoyé par {$names}." . (config('mail.default') === 'log' || config('services.sms.driver') === 'log' ? ' En mode « log », le message est écrit dans storage/logs/laravel.log.' : ''))
            : back()->with('error', 'L’envoi a échoué. Vérifiez la configuration MAIL / SMS du fichier .env (détail dans storage/logs/laravel.log).');
    }
}
