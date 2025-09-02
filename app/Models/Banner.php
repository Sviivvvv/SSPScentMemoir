<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = ['key', 'image_path', 'alt', 'is_active'];

    public function getUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }

    public static function urlFor(string $key): ?string
    {
        $b = static::where('key', $key)->where('is_active', true)->first();
        return $b ? $b->url : null;
    }
}
