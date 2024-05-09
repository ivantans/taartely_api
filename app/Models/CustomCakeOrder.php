<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomCakeOrder extends Model
{
    use HasFactory;
    protected $guarded = ["id"];

    public function customCakeOrderImages(){
        return $this->hasMany(CustomCakeOrderImage::class, "custom_cake_order_id");
    }

    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }

}
