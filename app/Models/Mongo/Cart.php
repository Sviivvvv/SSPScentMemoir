<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class Cart extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'carts';

    // items = [{ product_id: SQL id, quantity: int }]
    protected $fillable = ['user_id','items','updated_at'];
    protected $casts = ['items'=>'array','updated_at'=>'datetime'];
}
