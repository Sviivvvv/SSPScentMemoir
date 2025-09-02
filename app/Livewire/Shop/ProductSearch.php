<?php

namespace App\Livewire\Shop;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;


#[Layout('components.layouts.site')]
class ProductSearch extends Component
{
    use WithPagination;

    // Keep state in the URL
    #[Url(as: 'q')] public string $q = '';
    #[Url] public string $category = '';
    #[Url] public ?float $minPrice = null;
    #[Url] public ?float $maxPrice = null;
    #[Url] public string $sort = 'relevance';


    public array $categories = [];

    public int $formVersion = 0;

    public function mount(): void
    {
        $this->categories = $this->categoriesList();
    }

    /** When ANY bound property changes, go back to page 1 so results refresh immediately */
    public function updated($name, $value): void
    {
        $this->resetPage();
    }

    protected function categoriesList(): array
    {
        $fromTable = Category::query()->orderBy('name')->pluck('name')->all();
        $legacy = Product::query()
            ->whereNotNull('category')->where('category', '!=', '')
            ->distinct()->orderBy('category')->pluck('category')->all();

        return array_values(array_unique(array_merge($fromTable, $legacy)));
    }

    /** ----- Suggestions (computed) ----- */
    public function getSuggestionsProperty()
    {
        $term = trim($this->q);
        if (mb_strlen($term) < 1)
            return collect();

        return Product::query()
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('is_subscription')
                    ->orWhere('is_subscription', false);
            })
            ->where('name', 'like', '%' . $term . '%')
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'image_path']);
    }

    public function getCategoryMatchesProperty()
    {
        $term = trim($this->q);
        if (mb_strlen($term) < 1)
            return collect();

        $t = mb_strtolower($term);
        return collect($this->categories)
            ->filter(fn($n) => str_contains(mb_strtolower($n), $t))
            ->take(5)
            ->values();
    }

    /** ----- Actions ----- */
    public function selectSuggestion(int $id)
    {
        return $this->redirectRoute('products.show', $id);
    }

    public function goToCategory(string $name)
    {
        // Apply immediately & refresh
        $this->category = $name;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        // Reset all filters to defaults
        $this->q = '';
        $this->category = '';
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->sort = 'relevance';

        // also reset to page 1 and re-render
        $this->resetPage();
        $this->formVersion++;
    }

    public function render()
    {
        $builder = Product::query()
            ->whereNull('deleted_at')
            // hide subscriptions in all listings
            ->where(function ($q) {
                $q->whereNull('is_subscription')
                    ->orWhere('is_subscription', false);
            })
            ->select(['id', 'name', 'price', 'image_path', 'category', 'category_id', 'created_at']);

        // category filter (case-insensitive, table or legacy column)
        if ($this->category !== '') {
            $lc = mb_strtolower($this->category);
            $builder->where(function ($w) use ($lc) {
                $w->whereRaw('LOWER(category) = ?', [$lc])
                    ->orWhereHas('categoryRef', fn($c) => $c->whereRaw('LOWER(name) = ?', [$lc]));
            });
        }

        // price range
        if ($this->minPrice !== null && $this->minPrice !== '') {
            $builder->where('price', '>=', (float) $this->minPrice);
        }
        if ($this->maxPrice !== null && $this->maxPrice !== '') {
            $builder->where('price', '<=', (float) $this->maxPrice);
        }

        // free text
        if (trim($this->q) !== '') {
            $term = '%' . trim($this->q) . '%';
            $builder->where(function ($qq) use ($term) {
                $qq->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('category', 'like', $term)
                    ->orWhereHas('categoryRef', fn($c) => $c->where('name', 'like', $term));
            });
        }

        // sort
        switch ($this->sort) {
            case 'latest':
                $builder->orderByDesc('created_at');
                break;
            case 'price_asc':
                $builder->orderBy('price');
                break;
            case 'price_desc':
                $builder->orderBy('price', 'desc');
                break;
            case 'name':
                $builder->orderBy('name');
                break;
            default: // relevance
                if (trim($this->q) !== '') {
                    $term = '%' . trim($this->q) . '%';
                    $builder->orderByRaw(
                        "name LIKE ? DESC, description LIKE ? DESC, created_at DESC",
                        [$term, $term]
                    );
                } else {
                    $builder->orderByDesc('created_at');
                }
        }

        // Use normal paginate (not simplePaginate) and keep the current querystring
        $products = $builder->paginate(12)->withQueryString();

        return view('livewire.shop.search', [
            'products' => $products,
            'categories' => $this->categories,
            'suggestions' => $this->suggestions,
            'categoryMatches' => $this->categoryMatches,
        ]);
    }
}
