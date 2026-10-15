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

## Rôles

| Rôle | Accès |
|---|---|
| Client | Catalogue, réservations, code de retrait, reçus et bons |
| Secrétaire (`SECRETAIRE`) | Comptoir : confirmer les demandes, remise des clés, retours, ventes, clients sur place, tickets, sa caisse du jour |
| Administrateur (`ADMIN`) | Tout le comptoir + parc, comptes, paiements, messages, caisse de tous les agents |

Créer une secrétaire : Comptes → Créer un compte → rôle « Secrétaire ». Compte de démo (DemoSeeder) : `secretaire@autogestion.ht` / `password`.

## Le comptoir, pas à pas

1. Le client réserve en ligne → demande « En attente ».
2. L’agence confirme → statut « À récupérer » et **code de retrait** (ex. `K7P-29Q`) visible dans l’espace client.
3. Le client se présente : la secrétaire tape le code dans le Comptoir → **remise des clés** : pièce d’identité vérifiée, kilométrage, carburant, état, caution, encaissement (espèces, MonCash, NatCash, carte, virement) → **bon de sortie** imprimable (80 mm).
4. Retour : kilométrage et carburant, état, frais de retard calculés automatiquement, autres frais, caution rendue ou retenue, encaissement du solde → **bon de retour**. Le véhicule redevient disponible.
5. Achat réservé en ligne : la secrétaire encaisse → **bon de remise**.
6. Client sans réservation : « Location sur place » (création du client si besoin) puis remise des clés.
7. Caisse du jour : total par mode de paiement à comparer avec l’argent en caisse.

## Règles métier

Toute la logique de stock, de location et de vente est dans `app/Services/FleetService.php`. Les contrôleurs admin et client (et bientôt secrétaire) passent par ce service.

| Étape | Effet sur le stock | Paiement |
|---|---|---|
| Le client demande une location (`pending`) | −1 | — |
| L’agence confirme (`ready`, code de retrait) | — | — |
| L’agence refuse (`rejected`) ou le client ne vient pas (`cancelled`) | +1 | — |
| Remise des clés (`approved`) | — | location encaissée + bon de sortie |
| Date de fin dépassée (`expired`) | — | — |
| Prolongation au comptoir | — | supplément encaissé |
| Prolongation demandée en ligne et acceptée | — | supplément « à encaisser » au retour |
| Retour du véhicule (`returned`) | +1 | solde + frais encaissés + bon de retour |
| Le client réserve un achat | −1 | achat « à encaisser » |
| Encaissement au comptoir | — | achat réglé + bon de remise |
| Annulation de l’achat | +1 | achat annulé |

Les opérations de stock sont faites en transaction avec verrou (`lockForUpdate`) pour éviter deux réservations du même dernier véhicule.

## Interface

- Aucun build front nécessaire : `public/css/app.css` et `public/js/app.js` sont chargés directement.
- Police Archivo hébergée localement (`public/fonts`, licence OFL) : fonctionne sans Google Fonts.
- Composants Blade dans `resources/views/components` : `x-plate`, `x-badge`, `x-status`, `x-field`, `x-dialog`, `x-empty`, `x-icon`, `x-avatar`, `x-vehicle-card`.
- Layouts : `layouts.admin`, `layouts.client`, `layouts.guest`.
- Thème clair, sombre ou selon l’appareil (menu du compte).
- Messages de validation en français : `lang/fr`.

## Prochaines étapes

- Rôle super administrateur.
