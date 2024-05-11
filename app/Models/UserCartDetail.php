<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCartDetail extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    public function product(){
        return $this->belongsTo(Product::class, "product_id");
    }

    public function userCart(){
       return $this->belongsTo(UserCart::class, "user_cart_id"); 
    }
}
