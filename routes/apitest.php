<?php
use App\Http\Controllers\Api\Product\ProductController;




Route::middleware(["buyer", "auth:sanctum"])->group(function(){
    Route::get("/test", function(){
        return response()->json([
            "success" => true,
        ]);
    });
});
// 
Route::middleware(['auth:sanctum'])->group(function(){
    Route::get("/dev-test", function(){
        return response()->json([
           "id" => auth()->user()->id
        ]);
    });
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post("/data", [ProductController::class, "store"]);