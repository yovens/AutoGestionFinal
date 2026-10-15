{{-- Dyalòg pou ankese yon acha ki te rezève ($t = Transaction). --}}
<x-dialog id="sale-{{ $t->id }}" title="Encaisser l’achat {{ $t->reference }}" size="sm">
    <form method="POST" action="{{ route('counter.sales.confirm', $t) }}">
        @csrf
        <div class="modal__body stack">
            <div class="row" style="flex-wrap:nowrap">
                <x-plate :plate="$t->vehicle?->plate ?? '—'" />
                <div>
                    <div class="strong">{{ $t->vehicle?->name }}</div>
                    <div class="small muted">{{ $t->user?->nom }}</div>
                </div>
            </div>
            <div class="bill"><div class="bill__row bill__row--total"><span>À encaisser</span><span class="bill__value">@money($t->amount)</span></div></div>
            <x-methods :id-prefix="'sale' . $t->id" required />
            <p class="small muted">Après l’encaissement, le bon de remise s’imprime : faites-le signer en remettant les clés et les papiers.</p>
        </div>
        <div class="modal__foot">
            <button type="button" class="btn" data-dialog-close>Fermer</button>
            <button type="submit" class="btn btn--ok">Encaissé, remettre le véhicule</button>
        </div>
    </form>
    <div class="modal__foot" style="justify-content:flex-start;border-top:0;padding-top:0">
        <form method="POST" action="{{ route('counter.sales.cancel', $t) }}" data-confirm="Annuler cet achat ? Le véhicule sera remis en vente.">
            @csrf<button type="submit" class="btn btn--sm btn--ghost btn--danger">Annuler l’achat</button>
        </form>
    </div>
</x-dialog>
