<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'products';


    // fields the API needs same as SQL
    protected $fillable = [
        'mysql_id',
        'name',
        'price',
        'category_id',
        'category',
        'description',
        'image_path',
        'is_subscription',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'price' => 'float',
        'is_subscription' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // image url helper
    protected $appends = ['image_url'];
    public function getImageUrlAttribute()
    {
        return $this->image_path ? url('/storage/' . $this->image_path) : null;
    }

}
