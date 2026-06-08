🚗 AutoGestion

Système complet de gestion de véhicules développé avec Laravel, permettant la gestion des ventes, locations, clients et transactions dans une interface admin moderne.

📌 Table des matières
À propos
Fonctionnalités
Technologies
Installation
Configuration
Structure du projet
Modules
Routes principales
Sécurité
Améliorations futures
Licence
Auteur
📖 À propos

AutoGestion est une application web de gestion de flotte de véhicules permettant :

Gestion des véhicules (CRUD)
Vente de véhicules
Location de véhicules
Suivi des clients
Gestion des transactions
Interface admin moderne (dark mode)
Interface client intuitive
✨ Fonctionnalités
🚗 Gestion des véhicules
Ajouter / modifier / supprimer un véhicule
Upload d’image
Prix de vente et location
Statut de disponibilité
Gestion du stock (quantity)
👤 Clients
Inscription / connexion
Consultation des véhicules
Location de véhicules
Achat de véhicules
Historique personnel
📦 Locations
Création de location
Durée personnalisée
Calcul automatique du prix
Historique des locations
💰 Transactions
Suivi des paiements
Historique des achats
Liaison client ↔ véhicule
🧑‍💼 Admin
Dashboard complet
Gestion des véhicules
Gestion des clients
Suivi des locations
Suivi des ventes
🛠️ Technologies utilisées
Laravel (PHP Framework)
Blade (Template Engine)
MySQL (Base de données)
Bootstrap / CSS personnalisé
JavaScript (modales et interactions)
Laravel Storage (images véhicules)
⚙️ Installation
git clone https://github.com/ton-repo/autogestion.git
cd autogestion

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan storage:link

php artisan serve
🔧 Configuration (.env)
APP_NAME=AutoGestion
APP_ENV=local
APP_KEY=base64:xxx
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=autogestion
DB_USERNAME=root
DB_PASSWORD=
🚗 Modules principaux
🔹 Véhicules
CRUD complet
Upload d’images
Gestion stock
Prix vente / location
🔹 Locations
Création de location
Calcul automatique
Historique
🔹 Achats
Achat immédiat
Enregistrement transaction
🔹 Clients
Interface utilisateur
Historique personnel
🔹 Admin
Gestion complète du système
🔐 Sécurité
Authentification Laravel
Middleware admin / client
Protection CSRF
Validation des formulaires
Gestion des rôles et accès
📊 Améliorations futures

🔥 Dashboard statistiques (revenus, ventes, locations)
🔥 API mobile (Flutter / React Native)
🔥 Paiement en ligne (Stripe / Mobile Money)
🔥 Notifications (email / SMS)
🔥 Chat client-admin
🔥 Système de réservation avancé
🔥 Graphiques dynamiques

🧠 Objectif du projet

Créer une plateforme complète de gestion de véhicules comme :

Uber Fleet
Système de location SaaS
Marketplace de véhicules
📄 Licence

Ce projet est sous licence MIT.

🚀 Auteur

Développé par Jocelyn Youvens Dions

