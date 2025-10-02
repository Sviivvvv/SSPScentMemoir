<div class="space-y-4">
    {{-- Top bar: mini category nav + Add Product --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-wrap items-center gap-2">
            @php
                $tabs = [
                    'all' => 'All',
                    'limited' => 'Limited',
                    'men' => 'Men',
                    'women' => 'Women',
                    'subs' => 'Subscriptions',
                ];
            @endphp

            @foreach ($tabs as $key => $label)
                <button type="button" wire:click="changeScope('{{ $key }}')" @class([
                    'px-3 py-1.5 rounded-full text-sm border transition',
                    'bg-[#FBF9E4] text-[#122C4F] border-transparent font-semibold' => $scope === $key,
                    'border-[#FBF9E4]/30 text-[#FBF9E4]/80 hover:bg-white/5' => $scope !== $key,
                ])>
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <a wire:navigate href="{{ route('admin.products.create', ['scope' => $scope === 'all' ? 'men' : $scope]) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#FBF9E4] text-[#122C4F] font-semibold hover:opacity-90 shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z" />
            </svg>
            Add {{ $scope === 'subs' ? 'Subscription' : 'Product' }}
        </a>
    </div>

    <div class="flex items-center justify-between">
        <h3 class="text-lg opacity-80">Showing: <span class="capitalize">{{ $scope }}</span></h3>
        <span class="opacity-70 text-sm">Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>
    </div>

    <div class="overflow-x-auto rounded-xl border border-white/10 bg-[#0f203d]">
        <table class="min-w-full text-sm">
            <thead class="bg-white/5">
                <tr class="text-left">
                    <th class="px-3 py-2">ID</th>
                    <th class="px-3 py-2">Image</th>
                    <th class="px-3 py-2">Name</th>
                    <th class="px-3 py-2">Price</th>
                    <th class="px-3 py-2">{{ $scope === 'subs' ? 'Type' : 'Category' }}</th>
                    <th class="px-3 py-2">Description</th>
                    <th class="px-3 py-2">Updated</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse ($products as $p)
                    <tr wire:key="prod-{{ $p->id }}">
                        <td class="px-3 py-2">{{ $p->id }}</td>
                        <td class="px-3 py-2">
                            <div class="w-12 h-12 bg-[#FBF9E4] rounded flex items-center justify-center overflow-hidden">
                                @if ($p->image_path)
                                    <img src="{{ $p->image_url }}" class="object-cover w-full h-full" alt="">
                                @else
                                    <span class="text-xs text-[#122C4F]">No img</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 py-2 font-semibold truncate max-w-[240px]" title="{{ $p->name }}">
                            {{ $p->name }}
                        </td>
                        <td class="px-3 py-2">LKR {{ number_format($p->price, 2) }}</td>
                        <td class="px-3 py-2">
                            @if ($p->is_subscription)
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs bg-emerald-500/20 text-emerald-200">Subscription</span>
                            @else
                                <span class="capitalize">{{ $p->category ?? '—' }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <button type="button" wire:click="openDesc({{ $p->id }})"
                                class="px-3 py-1 rounded border border-[#FBF9E4]/30 hover:bg-white/5">
                                View
                            </button>
                        </td>
                        <td class="px-3 py-2 opacity-75">{{ $p->updated_at->diffForHumans() }}</td>
                        <td class="px-3 py-2">
                            <div class="flex items-center gap-2">
                                <a wire:navigate href="{{ route('admin.products.edit', $p) }}"
                                    class="px-3 py-1 rounded bg-[#FBF9E4] text-[#122C4F] hover:opacity-90 text-xs font-semibold">
                                    Edit
                                </a>
                                <button type="button" onclick="if(!confirm('Delete this product?')) return false;"
                                    wire:click="deleteProduct({{ $p->id }})"
                                    class="px-3 py-1 rounded border border-red-400/50 text-red-300 hover:bg-red-500/10 text-xs">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center opacity-70">Nothing here.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Use the custom pagination that includes wire:navigate --}}
    <div>{{ $products->links('admin.helpers.pagination') }}</div>

    {{-- Description Modal --}}
    @if ($showDesc)
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/60" wire:click="closeDesc"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-xl bg-[#0f203d] text-[#FBF9E4] rounded-2xl shadow-2xl border border-white/10">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
                        <h4 class="text-lg font-semibold">
                            {{ $descProduct?->name ?? 'Product' }} — Description
                        </h4>
                        <button class="px-3 py-1 rounded border border-white/20 hover:bg-white/5"
                            wire:click="closeDesc">Close</button>
                    </div>
                    <div class="p-5 space-y-3 max-h-[60vh] overflow-y-auto">
                        @php
                            $desc = trim((string) ($descProduct?->description ?? 'No description provided.'));
                        @endphp
                        <p class="leading-relaxed whitespace-pre-line">
                            {{ $desc !== '' ? $desc : 'No description provided.' }}
                        </p>
                        @if ($descProduct?->image_path)
                            <div class="mt-3">
                                <img src="{{ $descProduct->image_path }}" alt=""
                                    class="rounded-xl max-h-64 object-cover w-full">
                            </div>
                        @endif
                        <div class="text-sm opacity-70">
                            Updated {{ $descProduct?->updated_at?->diffForHumans() }}
                        </div>
                    </div>
                    <div class="px-5 py-4 border-t border-white/10 flex justify-end">
                        <button class="px-4 py-2 rounded bg-[#FBF9E4] text-[#122C4F] font-semibold hover:opacity-90"
                            wire:click="closeDesc">
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>