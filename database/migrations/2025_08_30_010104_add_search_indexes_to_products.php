<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!$this->hasIndex('products', 'products_name_index')) {
                $table->index('name');
            }
            if (!$this->hasIndex('products', 'products_category_index') && Schema::hasColumn('products', 'category')) {
                $table->index('category');
            }
            if (!$this->hasIndex('products', 'products_price_index')) {
                $table->index('price');
            }
            if (Schema::hasColumn('products', 'category_id') && !$this->hasIndex('products', 'products_category_id_index')) {
                $table->index('category_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
            if (Schema::hasColumn('products', 'category'))
                $table->dropIndex(['category']);
            $table->dropIndex(['price']);
            if (Schema::hasColumn('products', 'category_id'))
                $table->dropIndex(['category_id']);
        });
    }

    private function hasIndex(string $table, string $index): bool
    {
        try {
            return collect(DB::select("SHOW INDEX FROM {$table}"))
                ->contains(fn($r) => ($r->Key_name ?? '') === $index);
        } catch (\Throwable $e) {
            return false;
        }
    }
};
