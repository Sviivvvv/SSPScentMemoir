<div class="space-y-3 relative" wire:click.outside="closeDropdown">
    <div class="flex gap-3">
        <button type="button"
            class="px-3 py-1 rounded font-semibold {{ $mode === 'product' ? 'bg-[#122C4F] text-[#FBF9E4]' : 'bg-[#FBF9E4] text-[#122C4F]' }}"
            wire:click="switchMode('product')">Link to a Product</button>

        <button type="button"
            class="px-3 py-1 rounded font-semibold {{ $mode === 'custom' ? 'bg-[#122C4F] text-[#FBF9E4]' : 'bg-[#FBF9E4] text-[#122C4F]' }}"
            wire:click="switchMode('custom')">Custom URL</button>
    </div>

    @if($mode === 'product')
        <label class="block">
            <span class="font-semibold">Search product</span>
            <div class="relative">
                <input type="text" wire:model.live="q" wire:focus="openDropdown" wire:keydown.arrow-down="moveHighlight(1)"
                    wire:keydown.arrow-up="moveHighlight(-1)" wire:keydown.enter.prevent="confirmHighlight"
                    wire:keydown.escape="closeDropdown" placeholder="Type product name…"
                    aria-expanded="{{ $showDropdown ? 'true' : 'false' }}" aria-haspopup="listbox"
                    class="w-full mt-1 p-2 rounded bg-[#EBEBE0] text-[#122C4F]">
                {{-- (inline Close button removed) --}}
            </div>
        </label>

        {{-- Dropdown --}}
        @if($showDropdown)
            <div class="absolute z-20 mt-1 w-full max-w-none bg-white text-black rounded shadow max-h-64 overflow-auto">
                {{-- Sticky header with a single Close button --}}
                <div class="sticky top-0 flex items-center justify-between px-3 py-2 text-xs bg-gray-50 border-b">
                    <span class="text-gray-600">Suggestions</span>
                    <button type="button" class="px-2 py-1 rounded bg-gray-200 hover:bg-gray-300" wire:click="closeDropdown">
                        Close
                    </button>
                </div>

                <ul role="listbox">
                    @forelse($results as $i => $row)
                        <li role="option" aria-selected="{{ $highlight === $i ? 'true' : 'false' }}">
                            <button type="button"
                                class="w-full text-left px-3 py-2 hover:bg-gray-100 {{ $highlight === $i ? 'bg-gray-100' : '' }}"
                                wire:mousedown.prevent="selectProduct({{ $row['id'] }})">
                                {{ $row['name'] }}
                            </button>
                        </li>
                    @empty
                        <li class="px-3 py-2 text-sm text-gray-500">No results.</li>
                    @endforelse
                </ul>
            </div>
        @endif

        @if($selectedId)
            <div class="bg-[#FBF9E4] text-[#122C4F] p-3 rounded">
                <div class="text-sm">Selected product:</div>
                <div class="font-semibold">{{ $q }}</div>
                <div class="text-xs break-all mt-1">URL: {{ $linkUrl }}</div>
                <button type="button" class="mt-2 underline" wire:click="clearSelection">Clear</button>
            </div>
        @endif
    @else
        <label class="block">
            <span class="font-semibold">Custom URL</span>
            <input type="url" wire:model.live="linkUrl" placeholder="https://example.com/..."
                class="w-full mt-1 p-2 rounded bg-[#EBEBE0] text-[#122C4F]">
        </label>
    @endif

    {{-- Hidden input submitted with the parent form --}}
    <input type="hidden" name="link_url" value="{{ $linkUrl }}">
</div>