<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    /** one of: all|men|women|limited|subs */
    public string $scope = 'all';

    /** unique page param per table instance (so multiple tables don’t clash) */
    public string $pageName = 'page';

    /** UI state for description modal */
    public bool $showDesc = false;
    public ?int $descId = null;

    protected $listeners = [
        'refreshProducts' => '$refresh',
    ];

    public function mount(string $scope = 'all', string $pageName = 'page'): void
    {
        $this->scope = $scope;
        $this->pageName = $pageName;
    }

    /** when scope changes, go back to page 1  */
    public function updatingScope(): void
    {
        $this->resetPage($this->pageName);
    }

    public function changeScope(string $scope): void
    {
        $allowed = ['all', 'men', 'women', 'limited', 'subs'];
        $this->scope = in_array($scope, $allowed, true) ? $scope : 'all';
        $this->resetPage($this->pageName);
    }

    protected function baseQuery()
    {
        $q = Product::query()->latest();

        if ($this->scope === 'subs') {
            $q->where('is_subscription', true);
        } elseif ($this->scope === 'all') {
            $q->where('is_subscription', false);
        } else {
            $lc = mb_strtolower($this->scope);
            $q->where('is_subscription', false)
                ->where(function ($w) use ($lc) {
                    $w->whereRaw('LOWER(category) = ?', [$lc])
                        ->orWhereHas('categoryRef', fn($c) => $c->whereRaw('LOWER(name) = ?', [$lc]));
                });
        }

        return $q;
    }

    /** DELETE */
    public function deleteProduct(int $id): void
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
        }

        // If the current page becomes empty after deletion, step back a page
        $current = $this->baseQuery()->paginate(8, ['*'], $this->pageName);
        if ($current->isEmpty() && $current->currentPage() > 1) {
            $this->previousPage($this->pageName);
        } else {
            $this->dispatch('products-refreshed');
        }

        // Close modal if it was open for this product
        if ($this->descId === $id) {
            $this->closeDesc();
        }

        $this->dispatch('notify', message: 'Product deleted.');
    }

    /** Modal controls */
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
        $products = $this->baseQuery()->paginate(8, ['*'], $this->pageName);
        $descProduct = $this->descId ? Product::find($this->descId) : null;

        return view('livewire.admin.products.table', compact('products', 'descProduct'));
    }
}
