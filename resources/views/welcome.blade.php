nan page ola chart depence lan pran bon infos a ak tou nombre la <!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Client | AutoGestion</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap & Font Awesome (fa-v6) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  {{-- Police Google Fonts --}}
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  {{-- Script Chart.js (ESSENTIEL POUR LE GRAPHIQUE DE JAUGE) --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    /* =================================================================== */
    /* === 1. Variables Globales (Dark Mode par défaut) ================== */
    /* =================================================================== */
    :root {
      --primary-color: #FF6B6B; /* Rouge Corail Vif */
      --secondary-color: #4ECDC4; /* Cyan/Vert d'eau */
      --text-light: #EAEFF4;
      --text-muted: #94a3b8;
      --bg-page: #161B22; /* Fond très sombre */
      --card-bg: #1F2A37; /* Fond des cartes */
      --topbar-height: 70px;
      --promo-bg: #8e44ad; /* Violet pour les promos */
      --mvp-color: #ffd700; /* Or pour MVP */

      /* Ombres et Bordures */
      --shadow-color: rgba(0, 0, 0, 0.4);
      --shadow-hover: rgba(0, 0, 0, 0.6);
      --glow-primary: rgba(255, 107, 107, 0.5);
      --glow-secondary: rgba(78, 205, 196, 0.5);
      --card-border-color: rgba(255, 255, 255, 0.08);
    }

    /* =================================================================== */
    /* === 1.1 Variables Light Mode ====================================== */
    /* =================================================================== */
    .light-mode {
      --primary-color: #007bff;
      --secondary-color: #28a745;
      --text-light: #343a40;
      --text-muted: #6c757d;
      --bg-page: #f4f6f9;
      --card-bg: #ffffff;
      --promo-bg: #c0392b; /* Rouge Brique pour les promos */
      --mvp-color: #daa520; /* Or Foncée pour MVP */

      --shadow-color: rgba(0, 0, 0, 0.1);
      --shadow-hover: rgba(0, 0, 0, 0.15);
      --glow-primary: rgba(0, 123, 255, 0.3);
      --glow-secondary: rgba(40, 167, 69, 0.3);
      --card-border-color: #e9ecef;
    }

    /* =================================================================== */
    /* === 2. Styles de Base & Topbar ==================================== */
    /* =================================================================== */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--bg-page);
      min-height: 10vh;
      color: var(--text-light);
      padding-top: var(--topbar-height);
      transition: background-color 0.4s, color 0.4s;
    }

    .topbar {
      height: var(--topbar-height);
      background: var(--card-bg);
      box-shadow: 0 4px 15px var(--shadow-color);
      border-bottom: 1px solid var(--card-border-color);
      padding: 0 40px;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1020;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: background 0.4s, border-color 0.4s;
    }

    .logo {
      font-size: 24px;
      font-weight: 800;
      color: var(--primary-color);
      text-shadow: 0 0 5px rgba(255, 107, 107, 0.3);
      text-decoration: none;
    }
    .light-mode .logo { text-shadow: none; }

    .topbar-nav a {
      color: var(--text-muted);
      font-weight: 500;
      padding: 8px 15px;
      border-radius: 8px;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border-bottom: 3px solid transparent;
    }

    .topbar-nav a:hover, .topbar-nav a.active {
      color: var(--primary-color);
      background-color: var(--bg-page);
      border-bottom: 3px solid var(--primary-color);
    }

    /* 3. Boutons Topbar */
    .topbar-actions { display: flex; align-items: center; gap: 15px; }
    .btn-theme-toggle {
      background: rgba(255, 255, 255, 0.1);
      border: 2px solid var(--text-muted);
      color: var(--secondary-color);
      font-size: 1.2rem;
      width: 45px; height: 45px; padding: 0;
      border-radius: 50%; cursor: pointer;
      transition: all 0.3s ease;
      display: flex; justify-content: center; align-items: center;
      backdrop-filter: blur(5px);
    }

    .btn-theme-toggle:hover {
      color: var(--text-light);
      background: var(--primary-color);
      transform: rotate(360deg);
      box-shadow: 0 0 20px var(--glow-primary);
      border-color: var(--primary-color);
    }

    .ultra-icon-button {
      width: 45px; height: 45px; padding: 0; font-size: 1.1rem;
      display: flex; justify-content: center; align-items: center;
      border-radius: 50%; border: 2px solid var(--primary-color);
      background: transparent; color: var(--primary-color);
      position: relative; transition: all 0.4s ease;
      box-shadow: 0 0 5px rgba(13, 148, 136, 0.3);
    }
    .ultra-icon-button:hover {
      background: var(--primary-color); color: var(--bg-page);
      transform: scale(1.1); box-shadow: 0 0 20px var(--glow-primary);
    }
    .ultra-icon-button::before, .ultra-icon-button::after { /* Tooltip */ opacity: 0; transition: opacity 0.3s; }
    .ultra-icon-button::before { content: attr(data-tooltip); position: absolute; bottom: -35px; left: 50%; transform: translateX(-50%); background: var(--secondary-color); color: var(--bg-page); padding: 5px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; white-space: nowrap; pointer-events: none; z-index: 1001; }
    .ultra-icon-button:hover::before { opacity: 1; visibility: visible; bottom: -45px; }
    .ultra-icon-button:hover::after { opacity: 1; visibility: visible; }

    /* 4. Contenu Principal et Cartes */
    .main-content { padding: 40px 60px; max-width: 1400px; margin: 0 auto; }
    .page-header { text-align: left; margin-bottom: 50px; padding-left: 15px; }
    .page-header h2 { font-size: 2.5rem; font-weight: 800; margin-bottom: 5px; background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .page-header p { font-size: 1.1rem; color: var(--text-muted); }

    .stat-card, .secondary-content-card, .info-card {
      background: var(--card-bg);
      border-radius: 12px;
      box-shadow: 0 8px 25px var(--shadow-color);
      padding: 30px;
      transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
      border: 1px solid var(--card-border-color);
      position: relative;
      height: 100%;
    }
    .stat-card:hover, .info-card:hover, .promo-card:hover, .mvp-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px var(--shadow-hover);
    }
    
    /* Styles pour les statistiques claires (non graphiques) */
    .stat-card:not(.gauge-card) h2 { font-size: 2.8rem; font-weight: 900; line-height: 1; }
    .stat-card:nth-child(1) h2 { color: var(--primary-color); }
    .stat-card:nth-child(2) h2 { color: #2ecc71; }
    .stat-card:nth-child(3) h2 { color: #f39c12; }
    
    /* Styles pour la Jauge de Dépense */
    .gauge-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .gauge-center-text {
        position: absolute;
        top: 60%; /* Ajusté pour centrer dans la zone visible du demi-cercle */
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        line-height: 1.1;
        pointer-events: none;
    }

    .gauge-center-text h2 {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--primary-color); /* Utilisation de la couleur primaire pour la valeur */
        margin-bottom: 0;
    }

    .gauge-center-text small {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* =================================================================== */
    /* === 5. Styles Nouveaux pour les Infos Client ====================== */
    /* =================================================================== */

    /* Carte d'Information Générale/Confiance */
    .info-card {
      background: linear-gradient(135deg, var(--card-bg) 0%, rgba(255, 107, 107, 0.05) 100%);
      border-left: 5px solid var(--primary-color);
      padding: 25px;
    }
    .info-card h4 { color: var(--primary-color); font-weight: 700; margin-bottom: 15px; }
    .info-card p { color: var(--text-muted); line-height: 1.6; }

    /* Carte des Étapes de Location */
    .loan-steps-card {
      padding: 30px;
    }
    .loan-step {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 8px;
      transition: background 0.3s;
    }
    .loan-step:hover { background: rgba(78, 205, 196, 0.1); }
    .loan-step i {
      font-size: 1.5rem;
      color: var(--secondary-color);
      margin-right: 15px;
      min-width: 30px;
      text-align: center;
    }
    .loan-step span { font-weight: 600; color: var(--text-light); }
    .loan-step small { display: block; color: var(--text-muted); font-size: 0.85rem; }

    /* Carte Promotion */
    .promo-card {
      background: linear-gradient(135deg, var(--promo-bg) 0%, rgba(142, 68, 173, 0.2) 100%);
      border: 1px solid var(--promo-bg);
      text-align: center;
    }
    .promo-card h4 { color: var(--text-light); font-weight: 800; }
    .promo-badge {
      display: inline-block;
      background: var(--primary-color);
      color: white;
      padding: 8px 15px;
      border-radius: 50px;
      font-weight: 700;
      font-size: 1.2rem;
      margin-bottom: 15px;
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0.7); }
      70% { box-shadow: 0 0 0 10px rgba(255, 107, 107, 0); }
      100% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0); }
    }

    /* Carte MVP */
    .mvp-card {
      background: linear-gradient(135deg, var(--card-bg) 0%, rgba(255, 215, 0, 0.1) 100%);
      border: 2px solid var(--mvp-color);
      text-align: center;
    }
    .mvp-card i { font-size: 2.5rem; color: var(--mvp-color); margin-bottom: 15px; }
    .mvp-card h4 { color: var(--mvp-color); font-weight: 800; }
    .mvp-card p { font-size: 0.95rem; }
    .mvp-progress { height: 10px; background-color: var(--card-bg); border-radius: 5px; margin-top: 15px; position: relative; }
    .mvp-bar { height: 100%; width: 60%; /* Remplacer par la valeur réelle du client */ background-color: var(--mvp-color); border-radius: 5px; transition: width 1s ease-out; }
    .mvp-level { color: var(--mvp-color); font-weight: 700; margin-top: 5px; }


    /* Animations */
    .row.g-5 > div[class*="col"] { display: flex; }
    .card-animation { opacity: 0; transform: translateY(20px); animation: fadeInSlide 0.5s ease-out forwards; }
    @keyframes fadeInSlide { to { opacity: 1; transform: translateY(0); } }
  </style>
