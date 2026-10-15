# AutoGestion

Application Laravel de location et de vente de véhicules pour l’agence de Simon (Les Cayes) : catalogue client, demandes de location, achats réservés, suivi des retours et des paiements côté administration.

## Installation

```bash
composer install
cp .env.example .env          # puis renseigner DB_*, MAIL_*, GOOGLE_*
php artisan key:generate
php artisan migrate
php artisan db:seed           # crée le compte admin (admin@gmail.com / 123456 — à changer)
php artisan storage:link
php artisan serve
```

Données de démonstration (13 véhicules avec photos, 5 clients, locations et paiements) :

```bash
php artisan db:seed --class=DemoSeeder     # mot de passe des clients : password
```

### Tâche planifiée

Les locations dont la date de fin est dépassée passent en « Délai dépassé » chaque nuit. Sur le serveur, ajoutez une fois :

```
* * * * * cd /chemin/vers/autogestion && php artisan schedule:run >> /dev/null 2>&1
```

En local : `php artisan loans:sync-expired` (le statut est aussi recalculé à l’ouverture des pages de location).

## Règles métier

Toute la logique de stock, de location et de vente est dans `app/Services/FleetService.php`. Les contrôleurs admin et client (et bientôt secrétaire) passent par ce service.

| Étape | Effet sur le stock | Paiement |
|---|---|---|
| Le client demande une location (`pending`) | −1 | — |
| L’admin approuve (`approved`) | — | location enregistrée, réglée |
| L’admin refuse (`rejected`) | +1 | — |
| Date de fin dépassée (`expired`) | — | — |
| Prolongation demandée puis acceptée | — | supplément enregistré |
| Retour du véhicule (`returned`) | +1 | — |
| Le client réserve un achat | −1 | achat « à confirmer » |
| L’admin confirme le paiement | — | achat réglé |
| L’admin annule l’achat | +1 | achat annulé |

Les opérations de stock sont faites en transaction avec verrou (`lockForUpdate`) pour éviter deux réservations du même dernier véhicule.

## Interface

- Aucun build front nécessaire : `public/css/app.css` et `public/js/app.js` sont chargés directement.
- Police Archivo hébergée localement (`public/fonts`, licence OFL) : fonctionne sans Google Fonts.
- Composants Blade dans `resources/views/components` : `x-plate`, `x-badge`, `x-status`, `x-field`, `x-dialog`, `x-empty`, `x-icon`, `x-avatar`, `x-vehicle-card`.
- Layouts : `layouts.admin`, `layouts.client`, `layouts.guest`.
- Thème clair, sombre ou selon l’appareil (menu du compte).
- Messages de validation en français : `lang/fr`.

## Prochaines étapes

- Rôle secrétaire : tickets de remise et de retour des véhicules (le gabarit `.ticket` est déjà dans le CSS).
- Rôle super administrateur.
