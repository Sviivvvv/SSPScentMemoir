<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Review extends Model
{
    protected $fillable = [
        'author_name',
        'rating',
        'quote',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order')->orderByDesc('id');
    }

    public function getImageUrlAttribute(): string
    {
        $p = trim((string) ($this->image_path ?? ''));
        if ($p === '')
            return asset('/placeholder.png');

        if (Str::startsWith($p, ['reviews/'])) {
            return Storage::url($p); // /storage/reviews/...
        }

        if (Str::startsWith($p, ['http://', 'https://', '/']))
            return $p;

        return asset('/' . ltrim(str_replace('\\', '/', $p), '/'));
    }
}
