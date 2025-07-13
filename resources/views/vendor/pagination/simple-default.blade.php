@if (isset($paginator) && $paginator->hasPages())
    <div class="pagerArea">
        <ul class="pagerBox">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled">
                    <span class="btnDisable">&lsaquo;</span>
                </li>
            @else
                <li>
                    <a class="pagerBtn with-overlay" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a class="pagerBtn with-overlay" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="disabled">
                    <span class="btnDisable">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </div>
@endif