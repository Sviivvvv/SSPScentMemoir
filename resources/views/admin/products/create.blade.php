<x-layouts.site>
    <main class="bg-[#122C4F] text-[#FBF9E4] px-6 py-10 min-h-screen">
        <div class="max-w-3xl mx-auto bg-[#0f203d] rounded-2xl p-6">
            <h1 class="text-2xl font-bold mb-4">
                Add {{ $scope === 'subs' ? 'Subscription' : 'Product' }}
            </h1>

            <form wire:navigate method="POST" action="{{ route('admin.products.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="scope" value="{{ $scope }}">

                <div>
                    <label class="block mb-1">Name</label>
                    <input name="name" class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]" value="{{ old('name') }}">
                    @error('name') <div class="text-red-300 text-sm">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block mb-1">Price</label>
                    <input name="price" type="number" step="0.01" min="0"
                        class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]" value="{{ old('price') }}">
                    @error('price') <div class="text-red-300 text-sm">{{ $message }}</div> @enderror
                </div>

                @if ($scope !== 'subs')
                    <div>
                        <label class="block mb-1">Category</label>
                        <select name="category" class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]">
                            <option value="">— Select —</option>
                            <option value="men" @selected(old('category') === 'men')>men</option>
                            <option value="women" @selected(old('category') === 'women')>women</option>
                            <option value="limited" @selected(old('category') === 'limited')>limited</option>
                        </select>
                        @error('category') <div class="text-red-300 text-sm">{{ $message }}</div> @enderror
                    </div>
                @endif

                <div>
                    <label class="block mb-1">Image URL</label>
                    <input name="image_path" class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]"
                        value="{{ old('image_path') }}">
                    @error('image_path') <div class="text-red-300 text-sm">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block mb-1">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]">{{ old('description') }}</textarea>
                    @error('description') <div class="text-red-300 text-sm">{{ $message }}</div> @enderror
                </div>

                <div class="flex gap-2">
                    <button class="px-4 py-2 rounded bg-[#FBF9E4] text-[#122C4F] font-semibold">Save</button>
                    <a wire:navigate href="{{ route('admin.products.index') }}"
                        class="px-4 py-2 rounded border border-[#FBF9E4]/40">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</x-layouts.site>