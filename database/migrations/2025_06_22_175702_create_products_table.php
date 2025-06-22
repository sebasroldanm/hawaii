<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('price');
            $table->boolean('is_in_stock')->default(true);
            $table->boolean('is_composite')->default(false);
            $table->float('stock')->nullable();
            $table->integer('preparation_time')->nullable();
            $table->string('preparation_area')->nullable();
            $table->foreignId('category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
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
