<?php

include_once "apitest.php";
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Product\ProductCategoryController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Profile\UserController;
use App\Models\ProductCategory;
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
Route::delete("/logout", [LogoutController::class, "logout"]);
Route::post("/register", [RegisterController::class, 'register']);
Route::post("/login", [LoginController::class, "login"]);


// User profile
Route::get("/users", [UserController::class, "getUserProfile"]);

// Product Category
Route::middleware(["auth:sanctum"])->group(function(){
    Route::get("/categories", [ProductCategoryController::class, "index"]);
    Route::post("/categories", [ProductCategoryController::class, "store"]);
    Route::put("/categories/{id}", [ProductCategoryController::class, "update"]);
    Route::delete("/categories/{id}", [ProductCategoryController::class, "destroy"]);
});

Route::middleware(["auth:sanctum"])->group(function(){
    Route::post("/products", [ProductController::class, "store"]);
    Route::get("/products", [ProductController::class, "index"]);
    Route::get("/products/{id}", [ProductController::class, "show"]);
});


Route::get("/products", [ProductController::class, "index"]);
Route::get("/product/{id}", [ProductController::class, "show"]);

















