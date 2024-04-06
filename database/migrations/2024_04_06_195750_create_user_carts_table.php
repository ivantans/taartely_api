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
        Schema::create('user_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->string("cart_note")->nullable();
            $table->bigInteger("cart_total_price");
            $table->integer("cart_total_product");
            $table->integer("cart_total_quantity");
            $table->dateTime("cart_due_date");
            $table->boolean("cart_hidden");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_carts');
    }
};
