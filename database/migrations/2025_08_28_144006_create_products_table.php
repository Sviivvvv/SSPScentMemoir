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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // maps your productID
            $table->string('name'); // ProductName
            $table->decimal('price', 12, 2);
            // keep both: a normalized category_id (optional) + legacy string category
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category')->nullable(); // to import your old 'men/women/limited'
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_subscription')->default(false);
            $table->softDeletes();
            $table->timestamps();
            $table->index(['category', 'is_subscription']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
