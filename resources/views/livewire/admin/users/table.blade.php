<div class="space-y-4">
    {{-- Top: tabs + search --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-wrap items-center gap-2">
            @php
                $tabs = [
                    'all'    => 'All',
                    'recent' => 'Recent (30d)',
                ];
            @endphp

            @foreach ($tabs as $key => $label)
                <button
                    type="button"
                    wire:click="changeScope('{{ $key }}')"
                    @class([
                        'px-3 py-1.5 rounded-full text-sm border transition',
                        'bg-[#FBF9E4] text-[#122C4F] border-transparent font-semibold' => $scope === $key,
                        'border-[#FBF9E4]/30 text-[#FBF9E4]/80 hover:bg-white/5' => $scope !== $key,
                    ])
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            <div class="relative">
                {{-- Use .live for truly instant updates in LW v3 --}}
                <input
                    wire:model.live.debounce.200ms="q"
                    placeholder="Search name or email…"
                    class="pl-10 pr-8 py-2 rounded-lg bg-white/10 border border-white/20 text-[#FBF9E4] placeholder-white/60"
                />
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 opacity-70" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27a6.471 6.471 0 001.48-4.23C15.91 6.01 13.4 3.5 10.45 3.5S5 6.01 5 9.5 7.51 15.5 10.45 15.5c1.61 0 3.09-.59 4.23-1.57l.27.28v.79L19 20.49 20.49 19 15.5 14zM10.45 14c-2.48 0-4.5-2.02-4.5-4.5S7.97 5 10.45 5s4.5 2.02 4.5 4.5S12.93 14 10.45 14z"/>
                </svg>
                @if($q !== '')
                <button type="button" wire:click="clearSearch"
                        class="absolute right-2 top-1/2 -translate-y-1/2 opacity-70 hover:opacity-100">
                    ✕
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <h3 class="text-lg opacity-80">Showing: <span class="capitalize">{{ $scope }}</span></h3>
        <span class="opacity-70 text-sm">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
    </div>

    <div class="overflow-x-auto rounded-xl border border-white/10 bg-[#0f203d]">
        <table class="min-w-full text-sm">
            <thead class="bg-white/5">
                <tr class="text-left">
                    <th class="px-3 py-2">ID</th>
                    <th class="px-3 py-2">User</th>
                    <th class="px-3 py-2">Email</th>
                    <th class="px-3 py-2">Joined</th>
                    <th class="px-3 py-2">Details</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse ($users as $u)
                    <tr wire:key="user-{{ $u->id }}">
                        <td class="px-3 py-2">{{ $u->id }}</td>
                        <td class="px-3 py-2">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#FBF9E4] text-[#122C4F] font-bold flex items-center justify-center">
                                    {{ $u->initials() }}
                                </div>
                                <div class="font-semibold truncate max-w-[220px]" title="{{ $u->name }}">{{ $u->name }}</div>
                            </div>
                        </td>
                        <td class="px-3 py-2 truncate max-w-[260px]" title="{{ $u->email }}">{{ $u->email }}</td>
                        <td class="px-3 py-2 opacity-75">{{ $u->created_at->diffForHumans() }}</td>
                        <td class="px-3 py-2">
                            <button type="button"
                                    wire:click="openDesc({{ $u->id }})"
                                    class="px-3 py-1 rounded border border-[#FBF9E4]/30 hover:bg-white/5">
                                View
                            </button>
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex items-center gap-2">
                                {{-- ✅ Edit is back next to Delete --}}
                                <a wire:navigate href="{{ route('admin.users.edit', $u) }}"
                                   class="px-3 py-1 rounded bg-[#FBF9E4] text-[#122C4F] hover:opacity-90 text-xs font-semibold">
                                    Edit
                                </a>
                                <button type="button"
                                        onclick="if(!confirm('Delete this customer?')) return false;"
                                        wire:click="deleteUser({{ $u->id }})"
                                        class="px-3 py-1 rounded border border-red-400/50 text-red-300 hover:bg-red-500/10 text-xs">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center opacity-70">
                            @if($q !== '')
                                No customers match “{{ $q }}”.
                            @else
                                No customers found.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Use your custom pagination that includes wire:navigate --}}
    <div>{{ $users->links('admin.helpers.pagination') }}</div>

    {{-- Details Modal (no Edit button here) --}}
    @if ($showDesc)
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/60" wire:click="closeDesc"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-xl bg-[#0f203d] text-[#FBF9E4] rounded-2xl shadow-2xl border border-white/10">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
                        <h4 class="text-lg font-semibold">
                            {{ $descUser?->name ?? 'Customer' }}
                            <span class="ml-2 text-xs opacity-70">#{{ $descUser?->id }}</span>
                        </h4>
                        <button class="px-3 py-1 rounded border border-white/20 hover:bg-white/5"
                                wire:click="closeDesc">Close</button>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#FBF9E4] text-[#122C4F] font-bold flex items-center justify-center">
                                {{ $descUser?->initials() }}
                            </div>
                            <div>
                                <div class="font-semibold">{{ $descUser?->name }}</div>
                                <div class="text-sm opacity-80">{{ $descUser?->email }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <div class="opacity-60">Joined</div>
                                <div>{{ $descUser?->created_at?->format('Y-m-d') }}</div>
                            </div>
                            <div>
                                <div class="opacity-60">Orders</div>
                                <div>{{ $descUser?->orders()->count() ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="px-5 py-4 border-t border-white/10 flex justify-end">
                        <button class="px-4 py-2 rounded bg-[#FBF9E4] text-[#122C4F] font-semibold hover:opacity-90"
                                wire:click="closeDesc">
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
