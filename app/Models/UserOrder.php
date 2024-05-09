<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOrder extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }
    public function userContact(){
        return $this->belongsTo(UserContact::class, "user_contact_id");
    }
    public function userOrderDetails(){
        return $this->hasMany(UserOrderDetail::class, "user_order_id");
    }
}
