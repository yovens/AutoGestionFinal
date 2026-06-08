<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | AutoGestion</title>
 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

  <header class="navbar">
    <div class="nav-wrapper">
        <a href="{{ route('client.dashboard') }}" class="logo">AutoGestion</a>
      <nav class="nav-links">
    <a href="{{ route('client.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
    <a href="{{ route('client.vehicles') }}"><i class="fas fa-car"></i> Véhicules</a>
    <a href="{{ route('client.cart') }}"><i class="fas fa-shopping-cart"></i> Panier</a>
    <a href="{{ route('client.loan') }}"><i class="fas fa-key"></i> Locations</a>
    <a href="{{ route('client.transactions') }}"><i class="fas fa-exchange-alt"></i> Transactions</a>
    <a href="{{ route('client.contact') }}"><i class="fas fa-headset"></i> Contact</a>
    
    <a href="{{ route('client.profile') }}"><i class="fas fa-user-circle"></i> Profil</a>
</nav>
       <div class="nav-actions">
    <div style="position: relative;">
        <button id="theme-toggle" class="btn-theme"><i class="fas fa-palette"></i></button>
        <div id="theme-dropdown" class="theme-dropdown">
            <button onclick="setTheme('light')"><i class="fas fa-sun"></i> Clair</button>
            <button onclick="setTheme('dark')"><i class="fas fa-moon"></i> Sombre</button>
            <button onclick="setTheme('system')"><i class="fas fa-laptop"></i> Système</button>
        </div>
    </div>
    
    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
        @csrf
        <button type="submit" class="btn-logout"><i class="fas fa-power-off"></i></button>
    </form>
</div>
    </div>
</header>

    <main class="container">
        @yield('content')
    </main>

    <script src="{{ asset('js/theme.js') }}"></script>
</body>
</html>

 
<style>
:root {
    --bg-color: #f8fafc;
    --text-color: #1e293b;
    --card-bg: #ffffff;
    --primary: #3b82f6; /* Ble pi modèn */
    --shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
}

[data-theme='dark'] {
    --bg-color: #0f172a;
    --text-color: #f1f5f9;
    --card-bg: #1e293b;
    --primary: #f472b6;
}
body {
    transition: background-color 0.3s ease, color 0.3s ease;
    background-color: var(--bg-color);
    color: var(--text-color);
}
/* Navbar pwofesyonèl */
.navbar { 
    background: var(--card-bg); 
    padding: 0.8rem 0; 
    border-bottom: 1px solid rgba(0,0,0,0.05);
    position: sticky; top: 0; z-index: 1000;
}
.nav-wrapper { display: flex; align-items: center; justify-content: space-between; max-width: 1200px; margin: 0 auto; padding: 0 20px; }

.logo { font-weight: 800; font-size: 1.5rem; color: var(--primary); text-decoration: none; }

.nav-links { display: flex; gap: 5px; }
.nav-links a { 
    text-decoration: none; color: var(--text-color); font-weight: 500; font-size: 0.9rem;
    padding: 10px 15px; border-radius: 10px; transition: 0.3s;
}
.nav-links a:hover, .nav-links a.active { background: var(--primary); color: white; }

/* Aksyon (Bouton) */
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
</style>




<script>
const dropdown = document.getElementById('theme-dropdown');
const toggleBtn = document.getElementById('theme-toggle');

// Ouvri/Fèmen meni an
toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdown.classList.toggle('show');
});

// Fèmen si ou klike deyò
document.addEventListener('click', () => dropdown.classList.remove('show'));

function setTheme(theme) {
    localStorage.setItem('theme', theme);
    applyTheme(theme);
    dropdown.classList.remove('show');
}

function applyTheme(theme) {
    if (theme === 'system') {
        const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.body.setAttribute('data-theme', isDark ? 'dark' : 'light');
    } else {
        document.body.setAttribute('data-theme', theme);
    }
}

// Chaje tèm ki anrejistre a
const savedTheme = localStorage.getItem('theme') || 'system';
applyTheme(savedTheme);

// Gade si sistèm nan chanje pandan ou sou sit la
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
    if (localStorage.getItem('theme') === 'system') {
        applyTheme('system');
    }
});
</script>





<style>
    
    /* On utilise la classe card (globale) + contact-card pour les ajustements */
    .contact-card { 
        background-color: var(--card-bg); 
        border-radius: 20px; 
        box-shadow: var(--shadow);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .form-label { 
        display: block; 
        margin-bottom: 8px; 
        font-weight: 600;
        color: var(--text-color);
    }

    .form-control { 
        width: 100%; 
        padding: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background-color: var(--bg-color);
        color: var(--text-color);
        box-sizing: border-box; 
        margin-bottom: 20px;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: var(--primary);
        outline: none;
    }

    .btn-submit { 
        width: 100%;
        padding: 12px;
        background-color: var(--primary);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
        transition: opacity 0.3s;
    }

    .btn-submit:hover {
        opacity: 0.9;
    }
.contact-card { 
    background-color: var(--card-bg) !important; 
    border-radius: 20px !important; 
    box-shadow: var(--shadow) !important;
}
.form-control { 
    display: block !important; /* Force l'affichage en bloc */
    width: 100% !important;    /* Force la largeur totale */
    padding: 12px !important;
    margin-bottom: 20px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    background-color: var(--bg-color) !important;
    color: var(--text-color) !important;
    box-sizing: border-box !important; 
}
    /* 1. Assurer que la carte a de l'espace interne */
</style>







