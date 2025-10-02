<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class Category extends Model
{
    //
    protected $connection = 'mongodb';
    protected $collection = 'categories';

    protected $fillable = ['mysql_id', 'name', 'created_at', 'updated_at'];
    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];
}