</head>
<body>

<header class="topbar">
  <a href="{{ route('client.dashboard') }}" class="logo">
    AutoGestion
  </a>

  <nav class="topbar-nav d-none d-lg-flex">
    <a href="{{ route('client.dashboard') }}" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="{{ route('client.vehicles') }}"><i class="fas fa-car"></i> Véhicules</a>
    <a href="{{ route('client.cart') }}"><i class="fas fa-shopping-cart"></i> Panier</a>
    <a href="{{ route('client.loan') }}"><i class="fas fa-key"></i> Locations</a>
    <a href="{{ route('client.transactions') }}"><i class="fas fa-exchange-alt"></i> Transactions</a>
    <a href="{{ route('client.contact') }}"><i class="fas fa-headset"></i> Contact</a>
  </nav>

  <div class="topbar-actions">
    <button id="theme-toggle" class="btn-theme-toggle" aria-label="Basculer le thème clair/sombre">
      <i class="fas fa-moon"></i>
    </button>

    <form method="POST" action="{{ route('logout') }}" class="d-inline">
      @csrf
      {{-- Bouton à icône unique avec un libellé Tooltip --}}
      <button type="submit" class="btn btn-logout ultra-icon-button" data-tooltip="Déconnexion">
        <i class="fas fa-power-off"></i>
      </button>
    </form>
  </div>
