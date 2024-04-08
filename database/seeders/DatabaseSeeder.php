<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\UserCart;
use App\Models\UserCartDetail;
use App\Models\UserContact;
use App\Models\UserOrder;
use App\Models\UserOrderDetail;
use App\Models\UserOrderRequest;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        User::factory(10)->create();
        ProductCategory::factory(5)->create();
        Product::factory(10)->create();
        ProductImage::factory(10)->create();
        UserCart::factory(5)->create();
        UserCartDetail::factory(20)->create();
        UserContact::factory(20)->create();
        UserOrder::factory(5)->create();
        UserOrderDetail::factory(20)->create();
        UserOrderRequest::factory(10)->create();
    }
}
