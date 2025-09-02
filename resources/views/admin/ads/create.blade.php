<x-layouts.site>
    <x-site.navbar />
    <main class="px-6 py-10">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">New Ad</h1>

            @if ($errors->any())
                <div class="bg-red-600/20 text-red-100 px-4 py-2 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data"
                class="bg-[#FBF9E4] text-[#122C4F] p-6 rounded-xl">
                @csrf

                <label class="block mb-3">
                    <span class="font-semibold">Title</span>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full mt-1 p-2 rounded bg-[#EBEBE0]">
                </label>

                <label class="block mb-4">
                    <span class="font-semibold">Link</span>
                    <div class="mt-1">
                        <livewire:admin.ads.link-picker :initial-url="old('link_url')" />
                    </div>
                    <p class="mt-2 text-xs opacity-80">Tip: choose “Link to a Product” and search; or switch to “Custom
                        URL”.</p>
                </label>

                <label class="block mb-3">
                    <span class="font-semibold">Sort order</span>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                        class="w-full mt-1 p-2 rounded bg-[#EBEBE0]">
                </label>

                <label class="block mb-3">
                    <span class="font-semibold">Active</span>
                    <input type="checkbox" name="is_active" value="1" class="ml-2" {{ old('is_active', true) ? 'checked' : '' }}>
                </label>

                <label class="block mb-6">
                    <span class="font-semibold">Image (required)</span>
                    <input type="file" name="image" accept="image/*" class="w-full mt-1 p-2 rounded bg-[#EBEBE0]"
                        required>
                </label>

                <div class="flex gap-3">
                    <button class="bg-[#122C4F] text-[#FBF9E4] px-6 py-2 rounded">Create</button>
                    <a href="{{ route('admin.ads.index') }}" class="underline">Cancel</a>
                </div>
            </form>
        </div>
    </main>
    <x-site.footer />
</x-layouts.site>