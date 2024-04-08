<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOrderRequest extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }
    public function userCart(){
        return $this->belongsTo(UserCart::class, "user_cart_id");
    }
}
