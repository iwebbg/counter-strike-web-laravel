@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="{{ __('ui.pagination.label') }}">
        @if ($paginator->onFirstPage())
            <span class="pagination-step is-disabled">{{ __('ui.pagination.previous') }}</span>
        @else
            <a class="pagination-step" href="{{ $paginator->previousPageUrl() }}" rel="prev">{{ __('ui.pagination.previous') }}</a>
        @endif

        <div class="pagination-pages">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-ellipsis">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-page is-current" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="pagination-page" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        @if ($paginator->hasMorePages())
            <a class="pagination-step" href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('ui.pagination.next') }}</a>
        @else
            <span class="pagination-step is-disabled">{{ __('ui.pagination.next') }}</span>
        @endif
    </nav>
@endif
