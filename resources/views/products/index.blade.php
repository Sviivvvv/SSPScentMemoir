<x-layouts.site>
   

    <main class="bg-[#122C4F] text-[#FBF9E4] min-h-screen">
        <livewire:shop.product-search-bar />
        {{-- LIMITED banner --}}
        <section class="w-full mt-20 mb-10">
            @if($banners['limited_desktop'])
                <div class="hidden sm:block w-full max-w-screen-2xl mx-auto">
                    <img src="{{ $banners['limited_desktop'] }}" alt="Limited Products"
                        class="w-full h-auto object-cover rounded-xl transition-transform duration-300 hover:scale-102">
                </div>
            @endif
            @if($banners['limited_mobile'])
                <div class="sm:hidden w-full">
                    <img src="{{ $banners['limited_mobile'] }}" alt="Limited Products"
                        class="w-full h-auto object-cover rounded-xl transition-transform duration-300 hover:scale-102">
                </div>
            @endif
        </section>

        {{-- LIMITED products (grid + pagination) --}}
        <section class="p-6 mt-10 sm:mt-16 md:mt-10">
            <h2 class="text-xl font-bold mb-6">Limited-Time Offers</h2>

            @if($limited->count())
                <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($limited as $p)
                        <a href="{{ route('products.show', $p->id) }}"
                            class="bg-[#FBF9E4] text-[#122C4F] rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                            <div class="h-48 bg-[#FBF9E4]">
                                <img src="{{ $p->image_url }}" alt="{{ $p->name }}"
                                    class="w-full h-full object-contain p-6 transition duration-300 hover:scale-105">
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold truncate">{{ $p->name }}</h3>
                                <p class="text-xl font-bold mt-1">LKR {{ number_format($p->price, 2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{-- keep other query params so multiple pagers can coexist --}}
                    {{ $limited->withQueryString()->onEachSide(1)->links() }}
                </div>
            @else
                <p class="opacity-80">No limited products yet.</p>
            @endif
        </section>

        {{-- MEN banner --}}
        <section class="w-full mt-20 mb-10">
            @if($banners['men_desktop'])
                <div class="hidden sm:block w-full max-w-screen-2xl mx-auto">
                    <img src="{{ $banners['men_desktop'] }}" alt="Men's Products"
                        class="w-full h-auto object-cover rounded-xl transition-transform duration-300 hover:scale-102">
                </div>
            @endif
            @if($banners['men_mobile'])
                <div class="sm:hidden w-full">
                    <img src="{{ $banners['men_mobile'] }}" alt="Men's Products"
                        class="w-full h-auto object-cover rounded-xl transition-transform duration-300 hover:scale-102">
                </div>
            @endif
        </section>

        {{-- MEN products (grid + pagination) --}}
        <section class="p-6 mt-10 sm:mt-16 md:mt-16">
            <h2 class="text-xl font-bold mb-6">Men's Products</h2>

            @if($men->count())
                <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($men as $p)
                        <a href="{{ route('products.show', $p->id) }}"
                            class="bg-[#FBF9E4] text-[#122C4F] rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                            <div class="h-48 bg-[#FBF9E4]">
                                <img src="{{ $p->image_url }}" alt="{{ $p->name }}"
                                    class="w-full h-full object-contain p-6 transition duration-300 hover:scale-105">
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold truncate">{{ $p->name }}</h3>
                                <p class="text-xl font-bold mt-1">LKR {{ number_format($p->price, 2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $men->withQueryString()->onEachSide(1)->links() }}
                </div>
            @else
                <p class="opacity-80">No men’s products yet.</p>
            @endif
        </section>

        {{-- WOMEN banner --}}
        <section class="w-full mt-20 mb-10">
            @if($banners['women_desktop'])
                <div class="hidden sm:block w-full max-w-screen-2xl mx-auto">
                    <img src="{{ $banners['women_desktop'] }}" alt="Women's Products"
                        class="w-full h-auto object-cover rounded-xl transition-transform duration-300 hover:scale-102">
                </div>
            @endif
            @if($banners['women_mobile'])
                <div class="sm:hidden w-full">
                    <img src="{{ $banners['women_mobile'] }}" alt="Women's Products"
                        class="w-full h-auto object-cover rounded-xl transition-transform duration-300 hover:scale-102">
                </div>
            @endif
        </section>

        {{-- WOMEN products (grid + pagination) --}}
        <section class="p-6 mt-10 sm:mt-16 md:mt-10 mb-24">
            <h2 class="text-xl font-bold mb-6">Women's Products</h2>

            @if($women->count())
                <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($women as $p)
                        <a href="{{ route('products.show', $p->id) }}"
                            class="bg-[#FBF9E4] text-[#122C4F] rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                            <div class="h-48 bg-[#FBF9E4]">
                                <img src="{{ $p->image_url }}" alt="{{ $p->name }}"
                                    class="w-full h-full object-contain p-6 transition duration-300 hover:scale-105">
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold truncate">{{ $p->name }}</h3>
                                <p class="text-xl font-bold mt-1">LKR {{ number_format($p->price, 2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $women->withQueryString()->onEachSide(1)->links() }}
                </div>
            @else
                <p class="opacity-80">No women’s products yet.</p>
            @endif
        </section>

    </main>

    
</x-layouts.site>