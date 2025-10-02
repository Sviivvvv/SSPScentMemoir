<x-layouts.site>
    <main class="bg-[#122C4F] text-[#FBF9E4] px-6 py-10 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-10">
                <h1 class="text-4xl font-extrabold">Subscriptions</h1>
                <p class="opacity-90 mt-2">Choose one plan. You can switch anytime—your new plan replaces the old one
                    when you place the order.</p>
            </div>

            @if($plans->isEmpty())
                <div class="bg-[#0f203d] rounded-2xl p-6 text-center">
                    <p>No subscription plans found.</p>
                    <a href="{{ route('products.index') }}"
                        class="inline-flex mt-4 px-4 py-2 rounded bg-[#FBF9E4] text-[#122C4F] font-semibold hover:opacity-90">
                        Browse products
                    </a>
                </div>
            @else
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach($plans as $p)
                        @php
                            // Soft mapping for tier tags from the name
                            $name = trim($p->name);
                            $tier = Str::of($name)->lower();
                            $tierLabel = $tier->contains('gold') ? 'Gold' : ($tier->contains('silver') ? 'Silver' : ($tier->contains('bronze') ? 'Bronze' : 'Plan'));
                            // Fallback if description empty
                            $benefits = collect(explode('-', (string) $p->description))
                                ->filter(fn($line) => trim($line) !== '')
                                ->map(fn($line) => trim($line));
                            if ($benefits->isEmpty()) {
                                $benefits = match ($tierLabel) {
                                    'Bronze' => collect(['Monthly surprise sample', 'For explorers on a budget', 'Free standard support']),
                                    'Silver' => collect(['2 curated picks / month', 'Great balance of value & variety', 'Priority support']),
                                    'Gold' => collect(['Premium picks / month', 'Best for collectors', 'Priority support + VIP perks']),
                                    default => collect(['Great monthly value', 'Flexible switch policy', 'Cancel anytime']),
                                };
                            }
                        @endphp

                        <div class="rounded-2xl overflow-hidden bg-[#FBF9E4] text-[#122C4F] shadow-lg flex flex-col">
                            <div class="p-5 border-b border-[#122C4F]/10 flex items-center justify-between">
                                <div class="font-bold text-xl">{{ $name }}</div>
                                <span class="px-2 py-1 text-xs rounded-full bg-[#122C4F] text-[#FBF9E4]">{{ $tierLabel }}</span>
                            </div>

                            <div class="p-5">
                                <div class="w-full rounded-md overflow-hidden mb-4">
                                    @if($p->image_url)
                                        <img src="{{ $p->image_url }}" alt="{{ $name }}" class="w-full h-auto object-contain">
                                    @endif
                                </div>

                                <div class="text-2xl font-extrabold mb-3">LKR {{ number_format($p->price, 2) }} <span
                                        class="text-base font-semibold opacity-80">/ month</span></div>

                                <ul class="space-y-2 text-sm">
                                    @foreach($benefits as $line)
                                        <li class="flex items-start gap-2">
                                            <span class="mt-1 inline-block w-1.5 h-1.5 rounded-full bg-[#122C4F]"></span>
                                            <span>{{ $line }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="p-5 mt-auto">
                                {{-- Re-using add to cart --}}
                                <livewire:cart.add-to-cart :product-id="$p->id" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
</x-layouts.site>