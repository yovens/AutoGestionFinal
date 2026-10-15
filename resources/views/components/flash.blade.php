{{-- Mesaj siksè / erè ki sòti nan sesyon an + erè validasyon. --}}
@if (session('success') || session('error') || $errors->any())
    <div class="flashes" role="status" aria-live="polite">
        @if (session('success'))
            <div class="flash flash--ok">
                <x-icon name="check-circle" />
                <span>{{ session('success') }}</span>
                <button type="button" class="flash__close" aria-label="Fermer"><x-icon name="x" size="sm" /></button>
            </div>
        @endif
        @if (session('error'))
            <div class="flash flash--alert">
                <x-icon name="alert" />
                <span>{{ session('error') }}</span>
                <button type="button" class="flash__close" aria-label="Fermer"><x-icon name="x" size="sm" /></button>
            </div>
        @endif
        @if ($errors->any() && !($hideErrors ?? false))
            <div class="flash flash--alert">
                <x-icon name="alert" />
                <div>
                    @if ($errors->count() === 1)
                        {{ $errors->first() }}
                    @else
                        Corrigez les points suivants :
                        <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    @endif
                </div>
                <button type="button" class="flash__close" aria-label="Fermer"><x-icon name="x" size="sm" /></button>
            </div>
        @endif
    </div>
@endif
