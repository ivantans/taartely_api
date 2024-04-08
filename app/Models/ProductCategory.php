<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }
    public function products(){
        return $this->hasMany(Product::class, "product_category_id");
    }
}
