<x-layouts.site>
    <main class="bg-[#122C4F] text-[#FBF9E4] px-6 py-10 min-h-screen">
        <div class="max-w-2xl mx-auto bg-[#0f203d] rounded-2xl p-6">
            <h1 class="text-2xl font-bold mb-4">Edit Customer #{{ $user->id }}</h1>

            <form wire:navigate method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                @csrf @method('PATCH')

                <div>
                    <label class="block mb-1">Name</label>
                    <input name="name" class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]"
                        value="{{ old('name', $user->name) }}">
                    @error('name') <div class="text-red-300 text-sm">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block mb-1">Email</label>
                    <input name="email" type="email" class="w-full p-2 rounded bg-[#FBF9E4] text-[#122C4F]"
                        value="{{ old('email', $user->email) }}">
                    @error('email') <div class="text-red-300 text-sm">{{ $message }}</div> @enderror
                </div>

                <div class="flex gap-2">
                    <button class="px-4 py-2 rounded bg-[#FBF9E4] text-[#122C4F] font-semibold">Save</button>
                    <a wire:navigate href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 rounded border border-[#FBF9E4]/40">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</x-layouts.site>