@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        <ul class="pagination justify-content-center align-items-center gap-3 mb-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link border-0 bg-light text-muted rounded-3 px-3 py-2 shadow-sm"
                        style="cursor: not-allowed;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="page-link border-0 bg-white text-primary rounded-3 px-3 py-2 shadow-sm transition-all"
                        rel="prev" aria-label="Previous" style="transition: all 0.3s ease;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link border-0 bg-transparent text-muted px-3">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active mx-1" aria-current="page">
                                <span class="page-link border-0 rounded-3 shadow px-4 py-2 fw-semibold"
                                    style="background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); color: white; min-width: 45px; display: inline-block; text-align: center;">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item mx-1">
                                <a href="{{ $url }}"
                                    class="page-link border-0 bg-white text-dark rounded-3 px-4 py-2 shadow-sm fw-medium"
                                    style="transition: all 0.3s ease; min-width: 45px; display: inline-block; text-align: center;"
                                    onmouseover="this.style.background='linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%)'; this.style.color='white'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(14, 165, 233, 0.4)';"
                                    onmouseout="this.style.background='white'; this.style.color='#212529'; this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.12)';">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="page-link border-0 bg-white text-primary rounded-3 px-3 py-2 shadow-sm" rel="next"
                        aria-label="Next" style="transition: all 0.3s ease;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link border-0 bg-light text-muted rounded-3 px-3 py-2 shadow-sm"
                        style="cursor: not-allowed;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </span>
                </li>
            @endif
        </ul>
    </nav>

    <style>
        .page-link:hover:not(.active .page-link) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4) !important;
        }

        .page-item.active .page-link {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 2px 8px rgba(14, 165, 233, 0.4);
            }

            50% {
                box-shadow: 0 4px 16px rgba(14, 165, 233, 0.6);
            }
        }
    </style>
@endif
