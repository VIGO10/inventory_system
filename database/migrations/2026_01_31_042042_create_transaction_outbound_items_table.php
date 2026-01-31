<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_outbound_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_outbound_id')->constrained()->onDelete('cascade');
            $table->foreignId('catalog_id')->constrained('catalogs')->onDelete('restrict');

            $table->decimal('price', 15, 4)->default(0);           // final selling price per item
            $table->decimal('title_1_qty', 12, 2)->default(0);
            $table->decimal('title_1_price', 15, 4)->default(0);
            $table->decimal('title_2_qty', 12, 2)->nullable();
            $table->decimal('title_2_price', 15, 4)->nullable();

            $table->decimal('discount', 15, 4)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_outbound_items');
    }
};