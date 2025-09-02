<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportLegacyProductsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Ensure directories exist on the public disk
        Storage::disk('public')->makeDirectory('products');

        // 1) Ensure categories exist in new DB based on legacy product.category string
        $legacyCategories = DB::connection('legacy')
            ->table('product')
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $categoryIdMap = []; // name => id
        foreach ($legacyCategories as $name) {
            $existing = DB::table('categories')->where('name', $name)->first();
            if ($existing) {
                $categoryIdMap[$name] = $existing->id;
            } else {
                $id = DB::table('categories')->insertGetId([
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $categoryIdMap[$name] = $id;
            }
        }

        // 2) Pull legacy products
        $rows = DB::connection('legacy')->table('product')->select([
            'productID',
            'ProductName',
            'price',
            'category',
            'description',
            'imagePath',
            'isSubscription',
            'isDeleted',
        ])->get();

        $inserted = 0;
        $updated = 0;
        $copied = 0;
        $missingImages = 0;

        foreach ($rows as $r) {
            // Normalize/clean old image path
            $old = trim((string) ($r->imagePath ?? ''));
            $normalized = $old;
            if ($normalized !== '') {
                // Make it like "src/..." (remove leading "./" and backslashes)
                $normalized = ltrim(str_replace(['./', '\\'], ['/', '/'], $normalized), '/');
            }

            // Try to copy from /public/src/... to storage/app/public/products/...
            $storedPath = null;
            if ($normalized !== '') {
                $sourceAbs = public_path($normalized); // e.g. public/src/productPics/...
                if (is_file($sourceAbs)) {
                    $ext = pathinfo($sourceAbs, PATHINFO_EXTENSION) ?: 'png';
                    $base = Str::slug($r->ProductName ?: ('product-' . $r->productID));
                    $dest = "products/{$base}-{$r->productID}.{$ext}";
                    $bytes = @file_get_contents($sourceAbs);
                    if ($bytes !== false) {
                        Storage::disk('public')->put($dest, $bytes);
                        $storedPath = $dest; // save this in products.image_path
                        $copied++;
                    }
                } else {
                    $missingImages++;
                }
            }

            // Map category string -> category_id
            $categoryId = null;
            if (!empty($r->category) && isset($categoryIdMap[$r->category])) {
                $categoryId = $categoryIdMap[$r->category];
            }

            // Soft delete timestamp
            $deletedAt = ($r->isDeleted ?? 0) ? $now : null;

            // Upsert by name (change to another key if you prefer)
            $exists = DB::table('products')->where('name', $r->ProductName)->first();

            $payload = [
                'name' => $r->ProductName,
                'price' => (float) $r->price,
                'category' => $r->category,     // keep legacy label too
                'category_id' => $categoryId,
                'description' => $r->description,
                // If we copied into storage, use that. Else keep normalized public path like "src/...".
                'image_path' => $storedPath ?: ('/' . $normalized),
                'is_subscription' => (bool) $r->isSubscription,
                'deleted_at' => $deletedAt,
                'updated_at' => $now,
            ];

            if ($exists) {
                DB::table('products')->where('id', $exists->id)->update($payload);
                $updated++;
            } else {
                $payload['created_at'] = $now;
                DB::table('products')->insert($payload);
                $inserted++;
            }
        }

        $this->command->info("Import complete. Inserted: {$inserted}, Updated: {$updated}, Copied images: {$copied}, Missing images: {$missingImages}");
    }
}
