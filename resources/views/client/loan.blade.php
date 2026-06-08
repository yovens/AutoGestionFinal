<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Locations | AutoGestion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        :root {
    --bg-color: #f8fafc;
    --text-color: #1e293b;
    --card-bg: #ffffff;
    --primary: #3b82f6; /* Ble pi modèn */
    --shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
}

body {
    transition: background-color 0.3s ease, color 0.3s ease;
    background-color: var(--bg-color);
    color: var(--text-color);
}

/* Appliquer aussi aux cartes pour une transition cohérente */
.card {
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}
      

        [data-theme='dark'] {
            --bg-color: #0f172a;
            --text-color: #f1f5f9;
            --card-bg: #1e293b;
            --primary: #f472b6;
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

        
        /* Grid & Card Style */
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .card { background: var(--card-bg); padding: 20px; border-radius: 15px; box-shadow: var(--shadow); border: 1px solid rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .title { font-size: 1.2rem; font-weight: bold; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; }
        .b-approuve { background: #dcfce7; color: #166534; }
        .b-refuse { background: #fee2e2; color: #991b1b; }
        .b-attente { background: #fef3c7; color: #92400e; }
        .b-expire { background: #e2e8f0; color: #475569; }
        .price { font-weight: bold; color: var(--primary); font-size: 1.1rem; }
        
        #theme-toggle { background: rgba(128,128,128,0.1); border: none; padding: 10px; border-radius: 50%; color: var(--text-color); cursor: pointer; }
        .btn-logout { background: #ef4444; color: white; border: none; padding: 10px 15px; border-radius: 10px; cursor: pointer; }
    .btn-renew {
    transition: opacity 0.3s;
}
.btn-renew:hover {
    opacity: 0.8;
}
    /* Container fòm lan */
.renewal-form-container {
    background: rgba(128, 128, 128, 0.05);
    padding: 15px;
    border-radius: 12px;
    margin-top: 15px;
}

/* Input style */
.renewal-input {
    background: var(--bg-color) !important;
    border: 1px solid rgba(128, 128, 128, 0.3) !important;
    color: var(--text-color) !important;
    border-radius: 8px 0 0 8px !important;
    padding: 10px;
}

/* Bouton style */
.renewal-btn {
    background: var(--primary) !important;
    border: none !important;
    color: white !important;
    padding: 0 15px;
    border-radius: 0 8px 8px 0 !important;
    font-weight: 600;
    transition: 0.3s;
}

.renewal-btn:hover {
    filter: brightness(1.2);
    cursor: pointer;
}

/* Ti tèks anba a */
.renewal-helper {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 8px;
    display: block;
}
.renewal-btn {
    background: var(--primary) !important;
    color: #fff !important;
    border: none !important;
    padding: 8px 20px !important;
    font-weight: 600 !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease !important;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.renewal-btn:hover {
    filter: brightness(1.2);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px -2px rgba(0, 0, 0, 0.2);
}

.renewal-btn:active {
    transform: translateY(0);
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
</head>
<body>

  <header class="navbar">
    <div class="nav-wrapper">
        <a href="{{ route('client.dashboard') }}" class="logo">AutoGestion</a>
        <nav class="nav-links">
            <a href="{{ route('client.dashboard') }}" class=""><i class="fas fa-home"></i> Dashboard</a>
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
    <h2>🔑 Mes Locations</h2>

    @if($loans->isEmpty())
        <div class="card" style="text-align: center;">
            <p>Aucune location enregistrée pour le moment.</p>
        </div>
    @else
     <div class="grid">
    @foreach($loans as $loan)
    <div class="card">
        <div class="header">
            <span class="title">{{ $loan->vehicle->brand }} {{ $loan->vehicle->model }}</span>
            @php
                $map = [
                    'approved' => ['class' => 'b-approuve', 'label' => 'Approuvé'],
                    'expired'  => ['class' => 'b-expire', 'label' => 'Expiré'],
                    'rejected' => ['class' => 'b-refuse', 'label' => 'Refusé'],
                    'pending'  => ['class' => 'b-attente', 'label' => 'En attente'],
                    'pending_renewal' => ['class' => 'b-attente', 'label' => 'Renouv. en attente']
                ];
                $s = $map[$loan->status] ?? ['class' => '', 'label' => $loan->status];
            @endphp
            <span class="badge {{ $s['class'] }}">{{ $s['label'] }}</span>
        </div>
        
        <div style="font-size: 0.9rem;">
            <p><i class="fas fa-calendar-alt"></i> <strong>Début:</strong> {{ $loan->start_date ? \Carbon\Carbon::parse($loan->start_date)->format('d/m/Y') : 'N/A' }}</p>
            <p><i class="fas fa-calendar-check"></i> <strong>Fin:</strong> {{ $loan->end_date ? \Carbon\Carbon::parse($loan->end_date)->format('d/m/Y') : 'N/A' }}</p>
            <p><i class="fas fa-hourglass-half"></i> <strong>Durée:</strong> {{ $loan->duration_days }} jours</p>
        </div>
        
        <div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 10px;">
            <span class="price">{{ number_format($loan->total_amount, 2) }} USD</span>
        </div>

        @if(in_array($loan->status, ['approved', 'expired']))
<form method="POST" action="{{ route('client.loan.renew', $loan->id) }}" class="renewal-form-container">
    @csrf
    <div class="input-group">
        <input type="number" 
               name="additional_days" 
               class="form-control renewal-input" 
               placeholder="Nb de jours" 
               min="1" 
               required>
        <button type="submit" class="btn renewal-btn">
            <i class="fas fa-redo me-1"></i> Envoyer
        </button>
    </div>
    <small class="renewal-helper">
        <i class="fas fa-info-circle"></i> Ajouter des jours à la location.
    </small>
</form>
        @elseif($loan->status == 'pending_renewal')
            <div class="mt-3 text-muted small italic">
                <i class="fas fa-clock"></i> Demande de renouvellement envoyée.
            </div>
        @endif
    </div>
    @endforeach

         
        </div>
    @endif
</main>


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

</body>
</html>