<x-layouts.site>
    <main class="bg-[#122C4F] text-[#FBF9E4] px-6 py-10 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold">Orders</h1>
            </div>

            <section class="bg-[#0f203d] rounded-2xl p-5 shadow">
                <livewire:admin.orders.table pageName="orders_page" />
            </section>
        </div>
    </main>
</x-layouts.site>
