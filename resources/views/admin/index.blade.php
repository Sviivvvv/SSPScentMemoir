<x-layouts.site>
    
    <main class="px-6 py-10">
        @php
            $tz = config('app.timezone', 'UTC');

            // Periods
            $now = now();
            $startThisMonth = $now->copy()->startOfMonth();
            $endThisMonth = $now->copy()->endOfMonth();
            $startPrevMonth = $now->copy()->subMonthNoOverflow()->startOfMonth();
            $endPrevMonth = $now->copy()->subMonthNoOverflow()->endOfMonth();

            // Revenue (this month vs last)
            $revThis = \App\Models\Order::whereBetween('ordered_at', [$startThisMonth, $endThisMonth])->sum('total_amount');
            $revPrev = \App\Models\Order::whereBetween('ordered_at', [$startPrevMonth, $endPrevMonth])->sum('total_amount');
            $revDeltaPct = ($revPrev > 0) ? (($revThis - $revPrev) / $revPrev) * 100 : 0;

            // Orders (this month vs last)
            $ordThis = \App\Models\Order::whereBetween('ordered_at', [$startThisMonth, $endThisMonth])->count();
            $ordPrev = \App\Models\Order::whereBetween('ordered_at', [$startPrevMonth, $endPrevMonth])->count();
            $ordDeltaPct = ($ordPrev > 0) ? (($ordThis - $ordPrev) / $ordPrev) * 100 : 0;

            // Products (total)
            $prodThis = \App\Models\Product::count();
            $prodDeltaPct = 0; // placeholder

            // Customers (total + deltas)
            $custThis = \App\Models\User::where('role', 'customer')->whereBetween('created_at', [$startThisMonth, $endThisMonth])->count();
            $custPrev = \App\Models\User::where('role', 'customer')->whereBetween('created_at', [$startPrevMonth, $endPrevMonth])->count();
            $custTotal = \App\Models\User::where('role', 'customer')->count();
            $custDeltaPct = ($custPrev > 0) ? (($custThis - $custPrev) / $custPrev) * 100 : 0;

            // Recent orders (latest 5)
            $recentOrders = \App\Models\Order::with(['user', 'items'])
                ->latest('ordered_at')
                ->limit(5)->get();

            // Top products (by qty in last 30d)
            $topProducts = \App\Models\OrderItem::with('product')
                ->whereHas('order', fn($q) => $q->where('ordered_at', '>=', now()->subDays(30)))
                ->selectRaw('product_id, SUM(quantity) as qty, SUM(price*quantity) as revenue')
                ->groupBy('product_id')
                ->orderByDesc('qty')
                ->limit(5)
                ->get();
        @endphp

        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold">Admin Dashboard</h1>
            <p class="mt-2 opacity-80">For managing the <strong>customer home page</strong> (ads & reviews).</p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a wire:navigate href="{{ route('admin.ads.index') }}" class="underline">Manage Ads</a>
                <a wire:navigate href="{{ route('admin.reviews.index') }}" class="underline">Manage Reviews</a>
            </div>
        </div>

        {{--  dashboard panel --}}
        <div
            class="max-w-7xl mx-auto mt-8 bg-[#faf7f1] text-slate-900 rounded-2xl p-5 md:p-6 shadow-[0_10px_30px_-10px_rgba(0,0,0,0.3)]">
            <div class="flex gap-2 mb-5">
                <div class="px-3 py-1.5 rounded-full bg-slate-200/70 text-slate-900 font-semibold text-sm">Overview
                </div>
                
            </div>

            {{-- stat cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
                <div class="bg-white border border-slate-200 rounded-xl p-4">
                    <div class="text-sm text-slate-500">Total Revenue</div>
                    <div class="mt-1 text-2xl font-bold">LKR {{ number_format($revThis, 0) }}</div>
                    <div class="mt-1 text-xs {{ $revDeltaPct >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $revDeltaPct >= 0 ? '+' : '' }}{{ number_format($revDeltaPct, 1) }}% from last month
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4">
                    <div class="text-sm text-slate-500">Total Orders (this month)</div>
                    <div class="mt-1 text-2xl font-bold">{{ number_format($ordThis) }}</div>
                    <div class="mt-1 text-xs {{ $ordDeltaPct >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $ordDeltaPct >= 0 ? '+' : '' }}{{ number_format($ordDeltaPct, 1) }}% from last month
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4">
                    <div class="text-sm text-slate-500">Total Products</div>
                    <div class="mt-1 text-2xl font-bold">{{ number_format($prodThis) }}</div>
                    <div class="mt-1 text-xs text-emerald-600">
                        {{ $prodDeltaPct >= 0 ? '+' : '' }}{{ number_format($prodDeltaPct, 1) }}% from last month
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4">
                    <div class="text-sm text-slate-500">Total Customers</div>
                    <div class="mt-1 text-2xl font-bold">{{ number_format($custTotal) }}</div>
                    <div class="mt-1 text-xs {{ $custDeltaPct >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $custDeltaPct >= 0 ? '+' : '' }}{{ number_format($custDeltaPct, 1) }}% from last month
                    </div>
                </div>
            </div>

            {{-- recent orders + top products --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Recent Orders --}}
                <div class="bg-white border border-slate-200 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold">Recent Orders</h3>
                        <a wire:navigate href="{{ route('admin.orders.index') }}"
                            class="text-sm text-slate-600 hover:underline">View all</a>
                    </div>
                    <div class="text-xs text-slate-500 mb-2">Latest customer orders</div>

                    <ul class="divide-y divide-slate-200">
                        @forelse($recentOrders as $o)
                            <li class="py-3 flex items-center justify-between">
                                <div class="min-w-0">
                                    <div class="font-medium truncate max-w-[240px]">
                                        {{ $o->user?->name ?? 'Guest' }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ $o->items->sum('quantity') }}
                                        item{{ $o->items->sum('quantity') == 1 ? '' : 's' }}
                                        • {{ optional($o->ordered_at)->timezone($tz)->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold">LKR {{ number_format($o->total_amount, 2) }}</div>
                                </div>
                            </li>
                        @empty
                            <li class="py-6 text-slate-500 text-sm text-center">No orders yet.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Top Products --}}
                <div class="bg-white border border-slate-200 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold">Top Products</h3>
                        <a wire:navigate href="{{ route('admin.products.index') }}"
                            class="text-sm text-slate-600 hover:underline">Manage</a>
                    </div>
                    <div class="text-xs text-slate-500 mb-2">Best selling items (last 30 days)</div>

                    <ul class="divide-y divide-slate-200">
                        @forelse($topProducts as $tp)
                            <li class="py-3 flex items-center justify-between">
                                <div class="min-w-0">
                                    <div class="font-medium truncate max-w-[260px]">
                                        {{ $tp->product?->name ?? ('Product #' . $tp->product_id) }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ $tp->qty }} sold
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold">LKR {{ number_format($tp->revenue ?? 0, 2) }}</div>
                                </div>
                            </li>
                        @empty
                            <li class="py-6 text-slate-500 text-sm text-center">No sales data yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </main>
</x-layouts.site>