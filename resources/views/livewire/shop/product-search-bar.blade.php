<div class="bg-[#0f203d] text-[#FBF9E4] rounded-2xl p-4 md:p-5 shadow-lg space-y-4" wire:key="top-search-bar">
    <div class="flex flex-col md:flex-row gap-3 md:items-center">
        <div class="relative flex-1">
            <input type="search" wire:model.live.debounce.150ms="q" wire:keydown.enter.prevent="submit"
                placeholder="Search products…"
                class="w-full p-3 rounded bg-[#FBF9E4] text-[#122C4F] placeholder-[#122C4F] font-semibold"
                autocomplete="off" />

            @if(strlen($q) >= 1 && ($suggestions->count() || $categoryMatches->count()))
                <div
                    class="absolute left-0 top-full z-50 mt-1 w-full max-h-80 overflow-auto bg-[#FBF9E4] text-[#122C4F] rounded-xl shadow-xl">
                    @if($categoryMatches->count())
                        <div class="px-3 pt-3 pb-1 text-xs uppercase tracking-wide text-[#666]">Categories</div>
                        @foreach($categoryMatches as $cat)
                            <button type="button" class="w-full text-left px-3 py-2 hover:bg-[#EBEBE0] flex items-center gap-2"
                                wire:click="goToCategory('{{ $cat }}')">
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full bg-[#122C4F] text-[#FBF9E4] text-xs">Filter</span>
                                <span>{{ ucfirst($cat) }}</span>
                            </button>
                        @endforeach
                        @if($suggestions->count())
                            <div class="h-px bg-[#ddd] mx-3 my-1"></div>
                        @endif
                    @endif

                    @foreach($suggestions as $s)
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
            <button type="button" wire:click="$toggle('showFilters')"
                class="px-4 py-2 rounded bg-[#FBF9E4] text-[#122C4F] font-semibold hover:opacity-90">
                Filters
            </button>

            {{-- ✅ call the renamed method and prevent default --}}
            <button type="button" wire:click.prevent="clearFilters" wire:loading.attr="disabled"
                class="px-4 py-2 rounded bg-transparent border border-[#FBF9E4]/40 font-semibold hover:bg-[#FBF9E4]/10">
                Clear
            </button>

            <button type="button" wire:click="submit"
                class="px-4 py-2 rounded bg-[#FBF9E4] text-[#122C4F] font-semibold hover:opacity-90">
                Search
            </button>
        </div>
    </div>


    @if($showFilters)
        {{-- 👇 bump this key so inputs remount with blank/default values --}}
        <div class="grid md:grid-cols-4 gap-3" wire:key="filters-{{ $filtersVersion }}">
            <div>
                <label class="block mb-1 text-sm opacity-80">Category</label>
                <select wire:model.live.debounce.150ms="category" class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]">
                    <option value="">All</option>
                    @foreach($categories as $c)
                        <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1 text-sm opacity-80">Min Price (LKR)</label>
                <input type="number" min="0" step="1" wire:model.live.debounce.250ms="minPrice"
                    class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]">
            </div>

            <div>
                <label class="block mb-1 text-sm opacity-80">Max Price (LKR)</label>
                <input type="number" min="0" step="1" wire:model.live.debounce.250ms="maxPrice"
                    class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]">
            </div>

            <div>
                <label class="block mb-1 text-sm opacity-80">Sort</label>
                <select wire:model.live.debounce.150ms="sort" class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]">
                    <option value="relevance">Relevance</option>
                    <option value="latest">Latest</option>
                    <option value="price_asc">Price: Low → High</option>
                    <option value="price_desc">Price: High → Low</option>
                    <option value="name">Name A–Z</option>
                </select>
            </div>
        </div>
    @endif
</div>