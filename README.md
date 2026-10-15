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
| Administrateur (`ADMIN`) | Tout le comptoir + parc, comptes clients et secrétaires, paiements, messages, caisse de tous les agents |
| Super administrateur (`SUPER_ADMIN`) | Tout ce que fait l’admin + comptes administrateurs, paramètres de l’agence, journal d’activité, rapports et export CSV, suppression de paiements et de locations |

Le premier super administrateur : à la migration, l’administrateur le plus ancien devient super administrateur. Pour promouvoir un autre compte : `php artisan app:super-admin email@exemple.ht`. Il reste toujours au moins un super administrateur actif (impossible de le suspendre, supprimer ou rétrograder).

Créer une secrétaire : Comptes → Créer un compte → rôle « Secrétaire ». Comptes de démo (DemoSeeder) : super admin `admin@gmail.com` / `123456`, admin `gerant@autogestion.ht` / `password`, secrétaire `secretaire@autogestion.ht` / `password`.

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

## Paramètres et journal

- **Paramètres de l’agence** (super admin) : coordonnées affichées sur le site et les tickets, % de frais de retard, caution proposée, modes de paiement acceptés, mention en bas des tickets.
- **Journal d’activité** : chaque action importante (confirmation, remise, retour, encaissement, prolongation, prix modifié, rôle changé, suppression, connexion du personnel) est enregistrée avec l’auteur, l’heure et l’IP.

## Notifications clients

| Quand | Message | Canal |
|---|---|---|
| L’agence confirme une location | Code de retrait, adresse, horaires | e-mail + SMS |
| La veille du retour (8 h) | Rappel de retour, lien pour prolonger | e-mail + SMS |
| Premier jour de retard (8 h) | Avis de retard, frais appliqués | e-mail + SMS |

- Activer / désactiver : Direction → Paramètres → Notifications (bouton « Envoyer un test »).
- E-mail : configuration `MAIL_*` du `.env` (ex. Gmail avec un mot de passe d’application).
- SMS : `SMS_DRIVER=log` écrit les SMS dans `storage/logs/laravel.log` (test). Pour un envoi réel : `SMS_DRIVER=twilio`, `TWILIO_SID`, `TWILIO_TOKEN`, `TWILIO_FROM`.
- Les clients créés au comptoir sans e-mail reçoivent seulement les SMS. Un échec d’envoi ne bloque jamais l’opération (détail dans le log).
- Commande manuelle : `php artisan loans:remind`.

## Sauvegardes

- Chaque nuit à 2 h : `php artisan app:backup` → `storage/app/backups/autogestion-AAAA-MM-JJ-HHMMSS.sql.gz` (MySQL ou SQLite, sans `mysqldump`).
- Conservation et copie par e-mail : Direction → Paramètres → Sauvegardes. Liste, téléchargement et sauvegarde immédiate : Direction → Sauvegardes.
- Restaurer : `php artisan app:restore nom-du-fichier.sql.gz` (une sauvegarde de sécurité de l’état actuel est faite avant).

## Tâches planifiées

Tout (retards, rappels, sauvegardes) passe par le planificateur Laravel, à lancer **chaque minute** :

- Linux : `* * * * * cd /chemin/vers/autogestion && php artisan schedule:run >> /dev/null 2>&1`
- Windows (PowerShell administrateur) :
  `schtasks /create /sc minute /mo 1 /tn "AutoGestion" /tr "cmd /c cd /d C:\chemin\autoGestionFinal && php artisan schedule:run"`
- En développement : `php artisan schedule:work`
