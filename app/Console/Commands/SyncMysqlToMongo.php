<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

// SQL models
use App\Models\Product as SQLProduct;
use App\Models\Category as SQLCategory;

// Mongo models
use App\Models\Mongo\Product as MongoProduct;
use App\Models\Mongo\Category as MongoCategory;

class SyncMysqlToMongo extends Command
{
    protected $signature = 'mongo:sync {--truncate : Clear Mongo collections first}';
    protected $description = 'Copy Products & Categories from MySQL to Mongo for API';

    public function handle(): int
    {
        if ($this->option('truncate')) {
            MongoProduct::truncate();
            MongoCategory::truncate();
            $this->warn('Cleared Mongo products/categories.');
        }

        // categories
        foreach (SQLCategory::all() as $c) {
            MongoCategory::updateOrCreate(
                ['mysql_id' => $c->id],
                ['name' => $c->name, 'created_at' => $c->created_at, 'updated_at' => $c->updated_at]
            );
        }

        // quick FK map for fallback
        $catNameById = SQLCategory::pluck('name', 'id');

        // products (use your relation name: categoryRef)
        foreach (SQLProduct::with('categoryRef')->get() as $p) {
            $categoryName = $p->categoryRef?->name
                ?? (is_string($p->category) && $p->category !== '' ? $p->category : null)
                ?? ($p->category_id ? ($catNameById[$p->category_id] ?? null) : null);

            MongoProduct::updateOrCreate(
                ['mysql_id' => $p->id],
                [
                    'name' => $p->name,
                    'price' => (float) $p->price,
                    'category_id' => $p->category_id,
                    'category' => $categoryName,
                    'description' => $p->description,
                    'image_path' => $p->image_path,
                    'is_subscription' => (bool) $p->is_subscription,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at,
                ]
            );
        }

        $this->info('Sync complete.');
        return self::SUCCESS;
    }
}