</header>

<main class="main-content">
  {{-- Espacement pour le Topbar --}}
  <br>
  <br>

  <div class="page-header">
    <h2>Bienvenue, {{ $userName ?? 'cher client' }} !</h2>
    <p>Gérez vos réservations, suivez vos dépenses et explorez notre catalogue.</p>
  </div>

  <div class="row g-5 mb-5">

    <div class="col-lg-6">
      <div class="info-card card-animation">
        <h4><i class="fas fa-shield-alt me-2"></i> Votre Plateforme de Confiance</h4>
        <p>
          Bienvenue sur votre espace personnel **AutoGestion**. Votre sécurité et votre satisfaction sont nos priorités absolues.
          Toutes vos transactions, informations personnelles et historiques de location sont gérés de manière cryptée et confidentielle.
          **Louez l'esprit tranquille, la route vous appartient.**
        </p>
      </div>
    </div>
<br>
<br>
    <div class="col-lg-6">
      <div class="secondary-content-card loan-steps-card card-animation" style="animation-delay: 0.1s;">
        <h4><i class="fas fa-route me-2"></i> Comment Louer en 3 Étapes Simples</h4>
        <div class="loan-step">
          <i class="fas fa-search"></i>
          <div><span>1. Trouvez votre véhicule</span> <small>Parcourez notre catalogue et ajoutez au panier.</small></div>
        </div>
        <div class="loan-step">
          <i class="fas fa-credit-card"></i>
          <div><span>2. Confirmez et Payez</span> <small>Finalisez votre réservation de manière sécurisée.</small></div>
        </div>
        <div class="loan-step">
          <i class="fas fa-car-side"></i>
          <div><span>3. Récupérez les clés !</span> <small>Rendez-vous au lieu de prise et partez à l'aventure.</small></div>
        </div>
      </div>
    </div>
  </div>
<br>
<br>
  <div class="row g-5 mb-5">
    <div class="col-xl-3 col-md-6">
      <div class="stat-card card-animation" style="animation-delay: 0.2s;">
        <i class="fas fa-shopping-basket stat-card-icon" style="color: var(--primary-color);"></i>
        <h5>VÉHICULES PANIER</h5>
        <h2>{{ $cartCount ?? 0 }}</h2>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="stat-card card-animation" style="animation-delay: 0.3s;">
        <i class="fas fa-road stat-card-icon" style="color: #2ecc71;"></i>
        <h5>LOCATIONS ACTIVES</h5>
        <h2>{{ $loanCount ?? 0 }}</h2>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="stat-card card-animation" style="animation-delay: 0.4s;">
        <i class="fas fa-receipt stat-card-icon" style="color: #f39c12;"></i>
        <h5>TRANSACTIONS TOTALES</h5>
        <h2>{{ $transactionCount ?? 0 }}</h2>
      </div>
    </div>

    {{-- *** BLOC GRAPHIQUE DE DÉPENSE (JAUGE) *** --}}
    <div class="col-xl-3 col-md-6">
      <div class="stat-card gauge-card card-animation" style="animation-delay: 0.5s;">
        <h5 class="mb-3">DÉPENSE ANNUELLE</h5>
        <div style="height: 150px; width: 100%; position: relative;">
          <canvas id="totalSpentGauge"></canvas>
          <div class="gauge-center-text">
            <h2 id="spentAmount">{{ number_format($totalSpent ?? 0, 0, '.', ' ') }}</h2>
            <small class="text-muted">sur $5,000</small> {{-- REMPLACER 5,000 par {{ number_format($budgetTarget ?? 5000) }} --}}
          </div>
        </div>
      </div>
    </div>
    {{-- *** FIN DU BLOC GRAPHIQUE DE DÉPENSE *** --}}
    
  </div>

 

