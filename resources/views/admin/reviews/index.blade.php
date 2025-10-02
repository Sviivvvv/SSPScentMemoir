<x-layouts.site>
    
    <main class="px-6 py-10">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Reviews</h1>
                <a href="{{ route('admin.reviews.create') }}"
                    class="bg-[#FBF9E4] text-[#122C4F] px-4 py-2 rounded font-semibold">+ New Review</a>
            </div>

            @if(session('success'))
                <div class="mt-4 bg-green-600/20 text-green-100 px-4 py-2 rounded">{{ session('success') }}</div>
            @endif

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="text-[#FBF9E4]/80">
                        <tr>
                            <th class="py-2 pr-4">Image</th>
                            <th class="py-2 pr-4">Author</th>
                            <th class="py-2 pr-4">Rating</th>
                            <th class="py-2 pr-4">Active</th>
                            <th class="py-2 pr-4">Order</th>
                            <th class="py-2 pr-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="align-top">
                        @forelse($reviews as $review)
                            <tr class="border-top border-white/10">
                                <td class="py-2 pr-4">
                                    @if($review->image_url)
                                        <img src="{{ $review->image_url }}" alt="" class="h-14 w-24 object-cover rounded">
                                    @else —
                                    @endif
                                </td>
                                <td class="py-2 pr-4">{{ $review->author_name }}</td>
                                <td class="py-2 pr-4">⭐ {{ $review->rating }}/5</td>
                                <td class="py-2 pr-4">
                                    <form method="POST" action="{{ route('admin.reviews.toggle', $review) }}">
                                        @csrf
                                        <button
                                            class="px-3 py-1 rounded {{ $review->is_active ? 'bg-green-600/70' : 'bg-red-600/70' }}">
                                            {{ $review->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-2 pr-4">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.reviews.up', $review) }}">@csrf
                                            <button class="px-2 py-1 bg-[#FBF9E4] text-[#122C4F] rounded">↑</button>
                                        </form>
                                        <span>{{ $review->sort_order }}</span>
                                        <form method="POST" action="{{ route('admin.reviews.down', $review) }}">@csrf
                                            <button class="px-2 py-1 bg-[#FBF9E4] text-[#122C4F] rounded">↓</button>
                                        </form>
                                    </div>
                                </td>
                                <td class="py-2 pr-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.reviews.edit', $review) }}" class="underline">Edit</a>
                                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                            onsubmit="return confirm('Delete this review?')">
                                            @csrf @method('DELETE')
                                            <button class="underline text-red-300">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4" colspan="6">No reviews.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
</x-layouts.site>