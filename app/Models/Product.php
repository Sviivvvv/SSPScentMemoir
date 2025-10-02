<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'price',
        'category_id',
        'category',       // legacy label kept for compatibility
        'description',
        'image_path',
        'is_subscription',
    ];

    protected $casts = [
        'price' => 'float',
        'is_subscription' => 'boolean',
    ];

    // If you want these when array/JSON casting:
    protected $appends = ['image_url', 'category_name'];

    /* -------------------- Accessors -------------------- */

    public function getImageUrlAttribute(): string
    {
        $p = trim((string) $this->image_path);

        if ($p === '') {
            // Put a file at public/images/placeholder.png (or change the path)
            return asset('images/placeholder.png');
        }

        // Stored via store('products','public') => "products/..."
        if (Str::startsWith($p, ['products/', 'ads/', 'reviews/'])) {
            return Storage::url($p); // => /storage/products/...
        }

        // Absolute or site-relative
        if (Str::startsWith($p, ['http://', 'https://', '/'])) {
            return $p;
        }

        // Legacy paths from /public/... (first-semester assets)
        return asset($p);
    }

    public function getCategoryNameAttribute(): string
    {
        return $this->categoryRef?->name ?? ($this->category ?: 'Uncategorized');
    }

    /* -------------------- Relationships -------------------- */

    public function categoryRef()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    // Optional – only if you created App\Models\Subscription
    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'product_id');
    }

    /* -------------------- Scopes -------------------- */

    public function scopeCategoryName($q, string $name)
    {
        $lc = mb_strtolower($name);

        return $q->where(function ($w) use ($lc) {
            $w->whereRaw('LOWER(category) = ?', [$lc])
              ->orWhereHas('categoryRef', fn ($c) => $c->whereRaw('LOWER(name) = ?', [$lc]));
        });
    }

    public function scopeLimited($q)
    {
        return $q->categoryName('limited');
    }

    public function scopeMen($q)
    {
        return $q->categoryName('men');
    }

    public function scopeWomen($q)
    {
        return $q->categoryName('women');
    }

    public function scopeSubscriptions($q)
    {
        return $q->where('is_subscription', true);
    }

    public function scopeNonSubscriptions($q)
    {
        return $q->where(function ($w) {
            $w->where('is_subscription', false)
              ->orWhereNull('is_subscription');
        });
    }

    /* -------------------- Model events -------------------- */

    protected static function booted()
    {
        static::saving(function (Product $p) {
            // Keep categories empty for subscriptions so they don't leak into Men/Women/Limited lists
            if ($p->is_subscription) {
                $p->category_id = null;
                $p->category    = null;
            }
        });
    }
}
