<?php
use App\Http\Controllers\Api\Product\ProductController;





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
Route::get("/test", function(){
    return response()->json([
        "data1" => "anjay1",
        "data2" => "anjay2"
    ],200);
});