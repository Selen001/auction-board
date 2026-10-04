@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="btn btn-secondary disabled">&larr; Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-secondary">&larr; Previous</a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-secondary">Next &rarr;</a>
        @else
            <span class="btn btn-secondary disabled">Next &rarr;</span>
        @endif
    </nav>
@endif