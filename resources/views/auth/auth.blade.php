<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification - AutoGestion Plus Ultra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <style>
        /* === 1. VARIABLES & FOND AVÈK IMAJ PRESTIGE (REPRIS DE L'ACCUEIL) === */
        :root {
            --primary-color: #0d9488; /* Turquoise/Teck Vif */
            --secondary-color: #FFC300; /* Jaune/Or Électrique */
            --bg-page: #0f172a; /* Bleu Nuit Profond */
            --bg-dark-section: #1e293b; /* Section plus foncée */
            --text-light: #f1f5f9;
            --text-muted: #94a3b8;
            --input-bg: rgba(255, 255, 255, 0.08);

            /* GLOws pou aksan yo */
            --glow-primary: rgba(13, 148, 136, 0.6);
            --glow-secondary: rgba(255, 195, 0, 0.6);

            /* Glassmorphism */
            --bg-card: rgba(15, 23, 42, 0.65); 
        }

        body {
            height: 100vh;
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: var(--bg-page);
            color: var(--text-light);
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden; 
            padding: 20px;
            position: relative; 
        }

        /* NOUVO FOND AVÈK IMAJ MACHIN LIKZYÈ A + MASK FILTRE NWA */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* Mask nwa melanje ak nouvo bèl imaj fon an */
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(13, 148, 136, 0.4)), 
                        url('http://googleusercontent.com/image_generation_content/6');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -1;
            pointer-events: none;
        }

        /* === 2. STRUCTURE GLOBALE (Glassmorphism & Glow Kontou) === */
        .container-box {
            z-index: 10; 
            width: 950px; 
            max-width: 95%;
            height: 580px; 
            display: flex;
            border-radius: 25px;
            overflow: hidden;
            background: var(--bg-card);
            
            /* Glassmorphism Efè flou */
            backdrop-filter: blur(14px); 
            -webkit-backdrop-filter: blur(14px);
            
            /* Contour Glow */
            position: relative; 
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.1),
                        0 20px 60px rgba(0, 0, 0, 0.7),
                        0 0 25px var(--glow-primary);
        }

        /* 2A. Panneau Gauche (Visuel kòporatif) */
        .left-panel {
            width: 50%;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 41, 59, 0.8) 100%);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            z-index: 2;
            border-right: 2px solid var(--primary-color);
        }
        
        .left-panel h1 {
            font-size: 45px;
            font-weight: 900; 
            margin-top: 25px;
            background-image: linear-gradient(45deg, var(--secondary-color), var(--primary-color), var(--text-light));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            letter-spacing: 3px;
            text-shadow: 0 0 10px rgba(255, 195, 0, 0.2);
        }

        .icon-car {
            font-size: 6rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
            text-shadow: 0 0 20px var(--glow-secondary);
            animation: pulse 3s infinite ease-in-out;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* 2B. Conteneur Formulaires */
        .auth-container {
            width: 50%; 
            position: relative;
            overflow: hidden; 
        }
        .forms {
            width: 200%; 
            height: 100%;
            display: flex;
            transition: transform 0.8s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        .form-container {
            width: 50%; 
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        
        .form-container h3 {
            font-weight: 800;
            color: var(--secondary-color);
            margin-bottom: 30px;
            text-shadow: 0 0 5px rgba(255, 195, 0, 0.3);
        }

        /* === 3. INPUTS & BOUTONS === */
        .input-group {
            margin-bottom: 25px;
            width: 100%;
            position: relative;
        }
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
            font-size: 1.1rem;
            transition: color 0.3s;
        }
        .form-control {
            width: 100%;
            padding: 15px 15px 15px 50px; 
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-light);
            font-size: 1.05rem;
            transition: border-color 0.3s, background 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color); 
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.4); 
            color: var(--text-light);
        }
        
        .input-group:focus-within i {
            color: var(--primary-color);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        /* Bouton Principal */
        .btn-main {
            background: var(--primary-color);
            color: var(--bg-page); 
            width: 100%;
            padding: 16px; 
            font-weight: 800;
            border-radius: 12px;
            border: none;
            margin-top: 20px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 0 15px var(--glow-primary); 
        }

        .btn-main:hover {
            background: transparent;
            color: var(--primary-color); 
            transform: translateY(-4px) scale(1.01); 
            box-shadow: 0 12px 25px var(--glow-primary); 
            border: 1px solid var(--primary-color);
        }
        
        .switch-link {
            color: var(--secondary-color);
            cursor: pointer;
            text-decoration: underline;
            font-weight: 700;
            transition: color 0.3s, text-shadow 0.3s;
        }
        .switch-link:hover {
            color: var(--text-light);
            text-shadow: 0 0 8px var(--glow-secondary);
        }
        
        /* Alertes */
        .alert-danger {
            background-color: rgba(252, 165, 165, 0.15);
            color: var(--secondary-color);
            border-left: 3px solid var(--secondary-color);
            border-radius: 10px;
            font-size: 0.9rem;
            border: none;
        }

        /* === 4. RESPONSIVE (Mobile) === */
        .switch-link-mobile { display: none; }
        @media(min-width: 993px) { 
            .form-container { display: flex !important; }
        }
        @media(max-width: 992px) {
            .container-box { 
                width: 400px; 
                height: auto; 
                flex-direction: column; 
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8), 0 0 15px var(--glow-primary);
            }
            .left-panel { 
                width: 100%; 
                height: 150px; 
                border-radius: 25px 25px 0 0; 
                border-right: none;
            }
            .left-panel p { display: none; }
            .icon-car { font-size: 3rem; margin-bottom: 5px; }
            .left-panel h1 { font-size: 30px; margin-top: 10px; }
            .auth-container { width: 100%; }
            .forms { width: 100%; flex-direction: column; transition: none; transform: translateX(0) !important; }
            .form-container { width: 100%; padding: 30px 25px; display: none; }
            .form-container.active-form { display: flex; }
            .form-container:last-child { border-top: 1px solid rgba(255, 255, 255, 0.1); }
            
            .switch-link { display: none; }
            .switch-link-mobile {
                display: block; 
                margin-top: 20px; 
                text-decoration: none;
                font-weight: 700; 
                color: var(--secondary-color);
            }
        }
    </style>
