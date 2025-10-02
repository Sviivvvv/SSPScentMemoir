<x-layouts.site>
    <main class="bg-[#122C4F] text-[#FBF9E4] px-6 py-10 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold">Manage Products</h1>
            </div>

            @if (session('status'))
                <div class="bg-green-600/20 border border-green-600 text-green-100 px-4 py-2 rounded">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Single Livewire table with internal tabs (All / Limited / Men / Women / Subs) --}}
            <section class="bg-[#0f203d] rounded-2xl p-5 shadow">
                <livewire:admin.products.table scope="all" pageName="products_page" />
            </section>
        </div>
    </main>
</x-layouts.site>
