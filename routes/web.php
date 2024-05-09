<?php

use App\Http\Controllers\Api\Product\ProductCategoryController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Models\User;
use App\Models\UserContact;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/relation-test', function (){

    // $user = User::find(1);
    // echo($user->userContacts()->exists())?1:0;
    // dd(User::find(1));
    // if ($hasPosts) {
    //     echo "User memiliki postingan.";
    // } else {
    //     echo "User tidak memiliki postingan.";
    // }
});
Route::get('/dev-test', [ProductController::class, "store"]);