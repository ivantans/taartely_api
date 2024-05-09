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
        Schema::create('custom_cake_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->enum("custom_cake_order_status_id", [1,2,3]);
            $table->bigInteger("custom_cake_order_price"); 
            $table->string("custom_cake_order_design_theme");
            $table->string("custom_cake_order_color");
            $table->string("custom_cake_order_size");
            $table->string("custom_cake_order_due_date");
            $table->string("custom_cake_order_response");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_cake_orders');
    }
};
