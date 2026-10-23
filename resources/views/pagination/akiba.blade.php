@if ($paginator->hasPages())
    <nav class="flex items-center gap-2" role="navigation" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="px-4 py-2 rounded-[10px] border border-outline-variant text-outline text-[14px] font-semibold cursor-not-allowed">Précédent</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 rounded-[10px] border border-outline-variant bg-surface-container-lowest text-on-surface text-[14px] font-semibold hover:border-primary hover:text-primary transition-colors">Précédent</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-2 font-label-sm text-label-sm text-outline">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-10 h-10 flex items-center justify-center rounded-[10px] bg-primary text-on-primary text-[14px] font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center rounded-[10px] border border-outline-variant bg-surface-container-lowest text-on-surface text-[14px] font-semibold hover:border-primary transition-colors">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 rounded-[10px] border border-outline-variant bg-surface-container-lowest text-on-surface text-[14px] font-semibold hover:border-primary hover:text-primary transition-colors">Suivant</a>
        @else
            <span class="px-4 py-2 rounded-[10px] border border-outline-variant text-outline text-[14px] font-semibold cursor-not-allowed">Suivant</span>
        @endif
    </nav>
@endif
