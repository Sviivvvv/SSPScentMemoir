<x-layouts.site>
    

    <main class="px-6 py-10">
        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-10">

            {{-- Product image --}}
            <div class="bg-[#FBF9E4] rounded-2xl p-6">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-auto object-contain">
            </div>

            {{-- Info + Add to Cart --}}
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $product->name }}</h1>
                <p class="opacity-80 mb-4 capitalize">
                    {{ optional($product->categoryRef)->name ?? $product->category }}
                </p>
                <p class="text-2xl font-bold mb-6">LKR {{ number_format($product->price, 2) }}</p>

                @if($product->description)
                    <p class="mb-6 max-w-prose">{{ $product->description }}</p>
                @endif

                {{-- Livewire widget (session cart for now) --}}
                <livewire:cart.add-to-cart :product-id="$product->id" />
            </div>
        </div>

        {{-- Recommendations --}}
        <section class="mt-16">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">You may also like</h2>
            </div>

            <div class="flex gap-6 overflow-x-auto pb-4 text-[#122C4F]">
                @forelse($recommendations as $p)
                    <a href="{{ route('products.show', $p->id) }}"
                        class="flex-shrink-0 w-64 bg-[#FBF9E4] rounded-xl shadow hover:shadow-lg transition overflow-hidden group">
                        <div class="h-48 bg-[#FBF9E4]">
                            <img src="{{ $p->image_url }}"
                                class="w-full h-full object-contain p-6 transition group-hover:scale-105">
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold truncate">{{ $p->name }}</h3>
                            <p class="text-xl font-bold mt-1">LKR {{ number_format($p->price, 2) }}</p>
                        </div>
                    </a>
                @empty
                    <p class="opacity-70">No recommendations.</p>
                @endforelse
            </div>
        </section>
    </main>

</x-layouts.site>