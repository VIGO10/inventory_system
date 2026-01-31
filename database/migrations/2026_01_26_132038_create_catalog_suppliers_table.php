<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_suppliers', function (Blueprint $table) {
            $table->id();
            
            $table->string('name', 150);
            $table->text('description')->nullable();
            
            $table->string('title_1', 100)->nullable();         // e.g. "Pack", "Box", "Kg"
            $table->string('title_2', 100)->nullable();         // e.g. "Pcs", "Gram", "Liter"
            
            $table->unsignedInteger('value_per_title_2')->nullable();
            
            $table->decimal('title_1_price', 15, 4)->default(0);
            $table->decimal('title_2_price', 15, 4)->nullable();

            $table->string('minimum_order_title', 50)->nullable(); // "title_1" or "title_2"
            $table->unsignedInteger('minimum_order_qty')->default(1);

            $table->string('product_image', 255)->nullable();  // logo / photo of supplier
            
            $table->foreignId('supplier_id')
                  ->constrained('suppliers')
                  ->onDelete('cascade');
                  
            $table->boolean('is_available')->default(true);     // or ->tinyInteger()->default(1)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_suppliers');
    }
};