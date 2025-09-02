<?php

namespace App\Livewire\Admin\Ads;

use Livewire\Component;
use App\Models\Product;

class LinkPicker extends Component
{
    public string $mode = 'product'; // 'product' | 'custom'

    // Search / selection state
    public string $q = '';
    public ?int $selectedId = null;
    public string $linkUrl = '';

    // Dropdown state
    public array $results = [];
    public bool $showDropdown = false;
    public int $highlight = 0;

    // Initial value (edit form)
    public ?string $initialUrl = null;

    public function mount(?string $initialUrl = null): void
    {
        $this->initialUrl = $initialUrl;

        if ($initialUrl) {
            // keep existing URL; do not clear unless user clicks "Clear"
            $this->mode = 'custom';
            $this->linkUrl = $initialUrl;
        } else {
            // preload helpful suggestions
            $this->results = $this->defaultSuggestions();
        }
    }

    /** Open dropdown (don’t clear selection) */
    public function openDropdown(): void
    {
        $this->showDropdown = true;

        // If switching back to product mode and a URL already exists,
        // try to hydrate selection from /products/{id}
        if ($this->mode === 'product' && $this->q === '') {
            $this->hydrateFromLinkUrl();
        }

        if (empty($this->q) && empty($this->results)) {
            $this->results = $this->defaultSuggestions();
        }
    }

    /** Close only the dropdown, never clear selection */
    public function closeDropdown(): void
    {
        $this->showDropdown = false;
    }

    /** Instant search: run on every keypress */
    public function updatedQ(): void
    {
        $this->showDropdown = true;
        $term = trim($this->q);

        if ($term === '') {
            $this->results = $this->defaultSuggestions();
            $this->highlight = 0;
            return;
        }

        $rows = Product::query()
            ->where('name', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name']);

        $this->results = $rows->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->all();
        $this->highlight = 0;
    }

    /** Select product; keep it until user clicks Clear */
    public function selectProduct(int $id): void
    {
        $p = Product::find($id);
        if (!$p)
            return;

        $this->selectedId = $p->id;
        $this->q = $p->name;
        $this->linkUrl = route('products.show', $p->id); // absolute URL
        $this->results = [];
        $this->showDropdown = false;  // <- closes the list immediately
    }

    /** Only this clears the chosen link */
    public function clearSelection(): void
    {
        $this->selectedId = null;
        $this->q = '';
        $this->linkUrl = '';
        $this->results = $this->defaultSuggestions();
        $this->showDropdown = true;
        $this->highlight = 0;
    }

    /** Switching modes must NOT clear the chosen link */
    public function switchMode(string $mode): void
    {
        $this->mode = in_array($mode, ['product', 'custom'], true) ? $mode : 'product';

        if ($this->mode === 'product') {
            // Don’t clear link; try to populate q/selected from existing URL if possible
            $this->hydrateFromLinkUrl();
            $this->openDropdown();
        } else {
            // custom mode: keep existing $linkUrl as-is
            $this->showDropdown = false;
        }
    }

    /** Keyboard navigation */
    public function moveHighlight(int $delta): void
    {
        $count = count($this->results);
        if ($count === 0)
            return;

        $this->highlight = ($this->highlight + $delta) % $count;
        if ($this->highlight < 0)
            $this->highlight += $count;
    }

    public function confirmHighlight(): void
    {
        if (!isset($this->results[$this->highlight]))
            return;
        $this->selectProduct($this->results[$this->highlight]['id']);
    }

    /** “Forgot the name” fallback list */
    protected function defaultSuggestions(): array
    {
        $limited = Product::query()
            ->where('category', 'limited')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get(['id', 'name']);

        $recent = Product::query()
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get(['id', 'name']);

        return $limited->concat($recent)
            ->unique('id')
            ->take(8)
            ->map(fn($p) => ['id' => $p->id, 'name' => $p->name])
            ->values()
            ->all();
    }

    /** If linkUrl points to /products/{id}, hydrate q/selectedId */
    protected function hydrateFromLinkUrl(): void
    {
        if (!$this->linkUrl)
            return;

        if (preg_match('~/products/(\d+)\b~', $this->linkUrl, $m)) {
            $p = Product::find((int) $m[1]);
            if ($p) {
                $this->selectedId = $p->id;
                $this->q = $p->name;
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.ads.link-picker');
    }
}
