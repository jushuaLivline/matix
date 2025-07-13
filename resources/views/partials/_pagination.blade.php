@if (isset($paginator) && $paginator->hasPages())
    <div class="pagerArea">
        <ul class="pagerBox">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <a class="pagerBtn btnDisable" aria-hidden="true">&lsaquo;</a>
                </li>
            @else
                <li>
                    <a class="pagerBtn with-overlay" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li aria-disabled="true"><a class="pagerBtn btnDisable">{{ $element }}</a></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><a class="pagerBtn currentMark">{{ $page }}</a></li>
                        @else
                            <li><a class="pagerBtn with-overlay" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a class="pagerBtn with-overlay" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <a class="pagerBtn btnDisable" aria-hidden="true">&rsaquo;</a>
                </li>
            @endif
        </ul>
    </div>
@endif

