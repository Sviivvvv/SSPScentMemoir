<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;

class Table extends Component
{
    use WithPagination;

    /** tabs: all | recent */
    public string $scope = 'all';

    /** unique page param */
    public string $pageName = 'users_page';

    /** modal state */
    public bool $showDesc = false;
    public ?int $descId = null;

    /** search query */
    public string $q = '';

    /** keep scope + q in the URL so SPA nav preserves them */
    protected $queryString = [
        'scope' => ['except' => 'all'],
        'q' => ['except' => ''],
    ];

    protected $listeners = [
        'refreshUsers' => '$refresh',
    ];

    public function mount(string $scope = 'all', string $pageName = 'users_page'): void
    {
        $this->scope = $scope;
        $this->pageName = $pageName;
    }

    public function updatingScope(): void
    {
        $this->resetPage($this->pageName);
    }

    public function updatingQ(): void
    {
        $this->resetPage($this->pageName);
    }

    public function clearSearch(): void
    {
        $this->q = '';
        $this->resetPage($this->pageName);
    }

    public function changeScope(string $scope): void
    {
        $allowed = ['all', 'recent'];
        $this->scope = in_array($scope, $allowed, true) ? $scope : 'all';
        $this->resetPage($this->pageName);
    }

    protected function baseQuery()
    {
        $q = User::query()
            ->where('role', 'customer')
            ->latest();

        $termRaw = trim($this->q);
        if ($termRaw !== '') {
            // escape wildcards for LIKE
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $termRaw) . '%';
            $q->where(function ($w) use ($term) {
                $w->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        if ($this->scope === 'recent') {
            $q->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        return $q;
    }

    /** Livewire delete (instant) */
    public function deleteUser(int $id): void
    {
        $user = User::where('role', 'customer')->find($id);
        if ($user)
            $user->delete();

        $current = $this->baseQuery()->paginate(10, ['*'], $this->pageName);
        if ($current->isEmpty() && $current->currentPage() > 1) {
            $this->previousPage($this->pageName);
        } else {
            $this->dispatch('users-refreshed');
        }

        if ($this->descId === $id)
            $this->closeDesc();

        $this->dispatch('notify', message: 'Customer deleted.');
    }

    /** modal */
    public function openDesc(int $id): void
    {
        $this->descId = $id;
        $this->showDesc = true;
    }

    public function closeDesc(): void
    {
        $this->showDesc = false;
        $this->descId = null;
    }

    public function render()
    {
        $users = $this->baseQuery()->paginate(10, ['*'], $this->pageName);
        $descUser = $this->descId ? User::where('role', 'customer')->find($this->descId) : null;

        return view('livewire.admin.users.table', compact('users', 'descUser'));
    }
}
