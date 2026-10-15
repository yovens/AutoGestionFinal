@props(['level'])
@if ($level === null)
    <span class="muted">—</span>
@else
    <span class="fuel-read"><i style="--lvl: {{ $level * 25 }}%"></i>{{ \App\Models\LoanCart::fuelLabel($level) }}</span>
@endif
