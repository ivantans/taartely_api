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
        Schema::create('user_order_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->foreignId("user_cart_id")->constrained();
            $table->foreignId("user_contact_id")->constrained();
            $table->enum("user_order_request_status_id", [1,2,3]);
            $table->string("user_order_request_note");
            $table->timestamp("user_order_request_due_date");
            $table->string("user_order_request_reason");
            $table->bigInteger("user_order_total_price");
            $table->integer("user_order_total_product");
            $table->integer("user_order_total_quantity");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_order_requests');
    }
};
