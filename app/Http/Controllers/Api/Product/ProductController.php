<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            if(Auth::check() && auth()->user()->roles == "seller"){
                $products = Product::with(["productCategory", "productStatus"])->get();
                $data = $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'product_name' => $product->product_name,
                        'product_category' => $product->productCategory ? $product->productCategory->product_category_name : null,
                        'product_slug' => $product->product_slug,
                        'product_price' => $product->product_price,
                        "product_composision" => $product->product_composision,
                        "product_description" => $product->product_description,
                        "product_status" => $product->productStatus->product_status_name,
                    ];
                });
                return response()->json([
                    "success" => true,
                    "data" => $data
                ], 200);

            } else{
                $products = Product::with(["productCategory", "productStatus"])->where("product_status_id", "!=", 3)->get();
                $data = $products->map(function ($product){
                    return [
                        'id' => $product->id,
                        'product_name' => $product->product_name,
                        'product_category' => $product->productCategory ? $product->productCategory->product_category_name : null,
                        'product_slug' => $product->product_slug,
                        'product_price' => $product->product_price,
                        "product_composision" => $product->product_composision,
                        "product_description" => $product->product_description,
                        "product_status" => $product->productStatus->product_status_name,
                    ];          
                });
                return response()->json([
                    "success" => true,
                    "data" => $data
                ], 200);
            }

            

        } catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], 500);
        }


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            if ($request->hasFile("images")) {
                $validator = Validator::make($request->all(), [
                    'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Contoh validasi: file gambar, maksimal 2MB
                ]);
                if ($validator->fails()) {
                    return throw new \Illuminate\Validation\ValidationException($validator);
                }        
            } 
            $validatedData = $request->validate([
                "product_name" => "required|string|max:255|",
                "product_price" => "required|numeric",
                "product_composision" => "required|string|max:1000",
                "product_description" => "required|string|max:1000",
                "product_category_id" => "nullable|numeric"
            ]); 
            
            $user_id = auth()->user()->id;
            $product = Product::create([
                "user_id" => $user_id,
                "product_category_id" => $request->has("product_category_id") ? $validatedData["product_category_id"] : null,
                "product_name" => $validatedData["product_name"],
                "product_price" => $validatedData["product_price"],
                "product_composision" => $validatedData["product_composision"],
                "product_description" => $validatedData["product_description"],
                "product_status_id" => 1,
            ]);

            if($request->hasFile("images")){
                $images = $request->file("images");
                foreach($images as $key => $image){
                    $path = $image->store('images', 's3');
                    $url[] = Storage::disk('s3')->url($path);
                    ProductImage::create([
                        "user_id" => $user_id,
                        "product_id" => $product->id,
                        "product_image_path" => $url[$key]
                    ]);
                }
            }
     
            $data = [
                "id" => $product->id,
                "product_name" => $product->product_name,
            ];
     
            return response()->json([
                "success" => true,
                "data" => $data,
            ]);  

        } catch (\Illuminate\Validation\ValidationException $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], 422);
        } catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(["productCategory", "productImages"])->findOrFail($id);

        foreach ($product->productImages as $productImage) {
            $url[] = $productImage->product_image_path;
        }
        $data = [
            "id" => $product->id,
            "product_name" => $product->product_name,
            "product_category" => $product->productCategory->product_category_name,
            "product_slug" => $product->product_slug,
            "product_price" => $product->product_price,
            "product_composision" => $product->product_composision,
            "product_description" => $product->product_description,
            "created_at" => $product->created_at,
            "url" => $url
        ];
        return response()->json([
            "data" => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
