@if ($paginator->hasPages())
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="opacity:.4;">Previous</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}">Previous</a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding:8px 6px;">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}">Next</a>
    @else
        <span style="opacity:.4;">Next</span>
    @endif
@endif
