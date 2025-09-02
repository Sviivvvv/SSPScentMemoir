<x-layouts.site>
    <x-site.navbar />
    <main class="px-6 py-10">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Ads</h1>
                <a href="{{ route('admin.ads.create') }}"
                    class="bg-[#FBF9E4] text-[#122C4F] px-4 py-2 rounded font-semibold">+ New Ad</a>
            </div>

            @if(session('success'))
                <div class="mt-4 bg-green-600/20 text-green-100 px-4 py-2 rounded">{{ session('success') }}</div>
            @endif

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="text-[#FBF9E4]/80">
                        <tr>
                            <th class="py-2 pr-4">Image</th>
                            <th class="py-2 pr-4">Title</th>
                            <th class="py-2 pr-4">Link</th>
                            <th class="py-2 pr-4">Active</th>
                            <th class="py-2 pr-4">Order</th>
                            <th class="py-2 pr-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="align-top">
                        @forelse($ads as $ad)
                            <tr class="border-t border-white/10">
                                <td class="py-2 pr-4">
                                    <img src="{{ $ad->image_url }}" alt="" class="h-14 w-24 object-cover rounded">
                                </td>
                                <td class="py-2 pr-4">{{ $ad->title ?? '—' }}</td>
                                <td class="py-2 pr-4">
                                    @if($ad->link_url)
                                        <a href="{{ $ad->link_url }}" target="_blank" class="underline">Visit</a>
                                    @else — @endif
                                </td>
                                <td class="py-2 pr-4">
                                    <form method="POST" action="{{ route('admin.ads.toggle', $ad) }}">
                                        @csrf
                                        <button
                                            class="px-3 py-1 rounded {{ $ad->is_active ? 'bg-green-600/70' : 'bg-red-600/70' }}">
                                            {{ $ad->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-2 pr-4">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.ads.up', $ad) }}">@csrf
                                            <button class="px-2 py-1 bg-[#FBF9E4] text-[#122C4F] rounded">↑</button>
                                        </form>
                                        <span>{{ $ad->sort_order }}</span>
                                        <form method="POST" action="{{ route('admin.ads.down', $ad) }}">@csrf
                                            <button class="px-2 py-1 bg-[#FBF9E4] text-[#122C4F] rounded">↓</button>
                                        </form>
                                    </div>
                                </td>
                                <td class="py-2 pr-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.ads.edit', $ad) }}" class="underline">Edit</a>
                                        <form method="POST" action="{{ route('admin.ads.destroy', $ad) }}"
                                            onsubmit="return confirm('Delete this ad?')">
                                            @csrf @method('DELETE')
                                            <button class="underline text-red-300">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4" colspan="6">No ads.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <x-site.footer />
</x-layouts.site>