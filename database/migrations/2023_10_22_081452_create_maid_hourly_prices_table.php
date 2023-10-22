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
        Schema::create('maid_hourly_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maid_id');
            $table->decimal('price', 10, 2); // Define the price column as decimal for precision
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('maid_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maid_hourly_prices');
    }
};
