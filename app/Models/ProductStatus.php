<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStatus extends Model
{
    use HasFactory;

    protected $guarded = ["id"];
    
    public function products(){
        return $this->hasMany(Product::class, "product_status_id");
    }
}
