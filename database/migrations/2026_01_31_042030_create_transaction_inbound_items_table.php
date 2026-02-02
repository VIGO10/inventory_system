<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_inbound_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_inbound_id')->constrained()->onDelete('cascade');
            $table->foreignId('catalog_supplier_id')->constrained('catalog_suppliers')->onDelete('restrict');
            $table->foreignId('catalog_id')->constrained('catalogs')->onDelete('restrict');
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');

            $table->decimal('price', 15, 4)->default(0);           // final price per item after discount
            $table->decimal('title_1_qty', 12, 2)->default(0);
            $table->decimal('title_1_price', 15, 4)->default(0);
            $table->decimal('title_2_qty', 12, 2)->nullable();
            $table->decimal('title_2_price', 15, 4)->nullable();

            $table->decimal('discount', 15, 4)->default(0);        // discount for this line item

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_inbound_items');
    }
};