<div class="space-y-4">
    {{-- Top filters: period chips + search --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-1">
            @php $periods = ['all' => 'All time', '7d' => 'Last 7d', '30d' => 'Last 30d']; @endphp
            @foreach ($periods as $key => $label)
                <button type="button" wire:click="setPeriod('{{ $key }}')" @class([
                    'px-2.5 py-1 rounded-full text-xs border transition',
                    'bg-[#FBF9E4] text-[#122C4F] border-transparent font-semibold' => $period === $key,
                    'border-[#FBF9E4]/30 text-[#FBF9E4]/80 hover:bg-white/5' => $period !== $key,
                ])>{{ $label }}</button>
            @endforeach
        </div>

        <div class="relative">
            <input wire:model.live.debounce.200ms="q" placeholder="Search #id, name, email…"
                class="pl-10 pr-8 py-2 rounded-lg bg-white/10 border border-white/20 text-[#FBF9E4] placeholder-white/60" />
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 opacity-70" viewBox="0 0 24 24"
                fill="currentColor">
                <path
                    d="M15.5 14h-.79l-.28-.27a6.471 6.471 0 001.48-4.23C15.91 6.01 13.4 3.5 10.45 3.5S5 6.01 5 9.5 7.51 15.5 10.45 15.5c1.61 0 3.09-.59 4.23-1.57l.27.28v.79L19 20.49 20.49 19 15.5 14zM10.45 14c-2.48 0-4.5-2.02-4.5-4.5S7.97 5 10.45 5s4.5 2.02 4.5 4.5S12.93 14 10.45 14z" />
            </svg>
            @if($q !== '')
                <button type="button" wire:click="clearSearch"
                    class="absolute right-2 top-1/2 -translate-y-1/2 opacity-70 hover:opacity-100">
                    ✕
                </button>
            @endif
        </div>
    </div>

    <div class="flex items-center justify-between">
        <h3 class="text-lg opacity-80">
            Period: {{ $period === 'all' ? 'All time' : strtoupper($period) }}
        </h3>
        <span class="opacity-70 text-sm">Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}</span>
    </div>

    <div class="overflow-x-auto rounded-xl border border-white/10 bg-[#0f203d]">
        <table class="min-w-full text-sm">
            <thead class="bg-white/5">
                <tr class="text-left">
                    <th class="px-3 py-2">Order #</th>
                    <th class="px-3 py-2">Customer</th>
                    <th class="px-3 py-2">Items</th>
                    <th class="px-3 py-2">Total</th>
                    <th class="px-3 py-2">Placed</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse ($orders as $o)
                    <tr wire:key="order-{{ $o->id }}">
                        <td class="px-3 py-2 font-semibold">#{{ $o->id }}</td>
                        <td class="px-3 py-2">
                            <div class="flex flex-col">
                                <span class="font-medium truncate max-w-[240px]"
                                    title="{{ $o->user?->name }}">{{ $o->user?->name ?? '—' }}</span>
                                <span class="opacity-70 text-xs truncate max-w-[260px]"
                                    title="{{ $o->user?->email }}">{{ $o->user?->email ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-2">{{ $o->items->sum('quantity') }}</td>
                        <td class="px-3 py-2">LKR {{ number_format($o->total_amount, 2) }}</td>
                        <td class="px-3 py-2 opacity-75">{{ optional($o->ordered_at)->diffForHumans() }}</td>
                        <td class="px-3 py-2">
                            <button type="button" wire:click="open({{ $o->id }})"
                                class="px-3 py-1 rounded border border-[#FBF9E4]/30 hover:bg-white/5">
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center opacity-70">
                            @if($q !== '')
                                No orders match “{{ $q }}”.
                            @else
                                No orders found.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Custom pagination with wire:navigate --}}
    <div>{{ $orders->links('admin.helpers.pagination') }}</div>

    {{-- Order Details Modal --}}
    @if ($show && $order)
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/60" wire:click="close"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-4xl bg-[#0f203d] text-[#FBF9E4] rounded-2xl shadow-2xl border border-white/10">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                        <div>
                            <div class="text-lg font-semibold">Order #{{ $order->id }}</div>
                            <div class="text-sm opacity-70">
                                Placed {{ optional($order->ordered_at)->format('Y-m-d H:i') }}
                            </div>
                        </div>
                        <button class="px-3 py-1 rounded border border-white/20 hover:bg-white/5"
                            wire:click="close">Close</button>
                    </div>

                    <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-5">
                        {{-- Items --}}
                        <div class="lg:col-span-2">
                            <h4 class="font-semibold mb-3">Items</h4>
                            <div class="rounded-xl border border-white/10 overflow-hidden">
                                <table class="w-full text-sm">
                                    <thead class="bg-white/5">
                                        <tr class="text-left">
                                            <th class="px-3 py-2">Product</th>
                                            <th class="px-3 py-2">Qty</th>
                                            <th class="px-3 py-2">Price</th>
                                            <th class="px-3 py-2">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/10">
                                        @foreach ($order->items as $it)
                                            <tr>
                                                <td class="px-3 py-2">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="w-10 h-10 rounded bg-[#FBF9E4] flex items-center justify-center overflow-hidden">
                                                            @if($it->product?->image_path)
                                                                <img src="{{ $it->product?->image_url ?? $it->product?->image_path }}"
                                                                    class="object-cover w-full h-full" alt="">
                                                            @else
                                                                <span class="text-xs text-[#122C4F]">No img</span>
                                                            @endif
                                                        </div>
                                                        <div class="truncate max-w-[260px]" title="{{ $it->product?->name }}">
                                                            {{ $it->product?->name ?? 'Product #' . $it->product_id }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2">{{ $it->quantity }}</td>
                                                <td class="px-3 py-2">LKR {{ number_format($it->price, 2) }}</td>
                                                <td class="px-3 py-2">LKR {{ number_format($it->price * $it->quantity, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @php
                                $qty = $order->items->sum('quantity');
                                $subtotal = $order->items->sum(fn($i) => $i->price * $i->quantity);
                                $total = $order->total_amount ?? $subtotal;
                            @endphp
                            <div class="mt-4 flex justify-end">
                                <div class="text-sm">
                                    <div class="flex justify-between gap-8">
                                        <span class="opacity-70">Items</span>
                                        <span>{{ $qty }}</span>
                                    </div>
                                    <div class="flex justify-between gap-8">
                                        <span class="opacity-70">Subtotal</span>
                                        <span>LKR {{ number_format($subtotal, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between gap-8 font-semibold">
                                        <span>Total</span>
                                        <span>LKR {{ number_format($total, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Customer / Billing --}}
                        <div class="space-y-4">
                            <div class="rounded-xl border border-white/10 p-4">
                                <h4 class="font-semibold mb-2">Customer</h4>
                                <div class="text-sm">
                                    <div>{{ $order->user?->name ?? '—' }}</div>
                                    <div class="opacity-80">{{ $order->user?->email ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-white/10 p-4">
                                <h4 class="font-semibold mb-2">Billing</h4>
                                <div class="text-sm space-y-1">
                                    <div>{{ $order->billing_first_name }} {{ $order->billing_last_name }}</div>
                                    <div class="opacity-80">{{ $order->billing_email }}</div>
                                    <div class="opacity-80">{{ $order->billing_phone }}</div>
                                    <div class="opacity-80">
                                        {{ $order->billing_address }}<br>
                                        {{ $order->billing_city }} {{ $order->billing_zip }}
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-white/10 p-4">
                                <h4 class="font-semibold mb-2">Payment</h4>
                                <div class="text-sm">
                                    <div class="capitalize">{{ $order->payment_method ?? '—' }}</div>
                                    @if ($order->card_last4)
                                        <div class="opacity-80">•••• •••• •••• {{ $order->card_last4 }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-white/10 flex justify-end">
                        <button class="px-4 py-2 rounded bg-[#FBF9E4] text-[#122C4F] font-semibold hover:opacity-90"
                            wire:click="close">Done</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>