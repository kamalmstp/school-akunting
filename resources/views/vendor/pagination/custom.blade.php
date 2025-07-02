@if ($paginator->hasPages())
    <nav aria-label="Page Navigation">
        <ul class="pagination">
            @if ($paginator->onFirstPage())
            <li class="page-item">
                <span class="page-link" aria-hidden="true">
                    <i class="bi bi-arrow-left"></i>
                </span>
            </li>
            @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                    <span aria-hidden="true">
                        <i class="bi bi-arrow-left"></i>
                    </span>
                </a>
            </li>
            @endif
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $pageRange = 4; // Number of pages to show before and after the current page
                $ellipsis = '<li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>';
            @endphp
            @if ($lastPage <= 7) {{-- Show all page numbers --}}
                @for ($i = 1; $i <= $lastPage; $i++)
                    <li class="page-item {{ ($i == $currentPage) ? 'active' : '' }}"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
                @endfor
            @else {{-- Show ellipsis and range of pages around the current page --}}
                @if ($currentPage <= 4) {{-- First few pages --}}
                    @for ($i = 1; $i <= 5; $i++)
                        <li class="page-item {{ ($i == $currentPage) ? 'active' : '' }}"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
                    @endfor
                    {!! $ellipsis !!}
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a></li>
                @elseif ($currentPage >= $lastPage - 3) {{-- Last few pages --}}
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}">1</a></li>
                    {!! $ellipsis !!}
                    @for ($i = $lastPage - 4; $i <= $lastPage; $i++)
                        <li class="page-item {{ ($i == $currentPage) ? 'active' : '' }}"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
                    @endfor
                @else {{-- Middle pages --}}
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}">1</a></li>
                    {!! $ellipsis !!}
                    @for ($i = $currentPage - 1; $i <= $currentPage + 1; $i++)
                        <li class="page-item {{ ($i == $currentPage) ? 'active' : '' }}"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
                    @endfor
                    {!! $ellipsis !!}
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a></li>
                @endif
            @endif
            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
                        aria-label="@lang('pagination.next')">
                        <span aria-hidden="true">
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif