<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoGestion Pro — Location & Vente Corporate</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        /* === 1. PALETTE DE COULEURS CLASSIQUE / LIGHT === */
        :root {
            --brand-dark: #f8fafc;        
            --brand-surface: #ffffff;     
            --brand-border: #e2e8f0;      
            --accent-teal: #0d9488;       
            --accent-hover: #0f766e;      
            --text-main: #0f172a;         
            --text-muted: #64748b;        
            --font-sans: 'Inter', sans-serif;
            --font-serif: 'Playfair Display', serif;
        }

        body {
            margin: 0;
            font-family: var(--font-sans);
            background-color: var(--brand-dark);
            color: var(--text-main);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* === HEADER NAV === */
        .topbar {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 20px 60px;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.85);
            border-bottom: 1px solid var(--brand-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .topbar .logo {
            font-family: var(--font-serif);
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
            color: var(--text-main);
            text-decoration: none;
        }

        .topbar .logo span {
            color: var(--accent-teal);
        }

        .topbar nav a {
            margin-left: 30px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: color 0.3s;
        }

        .topbar nav a:hover {
            color: var(--text-main);
        }

        .topbar a.btn-cta {
            background: transparent;
            color: var(--accent-teal);
            padding: 10px 22px;
            border-radius: 6px;
            border: 1px solid var(--accent-teal);
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .topbar a.btn-cta:hover {
            background: var(--accent-teal);
            color: #ffffff;
        }
     

        /* === SECTION HERO === */
.hero {
    height: 65vh; 
    min-height: 500px; 
    padding-top: 80px; 
    background: linear-gradient(rgba(248, 250, 252, 0.45), rgba(248, 250, 252, 0.75)), 
                url("{{ asset('images/Gemini_Generated_Image_5fxjx75fxjx75fxj.png') }}") center/cover no-repeat;
    display: flex;
    align-items: center;
    position: relative;
    padding-left: 8%;
}

/* PANNEAU TEXTE FLOU */
.hero-content {
    max-width: 700px;
    background: rgba(254, 254, 255, 0.65); /* Équilibre parfait pour le contraste */
    padding: 40px; /* Donne de l'air en haut et en bas pour décadrer le badge */
    border-radius: 16px;
    backdrop-filter: blur(10px); 
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
}

/* TYPOGRAPHIES HERO */
.hero-content h1 {
    font-family: var(--font-serif);
    font-size: 3.5rem; 
    line-height: 1.2;
    margin-bottom: 20px;
    color: #090d16; /* Noir profond ultra-lisible */
    font-weight: 700;
}

.hero-content h1 span {
    font-style: italic;
    color: var(--accent-teal);
}

.hero-content p {
    font-size: 17px;
    color: #1e293b; /* Gris ardoise foncé pour un contraste doux */
    line-height: 1.6;
    margin-bottom: 30px;
    font-weight: 500;
}

/* === GROUPEMENT DES BOUTONS === */
.btn-premium-group {
    display: flex;
    gap: 15px;
}

/* Base commune des boutons */
.btn-pro {
    padding: 14px 34px;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    border-radius: 6px;
    transition: all 0.3s ease;
    text-decoration: none;
}

/* Bouton Principal (Vert/Turquoise) */
.btn-pro-primary {
    background: var(--accent-teal);
    color: #fff;
    border: 1px solid var(--accent-teal);
}
.btn-pro-primary:hover {
    background: var(--accent-hover);
    border-color: var(--accent-hover);
    transform: translateY(-2px);
}

/* Bouton Secondaire (Blanc épuré) */
.btn-pro-secondary {
    background: #ffffff;
    color: var(--text-main);
    border: 1px solid var(--brand-border);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    display: inline-flex; /* Centre parfaitement le texte horizontalement et verticalement */
    align-items: center;
    justify-content: center;
    line-height: 1; /* Évite les décalages de hauteur de ligne */
}
.btn-pro-secondary:hover {
    background: #f8fafc;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

        /* === SECTIONS GENERALS === */
        section {
            padding: 100px 0;
            border-bottom: 1px solid var(--brand-border);
        }

        .section-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--accent-teal);
            margin-bottom: 12px;
            display: block;
            font-weight: 600;
        }

        .section-title-pro {
            font-family: var(--font-serif);
            font-size: 36px;
            margin-bottom: 60px;
            color: var(--text-main);
        }

        /* === SERVICE CARDS === */
        .feature-box {
            background: var(--brand-surface);
            border: 1px solid var(--brand-border);
            padding: 40px;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }
        .feature-box:hover {
            border-color: var(--accent-teal);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }
        .feature-box i {
            font-size: 32px;
            color: var(--accent-teal);
            margin-bottom: 24px;
        }
        .feature-box h4 {
            font-size: 20px;
            margin-bottom: 12px;
            color: var(--text-main);
        }
        .feature-box p {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }

        /* === FLOTTE/VEHICLES CARDS === */
        .showcase-card {
            background: var(--brand-surface);
            border-radius: 12px;
            border: 1px solid var(--brand-border);
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .showcase-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.08);
        }

        .showcase-img-wrapper {
            position: relative;
            height: 240px;
            overflow: hidden;
        }

        .showcase-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .showcase-body {
            padding: 24px;
        }

        .showcase-body h5 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .showcase-price {
            font-size: 15px;
            color: var(--accent-teal);
            font-weight: 600;
        }

        /* === CONTACT BLOCK === */
        .contact-grid {
            background: var(--brand-surface);
            border: 1px solid var(--brand-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .contact-details {
            padding: 50px;
        }

        .contact-item {
            margin-bottom: 30px;
        }

        .contact-item h6 {
            font-size: 12px;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 1px;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .contact-item p {
            font-size: 16px;
            margin: 0;
            color: var(--text-main);
        }

        /* === TESTIMONIALS === */
        .review-card {
            padding: 30px;
            border-radius: 8px;
            border-left: 3px solid var(--accent-teal);
            background: var(--brand-surface);
            border-top: 1px solid var(--brand-border);
            border-right: 1px solid var(--brand-border);
            border-bottom: 1px solid var(--brand-border);
            box-shadow: 0 2px 4px rgba(0,0,0,0.01);
        }
        .review-card p {
            font-style: italic;
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.6;
        }
.theme-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 40px;
    background: var(--brand-surface);
    border: 1px solid var(--brand-border);
    border-radius: 8px;
    padding: 10px;
    z-index: 1001;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.theme-dropdown.show { display: block; }

.theme-dropdown button {
    display: block;
    width: 100%;
    padding: 8px 15px;
    border: none;
    background: transparent;
    text-align: left;
    color: var(--text-main);
    cursor: pointer;
}
.theme-dropdown button:hover { background: var(--brand-dark); }
        footer {
            padding: 40px 60px;
            background: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: var(--text-muted);
            border-top: 1px solid var(--brand-border);
        }

        @media (max-width: 991px) {
            .topbar { padding: 20px 30px; }
            .hero { padding-left: 4%; }
            .hero-content h1 { font-size: 2.8rem; }
            footer { flex-direction: column; gap: 15px; text-align: center; }
        }
        /* === DARK MODE VARIABLES === */
body.dark-mode {
    --brand-dark: #0f172a;        /* Fond paj la */
    --brand-surface: #1e293b;     /* Fond kat yo */
    --brand-border: #334155;      /* Bordure yo */
    --text-main: #f8fafc;         /* Koulè tèks prensipal */
    --text-muted: #94a3b8;        /* Koulè tèks segondè */
}

/* Ajoute tranzisyon dous pou tout eleman yo */
body {
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Mizajou espesifik pou header la ka gen bon kontras */
body.dark-mode .topbar {
    background: rgba(15, 23, 42, 0.85);
    border-bottom: 1px solid var(--brand-border);
}
/* Asire bouton theme a pa gen style ki anpeche l aliyen */
.btn-theme {
    padding: 8px;
    border-radius: 50%;
    transition: background 0.3s;
    font-size: 16px;
}

.btn-theme:hover {
    background: var(--brand-border);
}

/* Pou dropdown la pa parèt anba bouton an twò lwen */
.theme-dropdown {
    top: 45px; /* Ajiste selon wotè header ou a */
    right: 0;
}
.theme-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 50px; /* Ajiste selon wotè header ou */
    background: var(--brand-surface);
    border: 1px solid var(--brand-border);
    border-radius: 6px; /* Pi piti bor awondi */
    padding: 5px 0;    /* Diminye espas anwo/anba */
    z-index: 1001;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    min-width: 130px;  /* Limit lajè a */
}

.theme-dropdown.show { display: block; }

.theme-dropdown button {
    display: flex;
    align-items: center;
    gap: 8px;          /* Espas ant ikon ak tèks */
    width: 100%;
    padding: 6px 15px; /* Diminye padding entèn */
    border: none;
    background: transparent;
    text-align: left;
    color: var(--text-main);
    cursor: pointer;
    font-size: 13px;   /* Diminye gwosè tèks */
    transition: background 0.2s;
}

.theme-dropdown button:hover { 
    background: rgba(0,0,0,0.05); /* Efè hover pi lejè */
}

.theme-dropdown button i {
    font-size: 12px;   /* Diminye gwosè ikon yo */
    width: 16px;       /* Aliman tout ikon yo sou menm aks */
    text-align: center;
}
body.dark-mode .topbar .logo { color: #ffffff; }
body.dark-mode .hero-content { background: rgba(30, 41, 59, 0.7); }
body.dark-mode .hero-content h1 { color: #ffffff; }
body.dark-mode footer { background: var(--brand-dark); border-top: 1px solid var(--brand-border); }
    </style>
</head>
<body>

<header class="topbar">
    <a href="#" class="logo">AutoGestion<span>.</span></a>
    <nav class="d-none d-md-flex align-items-center">
        <a href="#services">Services</a>
        <a href="#flotte">La Flotte</a>
        <a href="#agence">L'Agence</a>
        <a href="#avis">Avis</a>
        
        <a href="{{ route('login') }}" class="btn-cta">Espace Pro</a>
        
        <div style="position: relative;" class="ms-3">
   
    

            

        </div>
    </nav>
</header>
<br>
<br>
<br>

<section class="hero">
    <div class="hero-content">
        <span class="section-label">Mobilité d'exception</span>
        <h1>Nouvelle ère de gestion de flottes & <span>Prestige</span>.</h1>
        <p>Solutions de transport corporatives et locations haut de gamme sur mesure pour entreprises exigeantes et particuliers sélectifs.</p>
        
        <div class="btn-premium-group">
            <a href="#flotte" class="btn-pro btn-pro-primary">Explorer la flotte</a>
            <a href="{{ route('register') }}" class="btn-pro btn-pro-secondary">Créer un compte</a>
        </div>
    </div>
</section>

<section id="services" class="container">
    <span class="section-label">Expertise</span>
    <h2 class="section-title-pro">Nos Services Corporatifs</h2>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="feature-box">
                <i class="fa-solid fa-briefcase"></i>
                <h4>Solutions Business</h4>
                <p>Gestion simplifiée pour les déplacements de vos collaborateurs et transferts aéroport VIP d'affaires.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-box">
                <i class="fa-solid fa-gem"></i>
                <h4>Flotte Prestige</h4>
                <p>Accès exclusif à des véhicules de luxe d'une propreté millimétrée pour vos événements majeurs.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-box">
                <i class="fa-solid fa-headset"></i>
                <h4>Conciergerie 24/7</h4>
                <p>Un gestionnaire de compte dédié pour répondre instantanément à toutes vos contraintes logistiques.</p>
            </div>
        </div>
    </div>
</section>

<section class="container" id="flotte">
    <span class="section-label">Sélection exclusive</span>
    <h2 class="section-title-pro">Flotte Véhicules Premium</h2>

    <div class="row g-4">
        @foreach([
            ['img' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&w=800&q=80', 'name' => 'Mercedes-Benz GLE Coupe', 'price' => '120 USD / Jour'],
            ['img' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&w=800&q=80', 'name' => 'Porsche Cayenne Turbo', 'price' => '199 USD / Jour'],
            ['img' => 'https://images.unsplash.com/photo-1504215680853-026ed2a45def?auto=format&w=800&q=80', 'name' => 'BMW Série 5 M-Sport', 'price' => '95 USD / Jour'],
        ] as $car)
            <div class="col-lg-4 col-md-6">
                <div class="showcase-card">
                    <div class="showcase-img-wrapper">
                        <img src="{{ $car['img'] }}" alt="{{ $car['name'] }}">
                    </div>
                    <div class="showcase-body">
                        <h5>{{ $car['name'] }}</h5>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="showcase-price">{{ $car['price'] }}</span>
                            <a href="{{ route('login') }}" style="color: var(--text-main); font-size: 13px; text-decoration: none; font-weight: 600; transition: color 0.2s;">
                                Réserver <i class="fa-solid fa-arrow-right ms-1" style="font-size: 11px; color: var(--accent-teal);"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<section id="agence" class="container">
    <span class="section-label">Réseau</span>
    <h2 class="section-title-pro">Notre Agence Principale</h2>

    <div class="contact-grid row g-0">
        <div class="col-lg-5 contact-details">
            <div class="contact-item">
                <h6>Siège Social</h6>
                <p>Simon, Arrondissement des Cayes, Haïti</p>
            </div>
            <div class="contact-item">
                <h6>Ligne Directe</h6>
                <p>+509 40 16 03 35</p>
            </div>
            <div class="contact-item">
                <h6>Courriel Corporate</h6>
                <p>contact@autogestion.ht</p>
            </div>
            <div class="contact-item mb-0">
                <h6>Disponibilité</h6>
                <p>Lun - Ven : 08h00 - 17h00<br><span style="color: var(--accent-teal); font-weight: 500;">Sam : Sur demande VIP uniquement</span></p>
            </div>
        </div>
        <div class="col-lg-7">
            <iframe 
                src="https://maps.google.com/maps?q=Simon,%20Cayes,%20Haiti&t=&z=13&ie=UTF-8&iwloc=&output=embed" 
                width="100%" 
                height="100%" 
                style="border:0; min-height: 400px; display: block;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>

<section class="container" id="avis">
    <span class="section-label">Confiance</span>
    <h2 class="section-title-pro">Avis de nos partenaires</h2>

    <div class="row g-4">
        @foreach([
            ['name' => 'Jean Pierre', 'corp' => 'Dir. Logistique', 'msg' => 'Un niveau de professionnalisme rare. Les véhicules sont impeccables et la ponctualité est absolue.'],
            ['name' => 'Marie Laurent', 'corp' => 'Particulier VIP', 'msg' => 'Le service conciergerie a su répondre à ma demande de location de dernière minute avec brio.'],
            ['name' => 'David Toussaint', 'corp' => 'Consultant Privé', 'msg' => 'Flotte moderne et facturation transparente. AutoGestion est notre partenaire de confiance depuis deux ans.']
        ] as $t)
            <div class="col-md-4">
                <div class="review-card">
                    <p class="mb-4">"{{ $t['msg'] }}"</p>
                    <div class="h6 mb-1" style="font-size: 14px; font-weight: 600;">{{ $t['name'] }}</div>
                    <small style="color: var(--accent-teal); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">{{ $t['corp'] }}</small>
                </div>
            </div>
        @endforeach
    </div>
</section>

<footer>
    <div>© {{ date('Y') }} AutoGestion . Tous droits réservés.</div>
    <div style="font-size: 13px;">Simon, Cayes, Haïti</div>
</footer>
<script>
    // Toggle dropdown
document.getElementById('theme-toggle').addEventListener('click', () => {
    document.getElementById('theme-dropdown').classList.toggle('show');
});

// Fonksyon pou chanje theme
function setTheme(theme) {
    if (theme === 'dark') {
        document.body.classList.add('dark-mode');
        localStorage.setItem('theme', 'dark');
    } else if (theme === 'light') {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('theme', 'light');
    } else {
        // Pou 'system', nou retire tout epi kite navigeur a deside
        localStorage.removeItem('theme');
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
    }
    document.getElementById('theme-dropdown').classList.remove('show');
}

// Aplikasyon chwa a depi paj la chaje
(function() {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.body.classList.add('dark-mode');
    }
})();
</script>
</body>
</html>