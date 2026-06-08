<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Privé - AutoGestion Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        /* === 1. PALETTE DE COULEURS CLASSIQUE / LIGHT === */
        :root {
            --primary-teal: #0d9488;     /* Turquoise Signature */
            --primary-hover: #0f766e;    /* Turquoise Sombre */
            --dark-deep: #f8fafc;        /* Fond Clair Général */
            --card-bg: #ffffff;          /* Fond Blanc Pur */
            --text-main: #0f172a;        /* Texte Foncé Principal */
            --text-dimmed: #64748b;      /* Gris Muted */
            --border-color: #e2e8f0;    /* Bordure Standard */
        }

        body {
            height: 100vh;
            margin: 0;
            font-family: "Montserrat", sans-serif;
            background: var(--dark-deep);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* === 2. INTERFACE DOUBLE PANNEAU ÉPURÉE === */
        .container-box {
            z-index: 10;
            width: 1000px;
            max-width: 95%;
            height: 600px;
            display: flex;
            border-radius: 16px;
            overflow: hidden;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 
                        0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        /* PANNEAU VISUEL GAUCHE */
        .left-panel {
            width: 45%;
            background: #f1f5f9;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            border-right: 1px solid var(--border-color);
        }

        .brand-logo {
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: var(--text-main);
        }
        
        .left-panel-content h2 {
            font-size: 34px;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 18px;
            color: var(--text-main);
        }

        .left-panel-content p {
            color: var(--text-dimmed);
            font-size: 0.95rem;
            line-height: 1.6;
            font-weight: 400;
            margin: 0;
        }

        .btn-back-home {
            color: var(--text-dimmed);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: all 0.2s ease;
        }
        .btn-back-home:hover {
            color: var(--primary-teal);
            transform: translateX(-3px);
        }

        /* PANNEAU FORMULAIRE DROITE */
        .auth-container {
            width: 55%;
            position: relative;
            overflow: hidden;
            background: var(--card-bg);
        }
        .forms {
            width: 200%;
            height: 100%;
            display: flex;
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .form-container {
            width: 50%;
            padding: 50px 65px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }
        
        .form-container h3 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .form-subtitle {
            color: var(--text-dimmed);
            font-size: 0.9rem;
            margin-bottom: 35px;
        }

        /* === 3. INPUTS ET BOUTONS STANDARD === */
        .input-group {
            margin-bottom: 18px;
            width: 100%;
            position: relative;
        }
        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            transition: color 0.2s;
            z-index: 5;
        }
        .form-control {
            width: 100%;
            padding: 14px 16px 14px 52px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff !important;
            color: var(--text-main) !important;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background: #ffffff !important;
            border-color: var(--primary-teal);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
        }
        
        .input-group:focus-within i {
            color: var(--primary-teal);
        }
        .form-control::placeholder {
            color: #94a3b8;
        }

        .btn-main {
            background: var(--primary-teal);
            color: #ffffff;
            width: 100%;
            padding: 14px;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 8px;
            border: none;
            margin-top: 15px;
            transition: background 0.2s ease;
        }

        .btn-main:hover {
            background: var(--primary-hover);
        }
        
        .switch-text {
            color: var(--text-dimmed);
            font-size: 0.88rem;
            width: 100%;
        }
        .switch-link {
            color: var(--primary-teal);
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            margin-left: 6px;
        }
        .switch-link:hover {
            text-decoration: underline;
        }

        /* === 4. RESPONSIVE === */
        .switch-link-mobile { display: none; }
        @media(max-width: 992px) {
            .container-box {
                width: 400px;
                height: auto;
                flex-direction: column;
            }
            .left-panel {
                width: 100%;
                padding: 35px 30px;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }
            .left-panel-content, .btn-back-home { display: none; }
            .auth-container { width: 100%; }
            .forms { width: 100%; flex-direction: column; transition: none; transform: translateX(0) !important; }
            .form-container { width: 100%; padding: 40px 30px; display: none; }
            .form-container.active-form { display: flex; }
            
            .switch-text { display: none; }
            .switch-link-mobile {
                display: block;
                margin-top: 20px;
                font-size: 0.9rem;
                font-weight: 600;
                color: var(--primary-teal);
                text-decoration: none;
            }
        }
        
.nav-actions { display: flex; gap: 15px; align-items: center; }
#theme-toggle { background: rgba(128,128,128,0.1); border: none; padding: 10px; border-radius: 50%; color: var(--text-color); }
.btn-logout { background: #ef4444; color: white; border: none; padding: 10px 15px; border-radius: 10px; cursor: pointer; }

/* Grid Dashboard */
.container { max-width: 1200px; margin: 2rem auto; padding: 0 20px; }
.dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; }
.card {
    background: var(--card-bg); padding: 25px; border-radius: 20px;
    box-shadow: var(--shadow); border: 1px solid rgba(0,0,0,0.05);
}
.btn-theme { background: rgba(128,128,128,0.1); border: none; padding: 10px; border-radius: 50%; color: var(--text-color); cursor: pointer; }
.theme-dropdown {
    display: none;
    position: absolute;
    top: 45px;
    right: 0;
    background: var(--card-bg);
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 12px;
    padding: 8px;
    width: 140px;
    box-shadow: var(--shadow);
    z-index: 1000;
}
.theme-dropdown.show { display: block; }
.theme-dropdown button {
    width: 100%;
    padding: 8px;
    background: transparent;
    border: none;
    color: var(--text-color);
    text-align: left;
    cursor: pointer;
}
.theme-dropdown button:hover { background: var(--primary); color: white; border-radius: 5px; }
    
    
    
    /* Ajoute sa anba :root ou an */
:root.dark-mode {
    --dark-deep: #0f172a;        /* Fond paj la */
    --card-bg: #1e293b;         /* Fond kat yo */
    --text-main: #f8fafc;        /* Tèks prensipal */
    --text-dimmed: #94a3b8;      /* Tèks segondè */
    --border-color: #334155;     /* Bordure */
}

/* Ajoute tranzisyon pou li dous */
body { transition: background-color 0.3s ease, color 0.3s ease; }
    
    </style>
</head>
<body>

<div class="container-box">
    
    <div class="left-panel">
        <div class="brand-logo">AutoGestion.</div>
        <div class="left-panel-content">
            <h2>L'excellence <br>à chaque kilomètre.</h2>
            <p>Pilotez votre flotte privée haut de gamme, accédez à vos contrats corporatifs et suivez vos réservations en toute simplicité.</p>
        </div>
        <a href="/" class="btn-back-home"><i class="fas fa-arrow-left me-2"></i> Retour au site</a>
    </div>

    <div class="auth-container">
        <div class="forms" id="forms">
            
            
            <!-- FORMULAIRE DE CONNEXION -->
            <div class="form-container active-form" id="login-form">
                @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
                <h3>Bienvenue</h3>
                <p class="form-subtitle">Connectez-vous à votre espace personnel.</p>
                
                @if($errors->any())
                    <div class="alert alert-danger w-100 text-center rounded-3 small py-2 mb-3">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}" class="w-100">
                    @csrf
                    <div class="input-group">
                        <i class="far fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="Adresse email" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i> 
                        <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
                    </div>
                    <button type="submit" class="btn-main">Se connecter</button>
                    <a href="{{ url('/auth/google') }}" class="btn btn-outline-dark w-100 mt-3">
    <i class="fab fa-google"></i> Se connecter avec Google
</a>
                </form>
                
                <p class="mt-4 text-start switch-text">
                    Nouveau sur la plateforme ?<span class="switch-link" onclick="toggleForms(false)">Créer un compte</span>
                </p>
                <a href="#" class="switch-link-mobile" onclick="toggleForms(false); return false;">Créer un compte</a>
            </div>

            <!-- FORMULAIRE D'INSCRIPTION -->
            <div class="form-container" id="register-form">
                <h3>Inscription</h3>
                <p class="form-subtitle">Rejoignez notre réseau privé premium.</p>
                
                <form method="POST" action="{{ route('register.submit') }}" class="w-100">
                    @csrf
                    <div class="input-group">
                        <i class="far fa-user"></i>
                        <input type="text" name="nom" class="form-control" placeholder="Nom complet" required>
                    </div>
                    <div class="input-group">
                        <i class="far fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="Adresse email valide" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Mot de passe (Min 8 car.)" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmer le mot de passe" required>
                    </div>
                    <button type="submit" class="btn-main">Créer mon espace</button>
                </form>

                <p class="mt-4 text-start switch-text">
                    Vous possédez déjà un compte ?<span class="switch-link" onclick="toggleForms(true)">Se connecter</span>
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