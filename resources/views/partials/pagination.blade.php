@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex justify-center items-center mt-10 space-x-1 select-none">
        
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="h-8 w-8 flex items-center justify-center rounded text-gray-300 border border-gray-100 bg-white cursor-not-allowed">
                <i class='bx bx-chevron-left text-lg'></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="h-8 w-8 flex items-center justify-center rounded text-gray-500 border border-gray-200 bg-white hover:bg-maroon-50 hover:text-maroon-700 hover:border-maroon-200 transition duration-150 ease-in-out">
                <i class='bx bx-chevron-left text-lg'></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="h-8 w-8 flex items-center justify-center text-gray-400 text-xs tracking-widest font-light">
                    {{ $element }}
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="h-8 w-8 flex items-center justify-center rounded bg-maroon-700 text-white font-bold text-xs border border-maroon-700 shadow-sm">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="h-8 w-8 flex items-center justify-center rounded text-gray-600 border border-transparent hover:bg-gray-100 hover:text-maroon-700 transition duration-150 ease-in-out text-xs font-medium">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="h-8 w-8 flex items-center justify-center rounded text-gray-500 border border-gray-200 bg-white hover:bg-maroon-50 hover:text-maroon-700 hover:border-maroon-200 transition duration-150 ease-in-out">
                <i class='bx bx-chevron-right text-lg'></i>
            </a>
        @else
            <span class="h-8 w-8 flex items-center justify-center rounded text-gray-300 border border-gray-100 bg-white cursor-not-allowed">
                <i class='bx bx-chevron-right text-lg'></i>
            </span>
        @endif
    </nav>
@endif