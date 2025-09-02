<x-layouts.site>
    <main class="bg-[#122C4F] text-[#FBF9E4] px-6 py-10 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-4xl font-extrabold">Your Orders</h1>
                <a href="{{ route('products.index') }}" class="underline" wire:navigate>Continue shopping →</a>
            </div>

            @if($orders->isEmpty())
                <div class="bg-[#0f203d] rounded-2xl p-6">
                    <p class="mb-4">You have no orders yet.</p>
                    <a href="{{ route('products.index') }}"
                        class="inline-flex px-4 py-2 rounded bg-[#FBF9E4] text-[#122C4F] font-semibold hover:opacity-90"
                        wire:navigate>
                        Browse products
                    </a>
                </div>
            @else
                {{-- top summary --}}
                <div class="mb-4 text-sm opacity-80">
                    Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }}
                </div>

                <div class="space-y-6">
                    @foreach($orders as $order)
                        <div class="rounded-2xl overflow-hidden bg-[#FBF9E4] text-[#122C4F] shadow-lg">
                            {{-- header --}}
                            <div
                                class="px-5 py-4 flex flex-col sm:flex-row gap-2 sm:gap-4 sm:items-center justify-between border-b border-[#122C4F]/10">
                                <div class="font-bold text-lg">Order #{{ $order->id }}</div>
                                <div class="flex items-center gap-3">
                                    <span class="px-2 py-1 text-xs rounded-full bg-[#122C4F] text-[#FBF9E4]">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    <span class="opacity-80 text-sm">
                                        {{ $order->ordered_at?->format('Y-m-d H:i') ?? $order->created_at->format('Y-m-d H:i') }}
                                    </span>
                                    <span class="font-semibold">
                                        LKR {{ number_format($order->total_amount, 2) }}
                                    </span>
                                </div>
                            </div>

                            {{-- items --}}
                            <div class="p-5">
                                <ul class="divide-y divide-[#122C4F]/10">
                                    @foreach($order->items as $it)
                                        <li class="py-3 grid grid-cols-[56px_1fr_auto] gap-4 items-center">
                                            <div class="w-14 h-14 bg-white rounded-md flex items-center justify-center">
                                                @if($it->product?->image_url)
                                                    <img src="{{ $it->product->image_url }}" alt=""
                                                        class="max-w-12 max-h-12 object-contain">
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <div class="truncate font-medium"
                                                    title="{{ $it->product->name ?? 'Product #' . $it->product_id }}">
                                                    {{ $it->product->name ?? 'Product #' . $it->product_id }}
                                                </div>
                                                <div class="flex items-center gap-2 text-sm">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full bg-[#122C4F]/10 font-semibold">
                                                        Qty {{ $it->quantity }}
                                                    </span>
                                                    <span>•</span>
                                                    <span>
                                                        LKR {{ number_format($it->price, 2) }}
                                                        <span class="opacity-70">each</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="justify-self-end font-semibold">
                                                LKR {{ number_format($it->price * $it->quantity, 2) }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- pager --}}
                <div class="mt-8">
                    {{ $orders->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </main>
</x-layouts.site>