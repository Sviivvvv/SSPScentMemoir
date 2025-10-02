<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Subscription extends Model
{
    protected $fillable = [
        'product_id',
        'tier',
        'monthly_price',
        'benefits',
        'is_active',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
