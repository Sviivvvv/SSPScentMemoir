<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            // add only if missing
            if (!Schema::hasColumn('banners', 'key')) {
                $table->string('key')->unique()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'key')) {
                $table->dropUnique('banners_key_unique');
                $table->dropColumn('key');
            }
        });
    }
};
