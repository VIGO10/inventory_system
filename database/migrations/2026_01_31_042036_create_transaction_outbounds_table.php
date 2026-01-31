<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_outbounds', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('vendor_id')->constrained()->onDelete('restrict');

            $table->decimal('total_price', 15, 4)->default(0);
            $table->decimal('discount', 15, 4)->default(0);
            $table->date('deadline_payment_date')->nullable();
            $table->dateTime('created_date')->useCurrent();
            $table->boolean('is_published')->default(false);
            $table->dateTime('published_date')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->dateTime('completed_date')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->dateTime('paid_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_outbounds');
    }
};