<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'price',
        'category_id',   // FK -> categories.id
        'category',      // legacy label kept for compatibility
        'description',
        'image_path',
        'is_subscription',
    ];

    protected $casts = [
        'price' => 'float',
        'is_subscription' => 'boolean',
    ];




    public function getCategoryNameAttribute(): string
    {
        return $this->categoryRef?->name ?? ($this->category ?? 'Uncategorized');
    }
    /** Resolve image whether stored on disk or legacy /src/... */
    public function getImageUrlAttribute(): string
    {
        $p = trim((string) ($this->image_path ?? ''));

        if ($p === '') {
            return asset('/placeholder.png');
        }

        // Stored on the public disk -> serve via /storage/...
        if (Str::startsWith($p, ['products/', 'ads/', 'reviews/'])) {
            return Storage::url($p);
        }

        // Absolute or site-rooted paths already fine
        if (Str::startsWith($p, ['http://', 'https://', '/'])) {
            return $p;
        }

        // Legacy assets under /public/src/... (from first semester)
        return asset('/' . ltrim(str_replace('\\', '/', $p), '/'));
    }

    /** Relations */
    public function categoryRef()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }

    /** Scopes */
    public function scopeCategoryName($q, string $name)
    {
        $lc = mb_strtolower($name);

        return $q->where(function ($w) use ($lc) {
            $w->whereRaw('LOWER(category) = ?', [$lc])
                ->orWhereHas('categoryRef', fn($c) => $c->whereRaw('LOWER(name) = ?', [$lc]));
        });
    }

    public function scopeLimited($q)
    {
        return $q->where(function ($w) {
            $w->whereRaw('LOWER(category) = ?', ['limited'])
                ->orWhereHas('categoryRef', fn($c) => $c->whereRaw('LOWER(name) = ?', ['limited']));
        });
    }
    public function scopeMen($q)
    {
        return $this->scopeCategoryName($q, 'men');
    }
    public function scopeWomen($q)
    {
        return $this->scopeCategoryName($q, 'women');
    }


}
