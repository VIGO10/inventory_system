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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('prefix', 40)->nullable();            // e.g. PT, CV, Toko, UD, etc.
            $table->string('name', 150);
            $table->string('slug')->unique()->nullable();

            $table->string('phone_number', 30)->nullable();
            $table->text('address')->nullable();

            $table->string('supplier_image', 255)->nullable();  // logo / photo of supplier

            $table->timestamps();

            // Useful indexes
            $table->index('phone_number');
            $table->index('slug');
            $table->index(['prefix', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};