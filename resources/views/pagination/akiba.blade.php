@if ($paginator->hasPages())
    <nav class="flex items-center gap-2" role="navigation" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="px-4 py-2 rounded-full border border-outline-variant text-outline font-label-sm text-label-sm uppercase tracking-widest cursor-not-allowed">Précédent</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 rounded-full border border-primary text-primary font-label-sm text-label-sm uppercase tracking-widest hover:bg-primary hover:text-on-primary transition-colors">Précédent</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-2 font-label-sm text-label-sm text-outline">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-10 h-10 flex items-center justify-center rounded-full bg-primary text-on-primary font-label-sm text-label-sm">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center rounded-full border border-outline-variant text-on-surface font-label-sm text-label-sm hover:border-primary transition-colors">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 rounded-full border border-primary text-primary font-label-sm text-label-sm uppercase tracking-widest hover:bg-primary hover:text-on-primary transition-colors">Suivant</a>
        @else
            <span class="px-4 py-2 rounded-full border border-outline-variant text-outline font-label-sm text-label-sm uppercase tracking-widest cursor-not-allowed">Suivant</span>
        @endif
    </nav>
@endif
