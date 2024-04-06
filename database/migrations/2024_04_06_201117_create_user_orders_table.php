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
        Schema::create('user_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->foreignId("user_contact_id")->constrained();
            $table->string("order_note");
            $table->bigInteger("order_total_price");
            $table->integer("order_total_product");
            $table->integer("order_total_quantity");
            $table->dateTime("order_due_date");
            $table->string("order_payment_status");
            $table->enum("order_status", ["process", "completed", "rejected"]);
            $table->string("order_reason");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_orders');
    }
};
