<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnsureMongoIndexes extends Command
{
    protected $signature = 'mongo:indexes';
    protected $description = 'Create MongoDB indexes for API collections';

    public function handle(): int
    {
        $db = DB::connection('mongodb')->getMongoDB();

        // products
        $db->selectCollection('products')->createIndex(['mysql_id' => 1], ['unique' => true]);
        $db->selectCollection('products')->createIndex(['name' => 'text', 'category' => 'text']);
        $db->selectCollection('products')->createIndex(['category_id' => 1, 'price' => 1]);

        // categories
        $db->selectCollection('categories')->createIndex(['mysql_id' => 1], ['unique' => true]);

        // carts (TTL on updated_at)
        $db->selectCollection('carts')->createIndex(['user_id' => 1], ['unique' => true]);
        $db->selectCollection('carts')->createIndex(['updated_at' => 1], ['expireAfterSeconds' => 60 * 60 * 24 * 7]);

        // orders
        $db->selectCollection('orders')->createIndex(['user_id' => 1, 'ordered_at' => -1]);

        $this->info('Mongo indexes ensured.');
        return self::SUCCESS;
    }
}
