<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = ["id"];

    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }
    public function productCategory(){
        return $this->belongsTo(ProductCategory::class, "product_category_id");
    }
    public function userOrderDetails(){
        return $this->hasMany(UserOrderDetail::class, "product_id");
    }
    public function userCartDetails(){
        return $this->hasMany(UserCartDetail::class, "product_id");
    }
    public function productImages(){
        return $this->hasMany(ProductImage::class, "product_id");
    }
    public function productReviews(){
        return $this->hasMany(ProductReview::class, "product_id");
    }
    public function productStatus(){
        return $this->belongsTo(ProductStatus::class, "product_status_id");
    }

    public function sluggable():array{
        return [
            "product_slug" => [
                "source" => "product_name"
            ]
        ];
    }
}
