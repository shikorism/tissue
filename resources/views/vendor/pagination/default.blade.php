@if ($paginator->hasPages())
    {{-- for PC : >= lg --}}
    <ul class="pagination d-none d-lg-flex {{ $className ?? '' }}">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
        @else
            <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
        @endif
    </ul>
    {{-- for Phone : <= md --}}
    <ul class="pagination d-flex d-lg-none {{ $className ?? '' }}">
        @if ($paginator->onFirstPage())
            <li class="page-item w-25 text-center disabled"><span class="page-link">&laquo;</span></li>
        @else
            <li class="page-item w-25 text-center"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
        @endif

        <li class="page-item w-25 text-center"><span class="page-link">{{ $paginator->currentPage() }}</span></li>

        @if ($paginator->hasMorePages())
            <li class="page-item w-25 text-center"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="page-item w-25 text-center disabled"><span class="page-link">&raquo;</span></li>
        @endif
    </ul>
@endif
