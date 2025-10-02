<x-layouts.site>
    
    <main class="px-6 py-10">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">New Review</h1>

            @if ($errors->any())
                <div class="bg-red-600/20 text-red-100 px-4 py-2 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.reviews.store') }}" method="POST" enctype="multipart/form-data"
                class="bg-[#FBF9E4] text-[#122C4F] p-6 rounded-xl">
                @csrf

                <label class="block mb-3">
                    <span class="font-semibold">Author name</span>
                    <input type="text" name="author_name" value="{{ old('author_name') }}"
                        class="w-full mt-1 p-2 rounded bg-[#EBEBE0]" required>
                </label>

                <label class="block mb-3">
                    <span class="font-semibold">Rating</span>
                    <select name="rating" class="w-full mt-1 p-2 rounded bg-[#EBEBE0]">
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('rating', 5) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </label>

                <label class="block mb-3">
                    <span class="font-semibold">Quote</span>
                    <textarea name="quote" rows="4"
                        class="w-full mt-1 p-2 rounded bg-[#EBEBE0]">{{ old('quote') }}</textarea>
                </label>

                <label class="block mb-3">
                    <span class="font-semibold">Sort order</span>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                        class="w-full mt-1 p-2 rounded bg-[#EBEBE0]">
                </label>

                <label class="block mb-3">
                    <span class="font-semibold">Active</span>
                    <input type="checkbox" name="is_active" value="1" class="ml-2" checked>
                </label>

                <label class="block mb-6">
                    <span class="font-semibold">Image (optional)</span>
                    <input type="file" name="image" accept="image/*" class="w-full mt-1 p-2 rounded bg-[#EBEBE0]">
                </label>

                <div class="flex gap-3">
                    <button class="bg-[#122C4F] text-[#FBF9E4] px-6 py-2 rounded">Create</button>
                    <a href="{{ route('admin.reviews.index') }}" class="underline">Cancel</a>
                </div>
            </form>
        </div>
    </main>
    
</x-layouts.site>