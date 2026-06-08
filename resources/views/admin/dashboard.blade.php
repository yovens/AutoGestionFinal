@extends('admin.layout')

@section('title', 'Tableau de bord')
@section('page_title', 'Bienvenue, ' . ($adminName ?? 'Administrateur') . ' 👋')

@section('content')
    <p class="intro" style="color: var(--text-muted); margin-bottom: 24px;">Aperçu en temps réel de l'activité globale.</p>

    {{-- STATS GRID --}}
    <div class="grid-stats">
        <div class="stat-card">
            <i class="bi bi-people-fill icon-bg"></i>
            <h5>Utilisateurs</h5><h3>{{ $usersCount }}</h3>
        </div>
        <div class="stat-card">
            <i class="bi bi-car-front-fill icon-bg"></i>
            <h5>Véhicules</h5><h3>{{ $vehiclesCount }}</h3>
        </div>
        <div class="stat-card">
            <i class="bi bi-wallet2 icon-bg"></i>
            <h5>Transactions</h5><h3>{{ $transactionsCount }}</h3>
        </div>
        <div class="stat-card">
            <i class="bi bi-calendar-range-fill icon-bg"></i>
            <h5>Locations</h5><h3>{{ $loansCount }}</h3>
        </div>
    </div>

    {{-- GRAPHIQUES --}}
    <div class="chart-wrapper" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 24px;">
        <div class="stat-card">
            <h5 style="margin-bottom: 20px;">Activité des Transactions</h5>
            <canvas id="lineChart" height="250"></canvas>
        </div>
        <div class="stat-card">
            <h5 style="margin-bottom: 20px;">Type d'Opérations</h5>
            <canvas id="doughnutChart" height="250"></canvas>
        </div>
    </div>

    <style>
        .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .stat-card { background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 14px; padding: 20px; position: relative; overflow: hidden; }
        .icon-bg { position: absolute; right: 10px; top: 10px; font-size: 30px; opacity: 0.2; }
    </style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{ label: 'Transactions', data: {!! json_encode($data) !!}, borderColor: '#3b82f6', tension: 0.4 }]
        }
    });

    // Doughnut Chart
    new Chart(document.getElementById('doughnutChart'), {
        type: 'doughnut',
        data: {
            labels: ['Location', 'Vente'],
            datasets: [{ data: [{{ $locationsCount }}, {{ $salesCount }}], backgroundColor: ['#22c55e', '#3b82f6'] }]
        }
    });
</script>
@endpush