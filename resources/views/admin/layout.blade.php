<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard') - AutoGestion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Polices & Icônes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* === CONFIGURATION DES VARIABLES GLOBALES === */
        :root {
            --sidebar-width: 280px;
            --font-main: 'Plus Jakarta Sans', sans-serif;
            
            /* Thème Sombre (Par défaut) */
            --bg-gradient: linear-gradient(135deg, #0b0f19 0%, #111827 50%, #071724 100%);
            --panel-bg: rgba(17, 24, 39, 0.7);
            --panel-border: rgba(255, 255, 255, 0.06);
            --panel-hover: rgba(255, 255, 255, 0.03);
            
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            
            --brand-primary: #0ea5e9;
            --brand-glow: rgba(14, 165, 233, 0.15);
            --brand-success: #10b981;
            --brand-danger: #ef4444;
            --brand-warning: #f59e0b;
            
            --shadow-ui: 0 10px 30px -10px rgba(0, 0, 0, 0.7);
            --glass-blur: blur(16px);
        }

        body.light-mode {
            /* Thème Clair */
            --bg-gradient: #f8fafc;
            --panel-bg: #ffffff;
            --panel-border: rgba(0, 0, 0, 0.06);
            --panel-hover: rgba(0, 0, 0, 0.02);
            
            --text-main: #0f172a;
            --text-muted: #64748b;
            
            --brand-primary: #0284c7;
            --brand-glow: rgba(2, 132, 199, 0.1);
            --brand-success: #16a34a;
            --brand-danger: #dc2626;
            --brand-warning: #d97706;
            
            --shadow-ui: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
            --glass-blur: none;
        }

        /* === CONFIGURATION STRUCURELLE === */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-main);
            background: var(--bg-gradient);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            transition: background 0.3s ease, color 0.3s ease;
            overflow-x: hidden;
        }
/* Ta sidebar existante (ajuste la classe si nécessaire) */
        .sidebar {
            width: 260px;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            background: #fff;
            z-index: 1000;
            /* Conserve tes styles actuels ici */
        }

        /* LE FIX COMPLET : Le conteneur principal prend toute la largeur restante */
        .main-container {
            padding-left: 280px; /* 260px de sidebar + 20px d'espace de respiration */
            padding-right: 24px;
            padding-top: 24px;
            padding-bottom: 24px;
            min-height: 100vh;
            box-sizing: border-box;
            width: 100%;
        }
.app-container {
    display: flex;
    width: 100%;
    min-height: 100vh;
}

.sidebar {
    width: 260px;
    flex-shrink: 0;
}