</head>
<body class="dark-mode">

<div class="container-box">
    
    <div class="left-panel">
        <i class="fas fa-car-side icon-car"></i>
        <h1>AutoGestion</h1>
        <p class="d-none d-lg-block">Plateforme de location & vente de véhicules premium.<br>Fiabilité, performance et luxe au service de nos clients.</p>
    </div>

    <div class="auth-container">
        <div class="forms" id="forms">
            <div class="form-container active-form" id="login-form">
                <h3><i class="fas fa-sign-in-alt me-2"></i> Connexion Client</h3>
                @if($errors->any())
                    <div class="alert alert-danger w-100 text-center">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('login.submit') }}" class="w-100">
                    @csrf
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="Adresse Email" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
                    </div>
                    <button class="btn-main">Se connecter</button>
                </form>
                
                <p class="mt-4 text-center">
                    Pas de compte ? 
                    <span class="switch-link" onclick="toggleForms(false)">Créer un compte</span>
                </p>
                <a href="#" class="switch-link-mobile" onclick="toggleForms(false); return false;">Créer un compte</a>
            </div>

            <div class="form-container" id="register-form">
                <h3><i class="fas fa-user-plus me-2"></i> Créer un compte</h3>
                <form method="POST" action="{{ route('register.submit') }}" class="w-100">
                    @csrf
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="nom" class="form-control" placeholder="Nom complet" required>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="Adresse Email" required>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control"
                               placeholder="Mot de passe (Min 8 caractères)" required>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password_confirmation"
                               class="form-control" placeholder="Confirmer le mot de passe" required>
                    </div>

                    <button class="btn-main">S'inscrire</button>
                </form>

                <p class="mt-4 text-center">
                    Déjà un compte ? 
                    <span class="switch-link" onclick="toggleForms(true)">Se connecter</span>
                </p>
                <a href="#" class="switch-link-mobile" onclick="toggleForms(true); return false;">Se connecter</a>
            </div>
        </div>
    </div>
</div>

<script>
    const formsContainer = document.getElementById('forms');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    
    function toggleForms(isLogin) {
        if (isLogin) {
            loginForm.classList.add('active-form');
            registerForm.classList.remove('active-form');
        } else {
            loginForm.classList.remove('active-form');
            registerForm.classList.add('active-form');
        }

        if (window.innerWidth > 992) {
            formsContainer.style.transform = isLogin ? 'translateX(0)' : 'translateX(-50%)';
        }
    }

    function handleScreenSize() {
        const isLoginActive = loginForm.classList.contains('active-form');
        if (window.innerWidth > 992) {
            formsContainer.style.transform = isLoginActive ? 'translateX(0)' : 'translateX(-50%)';
        } else {
            formsContainer.style.transform = 'translateX(0)'; 
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const isRegister = urlParams.get('form') === 'register';
        toggleForms(!isRegister); 
        handleScreenSize(); 
    });
    
    window.addEventListener('resize', handleScreenSize);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>