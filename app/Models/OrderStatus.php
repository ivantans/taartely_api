<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;
    protected $guarded = ["id"];

    public function userOrders(){
        return $this->hasMany(UserOrder::class, "order_status_id");
    }
    public function customCakeOrders(){
        return $this->hasMany(CustomCakeOrder::class, "custom_cake_order_status_id");
    }

    public function userOrderRequests(){
        return $this->hasMany(UserOrderRequest::class, "user_order_request_status_id");
    }

}
