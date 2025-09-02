<div class="bg-[#122C4F] text-[#FBF9E4] px-6 py-10 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-extrabold mb-6">Shopping Cart</h1>

        @if (empty($rows))
            <p class="mb-4">Your cart is empty.</p>
            <div class="flex gap-3">
                <a href="{{ route('products.index') }}"
                    class="px-4 py-2 rounded border border-[#FBF9E4]/40 hover:bg-[#FBF9E4]/10">Continue shopping</a>
                <a href="{{ route('orders.history') }}"
                    class="px-4 py-2 rounded border border-[#FBF9E4]/40 hover:bg-[#FBF9E4]/10">View orders</a>
            </div>
        @else
        <div class="grid lg:grid-cols-[1fr_380px] gap-6">
            {{-- items --}}
            <div class="space-y-4">
                @foreach ($rows as $row)
                @php($p = $row['product'])
                <div class="bg-[#0f203d] rounded-2xl p-4 md:p-5 shadow-lg" wire:key="cart-item-{{ $p->id }}">
                    {{-- Grid keeps columns aligned even with long names --}}
                    <div class="md:grid md:grid-cols-[88px_1fr_auto_auto] md:items-center md:gap-4">
                        {{-- image --}}
                        <div class="w-20 h-20 bg-[#FBF9E4] rounded-xl flex items-center justify-center mb-3 md:mb-0">
                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}"
                                class="max-h-16 max-w-16 object-contain">
                        </div>

                        {{-- name + price --}}
                        <div class="min-w-0">
                            <div class="font-semibold truncate" title="{{ $p->name }}">{{ $p->name }}</div>
                            <div class="opacity-80">LKR {{ number_format($p->price, 2) }}</div>
                            <button wire:click="remove({{ $p->id }})" wire:loading.attr="disabled"
                                class="mt-2 text-red-300 hover:text-red-200 underline text-sm">
                                Remove
                            </button>
                        </div>

                        {{-- qty controls (never shrink) --}}
                        <div class="flex items-center gap-2 justify-self-center shrink-0">
                            <button wire:click="decrement({{ $p->id }})" wire:loading.attr="disabled"
                                aria-label="Decrease quantity"
                                class="h-10 w-10 rounded bg-[#FBF9E4] text-[#122C4F] font-bold">−</button>
                            <span class="w-6 text-center">{{ $row['qty'] }}</span>
                            <button wire:click="increment({{ $p->id }})" wire:loading.attr="disabled"
                                aria-label="Increase quantity"
                                class="h-10 w-10 rounded bg-[#FBF9E4] text-[#122C4F] font-bold">+</button>
                        </div>

                        {{-- line total --}}
                        <div class="font-semibold whitespace-nowrap justify-self-end">
                            LKR {{ number_format($row['subtotal'], 2) }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- summary --}}
            <aside class="bg-[#FBF9E4] text-[#122C4F] rounded-2xl p-4 md:p-5 shadow-lg h-fit lg:sticky lg:top-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold">Total</h2>
                    <div class="text-xl font-extrabold">LKR {{ number_format($total, 2) }}</div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button wire:click="clear" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded border border-[#122C4F]/30 hover:bg-[#122C4F]/5">
                        Clear
                    </button>
                    <a href="{{ route('orders.history') }}"
                        class="px-4 py-2 rounded border border-[#122C4F]/30 text-center hover:bg-[#122C4F]/5">
                        View orders
                    </a>

                    <a href="{{ route('checkout.show') }}"
                        class="col-span-2 px-4 py-3 rounded bg-[#122C4F] text-[#FBF9E4] text-center font-semibold hover:bg-[#0f203d]">
                        Checkout
                    </a>
                </div>

                <div class="mt-3 text-xs opacity-70" wire:loading>
                    Updating…
                </div>
            </aside>
        </div>
        @endif
    </div>
</div>