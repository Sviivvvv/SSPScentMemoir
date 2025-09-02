<?php

namespace App\Livewire\Shop;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class ProductSearchBar extends Component
{
    public bool $openFilters = false;
    public string $q = '';
    public string $category = '';
    public ?float $minPrice = null;
    public ?float $maxPrice = null;
    public string $sort = 'relevance';
    public bool $showSuggestions = true;
    public bool $showFilters = false;

    public int $filtersVersion = 0;
    public function mount(): void
    {
        $this->showFilters = (bool) $this->openFilters;
    }

    public function getSuggestionsProperty()
    {
        $term = trim($this->q);
        if (mb_strlen($term) < 1)
            return collect();

        return Product::query()
            ->whereNull('deleted_at')
            ->where('name', 'like', '%' . $term . '%')
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'image_path']);
    }

    public function getCategoryOptionsProperty()
    {
        $fromTable = Category::query()->orderBy('name')->pluck('name')->all();
        $legacy = Product::query()
            ->whereNotNull('category')->where('category', '!=', '')
            ->distinct()->orderBy('category')->pluck('category')->all();

        return collect($fromTable)->merge($legacy)->unique()->values()->all();
    }

    public function getCategoryMatchesProperty()
    {
        $term = trim($this->q);
        if (mb_strlen($term) < 1)
            return collect();

        $t = mb_strtolower($term);
        return collect($this->categoryOptions)
            ->filter(fn($n) => str_contains(mb_strtolower($n), $t))
            ->take(5)->values();
    }

    public function updatingQ($value): void
    {
        $lc = mb_strtolower(trim((string) $value));
        $exact = collect($this->categoryOptions)->first(fn($n) => mb_strtolower($n) === $lc);
        if ($exact)
            $this->category = $exact;
    }

    /** ✅ Clear values only; do NOT close filters */
    public function clearFilters(): void
    {
        $this->q = '';
        $this->category = '';
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->sort = 'relevance';

        // Force the filters inputs to remount with new values
        $this->filtersVersion++;
        $this->dispatch('$refresh');
    }

    public function selectSuggestion(int $id)
    {
        return $this->redirectRoute('products.show', $id);
    }

    public function goToCategory(string $name)
    {
        return $this->redirectRoute('products.search', ['category' => $name], navigate: false);
    }

    public function submit()
    {
        $params = array_filter([
            'q' => $this->q ?: null,
            'category' => $this->category ?: null,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'sort' => $this->sort !== 'relevance' ? $this->sort : null,
        ], fn($v) => $v !== null && $v !== '');

        return $this->redirectRoute('products.search', $params, navigate: false);
    }

    public function render()
    {
        return view('livewire.shop.product-search-bar', [
            'suggestions' => $this->suggestions,
            'categories' => $this->categoryOptions,
            'categoryMatches' => $this->categoryMatches,
        ]);
    }
}
