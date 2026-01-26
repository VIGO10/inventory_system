<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();

            $table->string('prefix', 40)->nullable();
            $table->string('name', 150);
            $table->string('slug')->unique()->nullable();

            $table->string('phone_number', 30)->nullable();
            $table->text('address')->nullable();

            $table->string('vendor_image', 255)->nullable();

            $table->timestamps();

            // indexes
            $table->index('phone_number');
            $table->index('slug');
            $table->index(['prefix', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};