<div class="min-h-screen">
    

    <main class="bg-[#122C4F] text-[#FBF9E4] px-6 py-10">
        <div class="max-w-7xl mx-auto space-y-6">

        
            <div class="relative bg-[#0f203d] text-[#FBF9E4] rounded-2xl p-4 md:p-5 shadow-lg space-y-4"
                wire:key="search-form-{{ $formVersion }}">
                <div class="flex flex-col md:flex-row gap-3 md:items-center">
                    <div class="relative flex-1">
                        <input type="search" wire:model.live.debounce.150ms="q" placeholder="Search products…"
                            class="w-full p-3 rounded bg-[#FBF9E4] text-[#122C4F] placeholder-[#122C4F] font-semibold"
                            autocomplete="off" />

                        {{-- instant suggestions --}}
                        @if(strlen($q) >= 1)
                            <div class="absolute left-0 top-full z-[200] mt-1 w-full max-h-80 overflow-auto
                                                    bg-[#FBF9E4] text-[#122C4F] rounded-xl shadow-2xl">
                                {{-- categories --}}
                                @if(($categoryMatches ?? collect())->count())
                                    <div class="px-3 pt-3 pb-1 text-xs uppercase tracking-wide text-[#666]">Categories</div>
                                    @foreach($categoryMatches as $cat)
                                        <button type="button"
                                            class="w-full text-left px-3 py-2 hover:bg-[#EBEBE0] flex items-center gap-2"
                                            wire:click="goToCategory('{{ $cat }}')">
                                            <span
                                                class="inline-block px-2 py-0.5 rounded-full bg-[#122C4F] text-[#FBF9E4] text-xs">Filter</span>
                                            <span>{{ ucfirst($cat) }}</span>
                                        </button>
                                    @endforeach
                                    <div class="h-px bg-[#ddd] mx-3 my-1"></div>
                                @endif

                                {{-- product name suggestions  --}}
                                @foreach(($suggestions ?? collect()) as $s)
                                    <a href="{{ route('products.show', $s->id) }}"
                                        class="block w-full text-left px-3 py-2 hover:bg-[#EBEBE0] flex items-center gap-3">
                                        <img src="{{ $s->image_url ?? '' }}" onerror="this.style.display='none'"
                                            class="h-8 w-8 object-cover rounded" alt="">
                                        <span>{{ $s->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <button type="button" wire:click="resetFilters"
                            class="px-4 py-2 rounded bg-transparent border border-[#FBF9E4]/40 font-semibold hover:bg-[#FBF9E4]/10">
                            Clear
                        </button>
                    </div>
                </div>

                {{-- inline filters --}}
                <div class="grid md:grid-cols-4 gap-3">
                    <div>
                        <label class="block mb-1 text-sm opacity-80">Category</label>
                        <select wire:model.live="category" class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]">
                            <option value="">All</option>
                            @foreach($categories as $c)
                                <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm opacity-80">Min Price (LKR)</label>
                        <input type="number" min="0" step="1" wire:model.live.debounce.200ms="minPrice"
                            class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm opacity-80">Max Price (LKR)</label>
                        <input type="number" min="0" step="1" wire:model.live.debounce.200ms="maxPrice"
                            class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm opacity-80">Sort</label>
                        <select wire:model.live="sort" class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]">
                            <option value="relevance">Relevance</option>
                            <option value="latest">Latest</option>
                            <option value="price_asc">Price: Low → High</option>
                            <option value="price_desc">Price: High → Low</option>
                            <option value="name">Name A–Z</option>
                        </select>
                    </div>
                </div>
            </div>

            @if($products->count() === 0)
                <p class="opacity-80">No products match your filters.</p>
            @endif

            {{-- grid --}}
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 text-[#122C4F]">
                @foreach($products as $p)
                    <a href="{{ route('products.show', $p->id) }}"
                        class="bg-[#FBF9E4] rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                        <div class="h-48 bg-[#FBF9E4]">
                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-full h-full object-contain p-6">
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold truncate">{{ $p->name }}</h3>
                            <p class="text-xl font-bold mt-1">LKR {{ number_format($p->price, 2) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div>
                {{ $products->links() }}
            </div>
        </div>
    </main>

</div>