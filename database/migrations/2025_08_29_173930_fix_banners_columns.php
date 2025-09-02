<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            if (!Schema::hasColumn('banners', 'key')) {
                $table->string('key')->unique()->after('id');
            }
            if (!Schema::hasColumn('banners', 'image_path')) {
                $table->string('image_path')->after('key');
            }
            if (!Schema::hasColumn('banners', 'alt')) {
                $table->string('alt')->nullable()->after('image_path');
            }
            if (!Schema::hasColumn('banners', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('alt');
            }
            // add timestamps if they don't exist
            if (!Schema::hasColumn('banners', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('banners', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        // optional: index for faster lookups
        Schema::table('banners', function (Blueprint $table) {
            if (!$this->hasIndex('banners', 'banners_is_active_index') && Schema::hasColumn('banners', 'is_active')) {
                $table->index('is_active');
            }
        });
    }

    // tiny helper since Schema builder can’t check index names directly
    private function hasIndex(string $table, string $index): bool
    {
        try {
            return collect(DB::select("SHOW INDEX FROM {$table}"))
                ->contains(fn($r) => ($r->Key_name ?? '') === $index);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'is_active')) {
                $table->dropIndex(['is_active']);
            }
            if (Schema::hasColumn('banners', 'updated_at'))
                $table->dropColumn('updated_at');
            if (Schema::hasColumn('banners', 'created_at'))
                $table->dropColumn('created_at');
            if (Schema::hasColumn('banners', 'is_active'))
                $table->dropColumn('is_active');
            if (Schema::hasColumn('banners', 'alt'))
                $table->dropColumn('alt');
            if (Schema::hasColumn('banners', 'image_path'))
                $table->dropColumn('image_path');
            if (Schema::hasColumn('banners', 'key')) {
                $table->dropUnique('banners_key_unique');
                $table->dropColumn('key');
            }
        });
    }
};
