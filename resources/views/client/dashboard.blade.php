@extends('layouts.client')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1>Bienvenue, {{ $userName ?? 'cher client' }} !</h1>
    <p>Gérez vos réservations et suivez vos activités en un coup d'œil.</p>
</div>


<div class="dashboard-grid">
    <div class="card stat-card">
        <i class="fas fa-shopping-basket icon"></i>
        <h5>VÉHICULES PANIER</h5>
        <h2>{{ $cartCount ?? 0 }}</h2>
    </div>
    <div class="card stat-card">
        <i class="fas fa-key icon"></i>
        <h5>LOCATIONS ACTIVES</h5>
        <h2>{{ $loanCount ?? 0 }}</h2>
    </div>
    <div class="card stat-card">
        <i class="fas fa-exchange-alt icon"></i>
        <h5>TRANSACTIONS</h5>
        <h2>{{ $transactionCount ?? 0 }}</h2>
    </div>
    
    {{-- Kat Jauge a dwe yon 'card' senp pou grid la rekonèt li --}}
    <div class="card gauge-card">
        <h5 class="mb-3">DÉPENSE ANNUELLE</h5>
        <div style="height: 150px; width: 100%; position: relative;">
            <canvas id="totalSpentGauge"></canvas>
            <div class="gauge-center-text">
                <h2 id="spentAmount">{{ number_format($totalSpent ?? 0, 0, '.', ' ') }}</h2>
                <small class="text-muted">sur ${{ number_format($budgetTarget ?? 5000, 0, '.', ' ') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="info-section">
    <div class="card">
        <h4><i class="fas fa-shield-alt"></i> Votre Plateforme</h4>
        <p>Votre sécurité et votre satisfaction sont nos priorités. Toutes vos données sont cryptées.</p>
    </div>
    <div class="card">
        <h4><i class="fas fa-route"></i> Étapes de location</h4>
        <ul class="steps-list">
            <li><span>1</span> Trouvez votre véhicule</li>
            <li><span>2</span> Confirmez et payez</li>
            <li><span>3</span> Récupérez les clés !</li>
        </ul>
    </div>
</div>
@endsection

<style>
/* --- 1. Debaz (Reset & Typography) --- */
.page-header { margin-bottom: 2.5rem; border-left: 5px solid var(--primary); padding-left: 20px; }
.page-header h1 { font-size: 2rem; color: var(--text-color); margin: 0; }
.page-header p { color: var(--secondary); margin-top: 5px; }

/* --- 2. Grid Dashboard --- */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

/* --- 3. Kat (Cards) --- */
.card {
    background: var(--card-bg);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    border: 1px solid rgba(128, 128, 128, 0.1);
    transition: 0.4s ease;
}
.card:hover { transform: translateY(-5px); }

/* Tèks nan kat yo */
.card h5 { 
    font-size: 0.7rem; 
    text-transform: uppercase; 
    letter-spacing: 2px; 
    color: var(--secondary); 
    margin: 0 0 15px 0; 
}
.card h2 { font-size: 2.2rem; color: var(--text-color); margin: 0; }
.card p, .card h4 { color: var(--text-color); }

/* --- 4. Ikon & Grafik --- */
.card .icon {
    font-size: 1.5rem;
    color: var(--primary);
    margin-bottom: 10px;
    opacity: 0.8;
}
.gauge-wrapper { height: 140px; width: 100%; position: relative; margin-top: 10px; }

/* --- 5. Seksyon Enfòmasyon & Lis --- */
.info-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
}
.steps-list { list-style: none; padding: 0; }
.steps-list li { 
    display: flex; align-items: center; margin-bottom: 15px; 
    padding: 12px; 
    background: rgba(128, 128, 128, 0.1); 
    border-radius: 12px; 
    color: var(--text-color);
}
.steps-list li span { 
    background: var(--primary); 
    color: #fff; 
    width: 30px; height: 30px; 
    border-radius: 50%; 
    display: flex; align-items: center; justify-content: center; 
    margin-right: 15px; 
    font-weight: bold; 
}

</style>
<style>
/* ... (rete ak sa w genyen yo) ... */

/* Asire jauge a santre nan kat li */
.gauge-card {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.gauge-center-text {
    position: absolute;
    top: 65%; /* Santre chif la anba koub la */
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    pointer-events: none;
}
</style>


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


