.main-content {
    flex: 1;
    width: 100%;
    min-width: 0; /* IMPORTANT pour éviter les blocages */
    padding: 24px;
}
        .page-header {
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 28px;
            color: #0f172a;
            margin: 0;
            font-weight: 700;
        }
        /* === COMPOSANT : SIDEBAR NAVIGATION === */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--panel-bg);
            backdrop-filter: var(--glass-blur);
            border-right: 1px solid var(--panel-border);
            display: flex;
            flex-direction: column;
            padding: 24px;
            z-index: 100;
            box-shadow: var(--shadow-ui);
            transition: background 0.3s ease, border 0.3s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-main);
            margin-bottom: 32px;
        }

        .sidebar-brand i {
            color: var(--brand-primary);
            font-size: 24px;
        }

        .profile-card {
            background: var(--panel-hover);
            border: 1px solid var(--panel-border);
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .profile-avatar {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--brand-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 18px;
        }

        .profile-info {
            overflow: hidden;
        }

        .profile-name {
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .profile-role {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-grow: 1;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .nav-item a i {
            font-size: 18px;
            transition: transform 0.2s ease;
        }

        .nav-item a:hover {
            color: var(--text-main);
            background: var(--panel-hover);
        }

        .nav-item a:hover i {
            transform: translateX(2px);
        }

        .nav-item.active a {
            color: #fff;
            background: var(--brand-primary);
            box-shadow: 0 4px 20px var(--brand-glow);
            font-weight: 600;
        }

        .nav-item.active a i {
            color: #fff;
        }

        .logout-box {
            padding-top: 16px;
            border-top: 1px solid var(--panel-border);
        }

        .btn-logout {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            background: transparent;
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border-radius: 10px;
            cursor: pointer;
            font-family: var(--font-main);
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: #ef4444;
            color: #fff;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
        }

        /* === ZONE DE CONTENU PRINCIPALE === */
      .wrapper {
    flex: 1;
    min-width: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 24px;
    margin-left: var(--sidebar-width);
}

        header class="top-bar" {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .page-title-area h2 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .date-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
            background: var(--panel-bg);
            border: 1px solid var(--panel-border);
            padding: 10px 16px;
            border-radius: 10px;
        }

        .theme-toggle-btn {
            background: var(--panel-bg);
            border: 1px solid var(--panel-border);
            color: var(--text-main);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-ui);
        }

        .theme-toggle-btn:hover {
            border-color: var(--brand-primary);
            color: var(--brand-primary);
        }

        /* === FORMULAIRES & MODALS EN VANILLA CSS === */
        .form-control, .form-select {
            width: 100%;
            padding: 12px 16px;
            background: var(--panel-bg);
            border: 1px solid var(--panel-border);
            border-radius: 8px;
            color: var(--text-main);
            font-family: var(--font-main);
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px var(--brand-glow);
        }

        /* === ÉLÉMENTS REUTILISABLES (Pour les vues enfants) === */
        .intro {
            font-size: 15px;
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: 32px;
            max-width: 800px;
        }

        /* Grilles UI Native */
        .grid-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--panel-bg);
            backdrop-filter: var(--glass-blur);
            border: 1px solid var(--panel-border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow-ui);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(14, 165, 233, 0.3);
        }

        /* Tableaux Réinventés */
        .table-container {
            background: var(--panel-bg);
            backdrop-filter: var(--glass-blur);
            border: 1px solid var(--panel-border);
            border-radius: 16px;
            box-shadow: var(--shadow-ui);
            overflow-x: auto;
            margin-top: 20px;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .custom-table th {
            background: var(--panel-hover);
            padding: 16px 24px;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--panel-border);
        }

        .custom-table td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--panel-border);
            color: var(--text-main);
        }

        .custom-table tr:last-child td {
            border-bottom: none;
        }

        .custom-table tr:hover td {
            background: var(--panel-hover);
        }

        /* Badges de statuts standardisés */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-badge.pending { background: rgba(245, 158, 11, 0.12); color: var(--brand-warning); }
        .status-badge.success { background: rgba(16, 185, 129, 0.12); color: var(--brand-success); }
        .status-badge.danger { background: rgba(239, 68, 68, 0.12); color: var(--brand-danger); }

        /* Boutons natifs */
        .btn-action {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid var(--panel-border);
            background: var(--panel-hover);
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-action:hover {
            border-color: var(--brand-primary);
            color: var(--brand-primary);
        }

        /* Responsivité native */
        @media (max-width: 1024px) {
            :root { --sidebar-width: 80px; }
            .sidebar-brand span, .profile-info, .nav-item span, .btn-logout span { display: none; }
            .sidebar { padding: 16px 8px; align-items: center; }
            .sidebar-brand { justify-content: center; margin-bottom: 24px; }
            .profile-card { padding: 8px; justify-content: center; }
            .nav-item a { justify-content: center; padding: 12px; }
            .date-badge { display: none; }
        }

        .sidebar {
    height: 100vh;           /* Force sidebar a pran tout wotè ekran an */
    overflow-y: auto;        /* Sa a se li ki montre scroll bar la si l nesesè */
    display: flex;
    flex-direction: column;
}

/* Pou personnalize jan scroll bar la parèt (si ou vle l bèl) */
.sidebar::-webkit-scrollbar {
    width: 6px;              /* Lajè scroll bar la */
}

.sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;     /* Koulè background scroll la */
}

.sidebar::-webkit-scrollbar-thumb {
    background: #888;        /* Koulè "pouse" scroll la */
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #555;        /* Koulè lè sourit la sou li */
}

/* Container pou bouton an ak meni an */
.theme-menu-container {
    position: relative;
}

.theme-dropdown {
    display: none;
    position: absolute;
    top: 50px;
    right: 0;
    background: var(--panel-bg);
    border: 1px solid var(--panel-border);
    border-radius: 12px;
    padding: 8px;
    width: 160px;
    box-shadow: var(--shadow-ui);
    z-index: 1000;
}

.theme-dropdown.show { display: block; }

.theme-dropdown button {
    width: 100%;
    padding: 10px;
    background: transparent;
    border: none;
    color: var(--text-main);
    text-align: left;
    cursor: pointer;
    border-radius: 8px;
}

