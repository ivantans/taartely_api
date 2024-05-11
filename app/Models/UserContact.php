<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserContact extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }
    public function userOrders(){
        return $this->hasMany(UserOrder::class, "user_contact_id");
    }
}
