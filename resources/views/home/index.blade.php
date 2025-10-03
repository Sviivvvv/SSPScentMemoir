<x-layouts.site>
    
    <style>
        .scroll-x { scrollbar-width: none; }
        .scroll-x::-webkit-scrollbar { display: none; }
    </style>

    <main class="px-6 py-10">

        {{-- ADS --}}
        <section class="p-6 mb-5">
            <h2 class="text-xl font-bold mb-4">Advertisements</h2>

            <div class="relative">
                <button type="button"
                    class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-[#FBF9E4] text-[#122C4F] px-3 py-1 rounded shadow"
                    aria-label="Scroll left"
                    data-scroll="#adsScroll" data-dir="-1">&larr;</button>

                <div id="adsScroll"
                    class="scroll-x flex space-x-4 pr-10 snap-x snap-mandatory overflow-x-auto scroll-smooth focus:outline-none"
                    tabindex="0"
                    aria-label="Advertisement list (horizontal scroll)">
                    @foreach($ads as $ad)
                        @php $href = $ad->link_url ?: null; @endphp
                        <div class="min-w-[220px] rounded-2xl flex-shrink-0 snap-center overflow-hidden bg-[#FBF9E4] text-[#122C4F]">
                            <div class="relative">
                                @if($href)
                                    <a href="{{ $href }}" target="_blank" rel="noopener"
                                       class="block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#122C4F]">
                                        <img src="{{ Storage::url($ad->image_path) }}" alt="{{ $ad->title ?? 'Ad' }}"
                                             class="w-full h-60 object-cover">
                                    </a>
                                @else
                                    <img src="{{ Storage::url($ad->image_path) }}" alt="{{ $ad->title ?? 'Ad' }}"
                                         class="w-full h-60 object-cover">
                                @endif
                            </div>
                            @if($ad->title)
                                <div class="px-3 py-2">
                                    @if($href)
                                        <a href="{{ $href }}" target="_blank" rel="noopener"
                                           class="block font-extrabold text-xl md:text-2xl underline underline-offset-8 hover:opacity-80">
                                            {{ $ad->title }}
                                        </a>
                                    @else
                                        <p class="font-extrabold text-xl md:text-2xl">
                                            {{ $ad->title }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button type="button"
                    class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-[#FBF9E4] text-[#122C4F] px-3 py-1 rounded shadow"
                    aria-label="Scroll right"
                    data-scroll="#adsScroll" data-dir="1">&rarr;</button>
            </div>
        </section>

        {{-- LIMITED PRODUCTS --}}
        <section class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">Limited-Time Offers</h2>
                <a href="{{ route('products.index') }}" class="font-bold hover:opacity-80">View More &rarr;</a>
            </div>

            <div class="relative">
                <button type="button"
                    class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-[#FBF9E4] text-[#122C4F] px-3 py-1 rounded shadow"
                    aria-label="Scroll left"
                    data-scroll="#limitedScroll" data-dir="-1">&larr;</button>

                <div id="limitedScroll"
                    class="scroll-x flex gap-6 pb-2 pr-10 text-[#122C4F] snap-x snap-mandatory overflow-x-auto scroll-smooth focus:outline-none"
                    tabindex="0"
                    aria-label="Limited time products (horizontal scroll)">
                    @foreach($products as $p)
                        <a href="{{ route('products.show', $p->id) }}"
                           class="flex-shrink-0 w-64 bg-[#FBF9E4] rounded-xl shadow overflow-hidden snap-start">
                            <div class="h-48">
                                <img src="{{ $p->image_url }}" alt="{{ $p->name }}"
                                     class="w-full h-full object-contain p-6">
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold truncate">{{ $p->name }}</h3>
                                <p class="text-xl font-bold mt-1">LKR {{ number_format($p->price, 2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <button type="button"
                    class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-[#FBF9E4] text-[#122C4F] px-3 py-1 rounded shadow"
                    aria-label="Scroll right"
                    data-scroll="#limitedScroll" data-dir="1">&rarr;</button>
            </div>
        </section>

        {{-- Reviews --}}
        @if($reviews->count())
            <section class="p-6 mt-10">
                <h2 class="text-xl font-bold mb-6">Customer Reviews</h2>

                {{-- mobile horizontal scroll --}}
                <div class="sm:hidden flex space-x-4 overflow-x-auto scroll-smooth">
                    @foreach($reviews as $r)
                        <div class="min-w-[220px] rounded-2xl overflow-hidden bg-[#FBF9E4] text-[#122C4F]">
                            @if($r->image_path)
                                <img src="{{ Storage::url($r->image_path) }}" class="w-full h-60 object-cover">
                            @endif
                            <div class="p-4">
                                <p class="font-bold">{{ $r->author_name }}</p>
                                @if($r->quote)
                                    <p class="mt-1 italic">“{{ $r->quote }}”</p>
                                @endif
                                <p class="mt-1">⭐ {{ $r->rating }}/5</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- desktop grid --}}
                <div class="hidden sm:grid grid-cols-4 gap-4">
                    @foreach($reviews as $r)
                        <div class="rounded-2xl overflow-hidden bg-[#FBF9E4] text-[#122C4F]">
                            @if($r->image_path)
                                <img src="{{ Storage::url($r->image_path) }}" class="w-full h-64 object-cover">
                            @endif
                            <div class="p-4">
                                <p class="font-bold">{{ $r->author_name }}</p>
                                @if($r->quote)
                                    <p class="mt-1 italic">“{{ $r->quote }}”</p>
                                @endif
                                <p class="mt-1">⭐ {{ $r->rating }}/5</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </main>
</x-layouts.site>