.theme-dropdown button:hover { background: var(--panel-hover); }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-shield-lock-fill"></i>
            <span>AutoGestion</span>
        </div>

        <div class="profile-card">
           <div class="profile-avatar">
    {{ mb_strtoupper(mb_substr($adminName ?? 'A', 0, 1)) }}
</div>
            <div class="profile-info">
                <div class="profile-name">{{ $adminName ?? 'Administrateur' }}</div>
                <div class="profile-role">Admin Principal</div>
            </div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people-fill"></i><span>Utilisateurs</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
                <a href="{{ route('admin.vehicles.index') }}">
                    <i class="bi bi-car-front-fill"></i><span>Véhicules</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                <a href="{{ route('admin.transactions.index') }}">
                    <i class="bi bi-wallet2"></i><span>Transactions</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.loans.*') ? 'active' : '' }}">
                <a href="{{ route('admin.loans.index') }}">
                    <i class="bi bi-calendar-range-fill"></i><span>Locations</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                <a href="{{ route('admin.profile') }}">
                    <i class="bi bi-sliders"></i><span>Configuration</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.contacts.index') ? 'active' : '' }}">
    <a href="{{ route('admin.contacts.index') }}">
        <i class="bi bi-chat-left-dots"></i>
        <span>Messages</span>
       @isset($unreadMessagesCount)
    @if($unreadMessagesCount > 0)
        <span class="badge bg-danger">{{ $unreadMessagesCount }}</span>
    @endif
@endisset
    </a>
</li>
        </ul>

        <div class="logout-box">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-power"></i><span>Déconnexion</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- WRAPPER DE CONTENU -->
    <div class="wrapper">
        <header class="top-bar">
            <div class="page-title-area">
                <h2>@yield('page_title', 'Tableau de bord')</h2>
            </div>
            
            <div class="top-actions">
                <div class="date-badge">
                    <i class="bi bi-calendar3"></i>
                    <span>{{ date('d F Y') }}</span>
                </div>
              <div class="theme-menu-container">
    <button id="mode-toggle" class="theme-toggle-btn" title="Changer de thème">
        <i class="bi bi-palette-fill"></i>
    </button>
    <div id="theme-dropdown" class="theme-dropdown">
        <button onclick="setTheme('light')"><i class="bi bi-sun"></i> Clair</button>
        <button onclick="setTheme('dark')"><i class="bi bi-moon-stars"></i> Sombre</button>
        <button onclick="setTheme('system')"><i class="bi bi-laptop"></i> Système</button>
    </div>
</div>
            </div>
        </header>

        <main>
            @yield('content')
        </main>
    </div>

    <!-- JS REFAIT SANS BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const body = document.body;
        const toggleButton = document.getElementById('mode-toggle');
        const modeIcon = document.getElementById('mode-icon');

        const savedMode = localStorage.getItem('theme') || 'dark';
        if (savedMode === 'light') {
            body.classList.add('light-mode');
            modeIcon.className = 'bi bi-moon-stars-fill';
        } else {
            modeIcon.className = 'bi bi-sun-fill';
        }

        toggleButton.addEventListener('click', () => {
            body.classList.toggle('light-mode');
            const isLightMode = body.classList.contains('light-mode');
            
            modeIcon.className = isLightMode ? 'bi bi-moon-stars-fill' : 'bi bi-sun-fill';
            localStorage.setItem('theme', isLightMode ? 'light' : 'dark');
            
            if (typeof updateChartColors === 'function') {
                updateChartColors();
            }
        });
    });
    </script>
    <script>
const dropdown = document.getElementById('theme-dropdown');
const toggleBtn = document.getElementById('mode-toggle');

toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdown.classList.toggle('show');
});

// Fèmen meni an si ou klike deyò
document.addEventListener('click', () => dropdown.classList.remove('show'));

function setTheme(mode) {
    localStorage.setItem('theme', mode);
    applyTheme(mode);
    dropdown.classList.remove('show');
}

function applyTheme(mode) {
    const body = document.body;
    if (mode === 'system') {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        body.classList.toggle('light-mode', !prefersDark);
    } else {
        body.classList.toggle('light-mode', mode === 'light');
    }
}

// Aplike tèm nan lè paj la chaje
const savedMode = localStorage.getItem('theme') || 'system';
applyTheme(savedMode);
</script>
    @stack('scripts')










    
</body>
</html>