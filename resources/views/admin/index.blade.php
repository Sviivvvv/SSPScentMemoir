<x-layouts.site>
    <x-site.navbar />

    <main class="px-6 py-10">
        <h1 class="text-3xl font-bold">Admin Dashboard</h1>
        <p class="mt-2 opacity-80">Only admins can see this page.</p>

        <div class="mt-6 space-x-4">
            <a href="{{ route('admin.ads.index') }}" class="underline">Manage Ads</a>
            <a href="{{ route('admin.reviews.index') }}" class="underline">Manage Reviews</a>
        </div>
    </main>

    <x-site.footer />
</x-layouts.site>