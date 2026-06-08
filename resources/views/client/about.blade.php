<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>À propos | AutoGestion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <?php
        if (!function_exists('route')) {
            function route($name, $params = []) { 
                if (str_contains($name, 'dashboard')) return '/dashboard';
                if (str_contains($name, 'vehicles')) return '/vehicles';
                if (str_contains($name, 'cart')) return '/cart';
                if (str_contains($name, 'loan')) return '/loan';
                if (str_contains($name, 'transactions')) return '/transactions';
                if (str_contains($name, 'contact')) return '/contact';
                if (str_contains($name, 'logout')) return '/logout';
                return '#'; // Route par défaut
            }
        }
    ?>

    <style>
        /* =================================================================== */
        /* === 1. Variables Globales (Dark Mode par défaut) ================== */
        /* =================================================================== */
        :root {
            --primary-color: #FF6B6B; /* Corail Vif */
            --secondary-color: #4ECDC4; /* Cyan Vif */
            --text-light: #EAEFF4; 
            --text-muted: #94a3b8; 
            --bg-page: #1F2937; /* Fond : Bleu Nuit Profond */
            --card-bg: #2C3E50; /* Fond des cartes et Topbar */
            --content-card-bg: var(--card-bg); /* Carte de contenu en mode sombre */
            --content-text-color: var(--text-light); /* Texte clair sur les cartes de contenu */
            --content-strong-color: #EAEFF4; 
            --topbar-height: 65px;
            
            --header-text-color: var(--primary-color);
        }

        /* =================================================================== */
        /* === 1.1 Variables Light Mode ====================================== */
        /* =================================================================== */
        .light-mode {
            --primary-color: #007bff; /* Bleu Primaire */
            --secondary-color: #28a745; /* Vert Secondaire */
            --text-light: #343a40; /* Texte sombre */
            --text-muted: #6c757d; 
            --bg-page: #f8f9fa; /* Fond : Très clair */
            --card-bg: #ffffff; /* Fond Topbar Clair */
            --content-card-bg: #ffffff; /* Carte de contenu en mode clair (blanc) */
            --content-text-color: #1F2937; /* Texte sombre sur les cartes blanches */
            --content-strong-color: #343a40; 
            
            --header-text-color: var(--primary-color);
        }

        /* Styles de base */
        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif; 
            background-color: var(--bg-page);
            min-height: 100vh;
            color: var(--text-light);
            padding-top: var(--topbar-height); 
            transition: background-color 0.3s, color 0.3s;
        }
        
        /* =================================================================== */
        /* === 2. Topbar (Navigation Adaptative) ============================= */
        /* =================================================================== */
        .topbar {
            height: var(--topbar-height);
            background: var(--card-bg);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); 
            display: flex;
            align-items: center;
            justify-content: space-between; /* Pour l'espace entre le menu et les actions */
            padding: 0 40px;
            position: fixed; 
            top: 0;
            left: 0;
            right: 0;
            z-index: 1020;
            transition: background 0.3s, box-shadow 0.3s;
        }
        .logo {
            font-size: 22px; 
            font-weight: 700;
              color: var(--primary-color); 
            text-decoration: none;
            margin-right: 40px;
            transition: color 0.3s;
        }
        .logo i { color: var(--primary-color); margin-right: 5px; }

        .topbar-nav { 
            display: flex;
            align-items: center;
            margin-right: auto;
        }
        
        .topbar-nav a {
            color: var(--text-muted);
            font-weight: 500;
            padding: 8px 15px;
            text-decoration: none;
            transition: color 0.3s, border-bottom 0.3s;
            border-bottom: 3px solid transparent;
            margin: 0 5px;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 6px; 
        }

        .topbar-nav a:hover, .topbar-nav a.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
        }

        .topbar-actions { display: flex; align-items: center; gap: 15px; }

        .btn-logout {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            background-color: transparent;
            padding: 6px 15px; 
            font-size: 0.9rem;
            border-radius: 6px;
            transition: all 0.2s;
            text-transform: uppercase;
        }
        .btn-logout:hover {
            background-color: var(--primary-color);
            color: var(--bg-page);
            box-shadow: 0 4px 10px rgba(255, 107, 107, 0.4);
        }

        /* Bouton de bascule de thème */
        .btn-theme-toggle {
            background: none; border: none; color: var(--text-muted); font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s, transform 0.2s;
        }
        .btn-theme-toggle:hover { color: var(--primary-color); transform: scale(1.1); }
        
        /* =================================================================== */
        /* === 3. Styles Spécifiques À Propos (Carte Adaptative) ============= */
        /* =================================================================== */
        .main-content {
            padding: 40px 40px 80px 40px;
        }

        .main-content h2 {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--header-text-color); /* Adaptatif */
            margin-bottom: 30px;
            border-bottom: 3px solid var(--card-bg); /* Adaptatif */
            padding-bottom: 15px;
            transition: color 0.3s, border-color 0.3s;
        }
        
        /* Carte de contenu (remplace .white-card) */
        .content-card {
            background-color: var(--content-card-bg); /* Adaptatif */
            color: var(--content-text-color); /* Adaptatif */
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.3);
            margin-bottom: 30px;
            min-height: 150px;
            transition: background-color 0.3s, color 0.3s, transform 0.3s;
        }
        .content-card:hover {
            transform: translateY(-3px);
            box-shadow: 0px 15px 40px rgba(0,0,0,0.4);
        }
        
        .content-card h3 {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .icon-large {
            color: var(--secondary-color);
            font-size: 1.2em;
        }

        .text-strong {
            font-weight: 600;
            color: var(--content-strong-color); /* Adaptatif */
        }

        /* Override text-muted for cards */
        .content-card .text-muted {
            color: var(--text-muted) !important;
        }

    </style>
</head>
<body class="dark-mode">

<header class="topbar">
    <a href="{{ route('client.dashboard') }}" class="logo">
       AutoGestion
    </a>
    
    <nav class="topbar-nav">
        <a href="{{ route('client.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
        <a href="{{ route('client.vehicles') }}"><i class="fas fa-car"></i> Véhicules</a>
        <a href="{{ route('client.cart') }}"><i class="fas fa-shopping-cart"></i> Panier</a>
        <a href="{{ route('client.loan') }}"><i class="fas fa-key"></i> Locations</a>
        <a href="{{ route('client.transactions') }}"><i class="fas fa-exchange-alt"></i> Transactions</a>
        <a href="{{ route('client.about') }}" class="active"><i class="fas fa-info-circle"></i> À Propos</a>
        <a href="{{ route('client.contact') }}"><i class="fas fa-headset"></i> Contact</a> 
    </nav>
    
    <div class="topbar-actions">
        {{-- Bouton de Bascule de Thème --}}
        <button id="theme-toggle" class="btn-theme-toggle" aria-label="Basculer le thème clair/sombre">
            <i class="fas fa-sun"></i> 
        </button>

        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-logout">
                <i class="fas fa-power-off"></i> DÉCONNEXION
            </button>
        </form>
    </div>
</header>

<main class="main-content">
<br>
<br>
    <h2 class="mb-4">À Propos de Notre Entreprise</h2>

    {{-- Utilisation de la nouvelle classe content-card --}}
    <div class="content-card">
        <p class="fs-5 text-strong">
            Bienvenue chez AutoGestion ! Nous sommes spécialisés dans la vente et la location de véhicules de toutes catégories : voitures, camions, motos, et même des voitures de course.
        </p>
        <p class="text-strong">
            Nous nous engageons à offrir un service premium, sécurisé et rapide à tous nos clients.
        </p>
        <p class="text-muted fst-italic">Merci de nous faire confiance 🚗✨</p>
    </div>
    
    <div class="row">
        
        <div class="col-md-6">
            {{-- Utilisation de la nouvelle classe content-card --}}
            <div class="content-card">
                <h3><i class="icon-large fas fa-bullseye"></i> Notre But</h3>
                <p>Notre but principal est de faciliter la mobilité et l'accès à la propriété de véhicules pour la population haïtienne. Nous voulons être la plateforme de référence offrant la plus grande diversité de choix, avec une transparence totale des prix.</p>
                
                <hr>
                
                <h3><i class="icon-large fas fa-rocket"></i> Notre Mission</h3>
                <p>Notre mission est de simplifier le processus d'achat et de location de véhicules grâce à une interface utilisateur intuitive et des transactions entièrement numérisées et sécurisées, tout en maintenant un service client exceptionnel et humain.</p>
            </div>
        </div>

        <div class="col-md-6">
            {{-- Utilisation de la nouvelle classe content-card --}}
            <div class="content-card">
                <h3><i class="icon-large fas fa-calendar-alt"></i> Notre Histoire</h3>
                <p>Fondée en 2025 par un groupe de passionnés d'automobile et de technologie, AutoGestion est née de la volonté de moderniser le marché local du véhicule. En trois ans, nous sommes passés d'une petite flotte à un catalogue étendu couvrant tous les besoins de transport.</p>
                
                <hr>

                <h3><i class="icon-large fas fa-map-marker-alt"></i> Notre Adresse</h3>
                <p class="text-strong">
                    Siège Social (Bureau Administratif) : <br>
                    #123, Rue des Véhicules <br>
                     Simon , Cayes, Haïti <br>
                </p>
                <p>
                    <small class="text-muted">Visite sur rendez-vous uniquement pour les transactions importantes.</small>
                </p>
                





            </div>
        </div>
    </div>
    

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
{{-- Ajout du script de gestion de thème --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleButton = document.getElementById('theme-toggle');
        const body = document.body;
        
        if (!toggleButton) return; 
        const icon = toggleButton.querySelector('i');

        const applyTheme = (isLight) => {
            if (isLight) {
                body.classList.add('light-mode');
                body.classList.remove('dark-mode');
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon'); 
                localStorage.setItem('theme', 'light');
            } else {
                body.classList.remove('light-mode');
                body.classList.add('dark-mode');
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun'); 
                localStorage.setItem('theme', 'dark');
            }
        };

        // 1. Charger le thème sauvegardé, sinon utiliser le thème sombre par défaut
        const savedTheme = localStorage.getItem('theme');
        // Initialiser avec dark-mode par défaut, car c'était le style initial du code fourni
        if (savedTheme === 'light') {
            applyTheme(true);
        } else {
            applyTheme(false); 
        }

        // 2. Écouter le clic du bouton pour basculer
        toggleButton.addEventListener('click', () => {
            const isLight = body.classList.contains('light-mode');
            applyTheme(!isLight);
        });
    });
</script>
</body>
</html>