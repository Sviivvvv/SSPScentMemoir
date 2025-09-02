<div class="bg-[#FBF9E4] text-[#122C4F] p-4 rounded-xl max-w-sm">
    @if (session('status'))
        <div class="mb-3 text-green-700 text-sm">{{ session('status') }}</div>
    @endif

    <form wire:submit.prevent="add" class="flex items-center gap-3">
        <label for="qty" class="text-sm">Qty</label>
        <input id="qty" type="number" wire:model.live="qty" min="1" max="99" step="1"
            class="w-20 p-2 rounded border bg-white text-[#122C4F]">

        <button class="bg-[#122C4F] text-[#FBF9E4] px-5 py-2 rounded hover:shadow" wire:loading.attr="disabled"
            wire:target="add">
            <span wire:loading.remove wire:target="add">Add to cart</span>
            <span wire:loading wire:target="add">Adding…</span>
        </button>
    </form>

    @error('qty')
        <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
    @enderror
</div>