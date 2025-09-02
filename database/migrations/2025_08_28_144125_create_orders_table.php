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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // orderID
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('ordered_at')->useCurrent(); // orderDate
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status')->default('paid'); // simple status for now
            $table->timestamps();
            $table->index(['user_id', 'ordered_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
