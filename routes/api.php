<?php

include_once "apitest.php";
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Product\ProductCategoryController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Product\ProductImageController;
use App\Http\Controllers\Api\Product\UserCartController;
use App\Http\Controllers\Api\Product\UserCartDetailController;
use App\Http\Controllers\Api\Profile\UserContactController;
use App\Http\Controllers\Api\Profile\UserController;
use App\Http\Controllers\Api\Review\ReviewController;
use App\Http\Controllers\Api\Transaction\UserOrderController;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\UserContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware(["validate.server.key", 'auth:sanctum'])->group(function(){
    // }); 
    
// Authentication

Route::post("/register", [RegisterController::class, 'register']);
Route::post("/login", [LoginController::class, "login"]);






Route::middleware(["auth:sanctum"])->group(function(){
    Route::delete("/logout", [LogoutController::class, "logout"]);
    //! User profile
    Route::get("/users", [UserController::class, "getUserProfile"]);
    //! User address
    Route::get("/contacts", [UserContactController::class, "index"]);
    Route::get("/contacts/{id}", [UserContactController::class, "show"]);
    Route::post("/contacts", [UserContactController::class, "store"]);
    Route::put("/contacts/{id}", [UserContactController::class, "update"]);
    Route::delete("/contacts/{id}", [UserContactController::class, "destroy"]);
    //! Product Category
    Route::get("/categories", [ProductCategoryController::class, "index"]);
    Route::post("/categories", [ProductCategoryController::class, "store"]);
    Route::put("/categories/{id}", [ProductCategoryController::class, "update"]);
    Route::delete("/categories/{id}", [ProductCategoryController::class, "destroy"]);
    //! Product
    Route::get("/products", [ProductController::class, "searchByCategory"]);
    Route::post("/products", [ProductController::class, "store"]);
    Route::get("/products/{id}", [ProductController::class, "show"]);
    Route::put("/products/{id}", [ProductController::class, "update"]);
    Route::delete("/products/{id}", [ProductController::class, "destroy"]);
    
    //! Product Image
    Route::post("product/images/{id}", [ProductImageController::class, "uploadImage"]);
    Route::delete("product/images/{id}", [ProductImageController::class, "destroy"]);
    //! Cart
    Route::get("/carts", [UserCartController::class, "index"]);
    Route::post("/carts", [UserCartController::class, "store"]);
    Route::put("/carts/{id}", [UserCartController::class, "update"]);
    Route::delete("/carts/{id}", [UserCartController::class, "destroy"]);
});



// Order
Route::middleware(["auth:sanctum"])->group(function(){
    Route::get("/orders", [UserOrderController::class, "index"]);
    Route::post("/orders", [UserOrderController::class, "store"]);
    Route::get("/orders/{id}", [UserOrderController::class, "show"]);
    Route::put("/orders/approve/{id}", [UserOrderController::class, "approve"]);
    Route::put("/orders/complete/{id}", [UserOrderController::class, "complete"]);
    Route::put("/orders/cancel/{id}", [UserOrderController::class, "cancel"]);

});
// Review
Route::middleware(["auth:sanctum"])->group(function(){
    Route::post("/reviews/{productId}/{userOrderId}", [ReviewController::class, "store"]);
    Route::get("/reviews/{productId}", [ReviewController::class, "show"]);
    Route::post("/reviews/show-review/{reviewId}", [ReviewController::class, "showReview"]);
    Route::post("/reviews/hide-review/{reviewId}", [ReviewController::class, "hideReview"]);

});















