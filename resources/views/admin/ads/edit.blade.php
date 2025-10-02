<x-layouts.site>
    <main class="bg-[#122C4F] text-[#FBF9E4] px-6 py-10 min-h-screen">
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold">Edit Product</h1>
                <a wire:navigate href="{{ route('admin.products.index') }}" class="underline">← Back to list</a>
            </div>

            @if (session('status'))
                <div class="mb-4 px-4 py-2 rounded bg-green-600/20 text-green-200">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded bg-red-600/20 text-red-100">
                    <div class="font-semibold mb-1">Please fix the following:</div>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif>

            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
                class="bg-[#0f203d] rounded-2xl p-6 shadow">
                @csrf @method('PATCH')

                <div class="grid grid-cols-1 gap-5">
                    {{-- Name --}}
                    <div>
                        <label class="block mb-1 font-semibold">Name</label>
                        <input name="name" value="{{ old('name', $product->name) }}" required
                            class="w-full px-3 py-2 rounded bg-[#FBF9E4] text-[#122C4F]">
                    </div>

                    {{-- Price --}}
                    <div>
                        <label class="block mb-1 font-semibold">Price (LKR)</label>
                        <input name="price" type="number" step="0.01" min="0"
                            value="{{ old('price', $product->price) }}" required
                            class="w-full px-3 py-2 rounded bg-[#FBF9E4] text-[#122C4F]">
                    </div>

                    {{-- Category / Subscription --}}
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 font-semibold">Category</label>
                            <select name="category" class="w-full px-3 py-2 rounded bg-[#FBF9E4] text-[#122C4F]">
                                <option value="">— none —</option>
                                <option value="men" @selected(old('category', $product->category) === 'men')>Men</option>
                                <option value="women" @selected(old('category', $product->category) === 'women')>Women
                                </option>
                                <option value="limited" @selected(old('category', $product->category) === 'limited')>
                                    Limited</option>
                            </select>
                            <p class="mt-1 text-xs opacity-70">Leave empty for subscription products.</p>
                        </div>

                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="is_subscription" value="1" @checked(old('is_subscription', $product->is_subscription))>
                                <span>Is Subscription</span>
                            </label>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block mb-1 font-semibold">Description</label>
                        <textarea name="description" rows="5"
                            class="w-full px-3 py-2 rounded bg-[#FBF9E4] text-[#122C4F]">{{ old('description', $product->description) }}</textarea>
                    </div>

                    {{-- Image (preview + replace) --}}
                    <div>
                        <label class="block mb-1 font-semibold">Image</label>
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-16 h-16 bg-[#FBF9E4] rounded flex items-center justify-center">
                                <img src="{{ $product->image_url }}" class="max-w-14 max-h-14 object-contain" alt="">
                            </div>
                            <div class="text-sm opacity-80">Current</div>
                        </div>
                        <input type="file" name="image" accept="image/*"
                            class="w-full text-sm file:mr-3 file:px-3 file:py-1 file:rounded file:border-0 file:bg-[#FBF9E4] file:text-[#122C4F]">
                        <p class="mt-1 text-xs opacity-70">Upload to replace. If left empty, the current image stays.
                        </p>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <a wire:navigate href="{{ route('admin.products.index') }}"
                            class="px-4 py-2 rounded border border-[#FBF9E4]/30 hover:bg-[#FBF9E4]/10">Cancel</a>
                        <button class="px-4 py-2 rounded bg-[#FBF9E4] text-[#122C4F] font-semibold hover:opacity-90">
                            Save changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</x-layouts.site>