<div class="space-y-6">

    {{-- inline filters bar on the results page --}}
    <div class="bg-[#0f203d] rounded-2xl p-4">
        <div class="grid md:grid-cols-5 gap-3">
            <input type="search" placeholder="Search…" class="p-2 rounded bg-[#FBF9E4] text-[#122C4F]"
                wire:model.debounce.250ms="q">

            <select class="p-2 rounded bg-[#FBF9E4] text-[#122C4F]" wire:model.debounce.150ms="category">
                <option value="">All categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                @endforeach
            </select>

            <input type="number" min="0" step="1" placeholder="Min LKR" class="p-2 rounded bg-[#FBF9E4] text-[#122C4F]"
                wire:model.debounce.300ms="minPrice">

            <input type="number" min="0" step="1" placeholder="Max LKR" class="p-2 rounded bg-[#FBF9E4] text-[#122C4F]"
                wire:model.debounce.300ms="maxPrice">

            <select class="p-2 rounded bg-[#FBF9E4] text-[#122C4F]" wire:model.debounce.150ms="sort">
                <option value="relevance">Relevance</option>
                <option value="latest">Latest</option>
                <option value="price_asc">Price: Low → High</option>
                <option value="price_desc">Price: High → Low</option>
                <option value="name">Name A-Z</option>
            </select>
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

    {{-- pagination --}}
    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>