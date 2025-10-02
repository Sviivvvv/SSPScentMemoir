<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class Order extends Model
{
    //
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    // with total_amount, ordered_at
    // items = [{product_id,name,price,quantity,line_total}]
    protected $fillable = ['user_id','status','total_amount','ordered_at','items','created_at'];
    protected $casts = [
        'total_amount'=>'float','items'=>'array',
        'ordered_at'=>'datetime','created_at'=>'datetime',
    ];
}
