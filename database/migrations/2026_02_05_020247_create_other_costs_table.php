<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('other_costs', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->decimal('price', 15, 4)->default(0);

            // 'in' = income/additional revenue, 'out' = expense/cost
            $table->enum('type', ['in', 'out'])->default('out');

            // Date when this cost occurred (e.g., delivery fee date, bonus date, etc.)
            $table->date('date')->nullable(false)->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('other_costs');
    }
};