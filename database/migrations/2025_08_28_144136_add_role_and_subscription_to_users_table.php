<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('password');
            $table->unsignedBigInteger('subscription_id')->nullable()->after('role');
            // if you later create a subscriptions table, add FK here
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'subscription_id')) {
                $table->dropIndex(['subscription_id']); // drops users_subscription_id_index
                $table->dropColumn('subscription_id');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropIndex(['role']); // drops users_role_index
                $table->dropColumn('role');
            }
        });
    }
};
