@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="mt-6 flex justify-center">
        <ul class="inline-flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 rounded border border-[#122C4F]/20 text-[#122C4F] opacity-50 cursor-not-allowed">
                    ← Prev
                </span>
            @else
                <a wire:navigate href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="px-3 py-2 rounded border border-[#122C4F]/20 text-[#122C4F] hover:bg-[#122C4F]/5">
                    ← Prev
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-2 text-[#122C4F]">…</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="px-3 py-2 rounded bg-[#122C4F] text-[#FBF9E4] font-semibold">
                                {{ $page }}
                            </span>
                        @else
                            <a wire:navigate href="{{ $url }}"
                                class="px-3 py-2 rounded border border-[#122C4F]/20 text-[#122C4F] hover:bg-[#122C4F]/5">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a wire:navigate href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="px-3 py-2 rounded border border-[#122C4F]/20 text-[#122C4F] hover:bg-[#122C4F]/5">
                    Next →
                </a>
            @else
                <span class="px-3 py-2 rounded border border-[#122C4F]/20 text-[#122C4F] opacity-50 cursor-not-allowed">
                    Next →
                </span>
            @endif
        </ul>
    </nav>
@endif