</main>

{{-- =================================================================== --}}
{{-- === SCRIPTS JAVASCRIPT --}}
{{-- =================================================================== --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const toggleButton = document.getElementById('theme-toggle');
  const body = document.body;
  const icon = toggleButton.querySelector('i');
  
  // Données de base (À synchroniser avec vos variables Blade/Laravel)
  const totalSpent = {{ $totalSpent ?? 2500 }}; 
  const budgetTarget = {{ $budgetTarget ?? 5000 }}; // REMPLACER par votre variable si elle existe

  // --- 1. THEME TOGGLE LOGIC ---

  /**
   * Retourne les couleurs pour les graphiques basées sur le mode actuel.
   */
  const getChartColors = () => {
    const isLight = body.classList.contains('light-mode');
    return {
      lineColor: isLight ? '#007bff' : '#FF6B6B', // Primary color
      gridColor: isLight ? '#f0f0f0' : '#2F3C4C', // Background / Grid color
    };
  };


  const applyTheme = (isLight) => {
    if (isLight) {
      body.classList.add('light-mode');
      icon.classList.remove('fa-moon');
      icon.classList.add('fa-sun');
      localStorage.setItem('theme', 'light');
    } else {
      body.classList.remove('light-mode');
      icon.classList.remove('fa-sun');
      icon.classList.add('fa-moon');
      localStorage.setItem('theme', 'dark');
    }
  };


  // --- 2. GAUGE CHART LOGIC ---
  
  window.totalSpentGaugeChart = null; // Variable globale pour la jauge

  function createTotalSpentGauge(totalSpent, budgetTarget) {
    const ctxGauge = document.getElementById('totalSpentGauge');
    if (!ctxGauge) return;

    const spentValue = totalSpent || 0;
    
    // Calcul de la progression
    const percentage = Math.min(100, (spentValue / budgetTarget) * 100);
    const remainingPercentage = 100 - percentage;
    
    // Mise à jour du texte central
    document.getElementById('spentAmount').innerHTML = new Intl.NumberFormat('fr-FR', { useGrouping: true, maximumFractionDigits: 0 }).format(spentValue);

    const colors = getChartColors();

    const gaugeData = {
        datasets: [{
            data: [percentage, remainingPercentage],
            backgroundColor: [colors.lineColor, colors.gridColor],
            borderWidth: 0,
            hoverBackgroundColor: [colors.lineColor, colors.gridColor],
        }]
    };

    if (window.totalSpentGaugeChart) {
        // Mise à jour pour le changement de thème
        window.totalSpentGaugeChart.data.datasets[0].backgroundColor = [colors.lineColor, colors.gridColor];
        window.totalSpentGaugeChart.update();
        return;
    }
    
    // Création initiale
    window.totalSpentGaugeChart = new Chart(ctxGauge, {
        type: 'doughnut',
        data: gaugeData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            circumference: 180, // Demi-cercle
            rotation: -90, // Commence en haut
            cutout: '80%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            if (context.dataIndex === 0) {
                                return `Dépensé: ${context.formattedValue}%`;
                            }
                            return `Restant: ${context.formattedValue}%`;
                        }
                    }
                }
            },
        }
    });
  }


  // --- 3. EXECUTION AU CHARGEMENT ---

  // Chargement initial du thème
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'light') {
    applyTheme(true);
  } else {
    applyTheme(false);
  }
  
  // Création initiale du graphique de jauge
  createTotalSpentGauge(totalSpent, budgetTarget);

  // Événement du bouton
  toggleButton.addEventListener('click', () => {
    const isLight = body.classList.contains('light-mode');
    applyTheme(!isLight);
    // Mettre à jour la jauge lors du changement de thème
    createTotalSpentGauge(totalSpent, budgetTarget); 
  });
});
</script>

</body>
</html>