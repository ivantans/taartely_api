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
        Schema::create('user_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_order_id")->constrained();
            $table->foreignId("product_id")->constrained();
            $table->integer("order_detail_quantity");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_order_details');
    }
};
