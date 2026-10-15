@if ($paginator->hasPages())
    <nav class="row row--between" aria-label="Pagination" style="padding:var(--s3) var(--s5)">
        <span class="small muted">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} sur {{ $paginator->total() }}</span>
        <span class="row row--tight">
            @if ($paginator->onFirstPage())
                <span class="btn btn--sm" aria-disabled="true"><x-icon name="chevron-left" size="sm" /> Précédent</span>
            @else
                <a class="btn btn--sm" href="{{ $paginator->previousPageUrl() }}" rel="prev"><x-icon name="chevron-left" size="sm" /> Précédent</a>
            @endif
            <span class="small muted">Page {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
            @if ($paginator->hasMorePages())
                <a class="btn btn--sm" href="{{ $paginator->nextPageUrl() }}" rel="next">Suivant <x-icon name="chevron-right" size="sm" /></a>
            @else
                <span class="btn btn--sm" aria-disabled="true">Suivant <x-icon name="chevron-right" size="sm" /></span>
            @endif
        </span>
    </nav>
@endif
