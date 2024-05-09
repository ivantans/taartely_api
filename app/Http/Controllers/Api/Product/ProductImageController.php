<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Utils\UserRoleUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function uploadImage(Request $request, string $id)
    {
        try{
            UserRoleUtil::sellerStrict();

            $product = Product::findOrFail($id);

            if ($request->hasFile("image")) {
                $validator = Validator::make($request->all(), [
                    'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Contoh validasi: file gambar, maksimal 2MB
                ]);
                if ($validator->fails()) {
                    return throw new \Illuminate\Validation\ValidationException($validator);
                }        
            } 

            if($request->hasFile("image")){
                $image = $request->file("image");
                $path = $image->store('images', 's3');
                $url = Storage::disk('s3')->url($path);
                ProductImage::create([
                    "user_id" => auth()->user()->id,
                    "product_id" => $product->id,
                    "product_image_path" => $url
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Gambar berhasil di upload"
            ]);

        } catch(\Exception $e){
            return response()->json([
                "success" => true,
                "message" => $e->getMessage()
            ]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $image = ProductImage::findOrFail($id);
            $image->delete();
            return response()->json([
                "success" => true,
                "message" => "Image berhasil dihapus"
            ],200);
        } catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ],500);
        }
    }
}
