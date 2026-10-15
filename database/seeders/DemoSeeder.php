<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\LoanCart;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\FleetService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Done pou demonstrasyon / tès (machin, kliyan, lokasyon, peman).
 *   php artisan db:seed --class=DemoSeeder
 * Pa itilize li sou sèvè pwodiksyon an.
 */
class DemoSeeder extends Seeder
{
    public function run(FleetService $fleet): void
    {
        $this->call(AdminUserSeeder::class);

        $cars = [
            ['Jeep', 'Wrangler Sahara', 'AA-21457', 2023, 46500, 95, 3, 'wrangler'],
            ['Jeep', 'Wrangler Rubicon', 'AB-30982', 2022, 52000, 110, 2, 'wrangler-rubicon'],
            ['Jeep', 'Gladiator', 'AC-11804', 2022, 49000, 105, 1, 'gladiator'],
            ['Jeep', 'Grand Cherokee', 'AD-77310', 2021, 41000, 85, 2, 'grand-cherokee'],
            ['Mercedes-Benz', 'Classe C 300', 'AE-45021', 2023, 47000, 90, 2, 'classe-c'],
            ['Mercedes-Benz', 'GLE 450', 'AF-62290', 2022, 68000, 140, 1, 'gle'],
            ['Mercedes-Benz', 'GLC Coupé', 'AG-19734', 2023, 59000, 120, 1, 'glc-coupe'],
            ['Mercedes-Benz', 'GLE Coupé AMG', 'AH-80455', 2022, 89000, 185, 1, 'gle-coupe'],
            ['Mercedes-Benz', 'Classe S 500', 'AJ-33107', 2021, 98000, 210, 1, 'classe-s'],
            ['Mercedes-AMG', 'GT C', 'AK-90012', 2020, 115000, 260, 1, 'amg-gt'],
            ['Porsche', 'Macan S', 'AL-56789', 2022, 72000, 150, 1, 'macan'],
            ['Porsche', '911 Turbo', 'AM-40321', 2019, 135000, 290, 1, '911-turbo'],
            ['Bentley', 'Bentayga', 'AN-10077', 2021, 165000, 340, 1, 'bentayga'],
        ];

        foreach ($cars as [$brand, $model, $plate, $year, $price, $day, $qty, $img]) {
            $path = "vehicles/demo-{$img}.jpg";
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, file_get_contents(__DIR__ . "/demo/{$img}.jpg"));
            }

            Vehicle::updateOrCreate(['plate' => $plate], [
                'brand' => $brand, 'model' => $model, 'year' => $year, 'image' => $path,
                'price' => $price, 'loan_price' => $day, 'quantity' => $qty, 'status' => $qty > 0,
            ]);
        }

        if (User::where('role', User::CLIENT)->exists()) {
            return; // déjà peuplé
        }

        $people = [
            ['Jean-Marc Desir', 'jm.desir@example.ht', '+509 37 12 44 80'],
            ['Nadège Pierre', 'nadege.pierre@example.ht', '+509 48 90 11 23'],
            ['Wilson Jean-Baptiste', 'wilson.jb@example.ht', '+509 34 55 02 18'],
            ['Carline Augustin', 'carline.augustin@example.ht', '+509 31 76 29 04'],
            ['Ricardo Louis', 'ricardo.louis@example.ht', '+509 42 03 88 61'],
        ];

        $clients = collect($people)->map(fn ($p) => User::create([
            'nom' => $p[0], 'email' => $p[1], 'telephone' => $p[2],
            'password' => Hash::make('password'), 'role' => User::CLIENT, 'status' => User::ACTIVE,
        ]));

        $v = fn ($plate) => Vehicle::where('plate', $plate)->value('id');

        // Lokasyon an kou
        $l1 = $fleet->createApprovedLoan($clients[0]->id, $v('AA-21457'), 7);
        $this->backdate($l1, 5);

        // Lokasyon an reta
        $l2 = $fleet->createApprovedLoan($clients[1]->id, $v('AE-45021'), 4);
        $this->backdate($l2, 6);

        // Demann k ap tann
        $fleet->requestLoan($clients[2], $v('AB-30982'), 3);
        $fleet->requestLoan($clients[0], $v('AL-56789'), 2);

        // Prolongasyon mande
        $l3 = $fleet->createApprovedLoan($clients[3]->id, $v('AD-77310'), 5);
        $this->backdate($l3, 3);
        $fleet->requestRenewal($l3->fresh(), 3);

        // Lokasyon fini (retounen)
        $l4 = $fleet->createApprovedLoan($clients[4]->id, $v('AF-62290'), 3);
        $this->backdate($l4, 40);
        $fleet->returnLoan($l4->fresh());

        // Acha
        $t1 = $fleet->requestPurchase($clients[4], $v('AC-11804'));
        $fleet->confirmSale($t1);
        $t1->forceFill(['created_at' => now()->subMonths(2)])->save();
        $fleet->requestPurchase($clients[2], $v('AJ-33107'));

        // Kèk peman nan mwa pase yo pou grafik la
        foreach ([5 => 14800, 4 => 9200, 3 => 21500, 1 => 12600] as $months => $amount) {
            Transaction::create([
                'user_id' => $clients[$months % 5]->id, 'vehicle_id' => $v('AA-21457'),
                'amount' => $amount, 'type' => Transaction::RENTAL, 'status' => Transaction::COMPLETED,
            ])->forceFill(['created_at' => now()->subMonths($months)->setDay(12)])->save();
        }

        Contact::create(['user_id' => $clients[1]->id, 'subject' => 'Retour du véhicule', 'message' => "Bonjour, je serai en retard de deux jours pour rendre la Classe C. Est-ce possible de prolonger sur place à l'agence ?"]);
        Contact::create(['user_id' => $clients[2]->id, 'subject' => 'Paiement de la Classe S', 'message' => 'Est-ce que je peux payer en deux fois pour la Classe S réservée ?', 'is_read' => true]);
    }

    private function backdate(LoanCart $loan, int $daysAgo): void
    {
        $start = Carbon::today()->subDays($daysAgo);
        $loan->update(['start_date' => $start, 'end_date' => $start->copy()->addDays($loan->duration_days)]);
        $loan->transactions()->update(['created_at' => $start]);
        $loan->forceFill(['created_at' => $start])->save();
    }
}
