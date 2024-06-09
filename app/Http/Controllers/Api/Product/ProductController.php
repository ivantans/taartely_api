<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Utils\UserRoleUtil;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $seller = UserRoleUtil::sellerRoles();

            if ($seller) {
                $products = Product::with(["productStatus"])
                    ->where("product_status_id", "!=", 4)
                    ->get();


                $data = $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'product_name' => $product->product_name,
                        'product_category' => $product->productCategory == null ? "Kue" : $product->productCategory->product_category_name,
                        'product_slug' => $product->product_slug,
                        'product_price' => $product->product_price,
                        "product_composision" => $product->product_composision,
                        "product_description" => $product->product_description,
                        "product_status" => $product->productStatus->product_status_name,
                        "images" => $product->productImages->first() ? $product->productImages->first()->product_image_path : null,
                    ];
                });

                return response()->json([
                    "roles" => "seller",
                    "success" => true,
                    "data" => $data
                ], 200);
            }

            if (!$seller) {
                $products = Product::with(["productCategory", "productStatus"])
                    ->where("product_status_id", "!=", 3)
                    ->where("product_status_id", "!=", 4)
                    ->get();
                $data = $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'product_name' => $product->product_name,
                        'product_category' => $product->productCategory == null ? "Kue" : $product->productCategory->product_category_name,
                        'product_slug' => $product->product_slug,
                        'product_price' => $product->product_price,
                        "product_composision" => $product->product_composision,
                        "product_description" => $product->product_description,
                        "product_status" => $product->productStatus->product_status_name,
                        "images" => $product->productImages->first() ? $product->productImages->first()->product_image_path : null,
                    ];
                });
                return response()->json([
                    "roles" => "buyer",
                    "success" => true,
                    "message" => "berhasil",
                    "data" => $data
                ], 200);
            }
        } catch (\Exception $e) {
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
        try {
            UserRoleUtil::sellerStrict();
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

            if ($request->hasFile("images")) {
                $images = $request->file("images");
                foreach ($images as $key => $image) {
                    $path = $image->store('images', 's3');
                    $url[] = Storage::disk('s3')->url($path);
                    ProductImage::create([
                        "user_id" => $user_id,
                        "product_id" => $product->id,
                        "product_image_path" => $url[$key]
                    ]);
                }
            }


            return response()->json([
                "success" => true,
                "message" => "Berhasil menambahkan product",
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $seller = UserRoleUtil::sellerRoles();
            $product = Product::with(["productCategory", "productImages"])->findOrFail($id);

            // Jika data telah dihapus maka data tidak valid
            if ($product->product_status_id == 4) {
                return throw new \Exception("data tidak valid");
            }
            // jika bukan use maka product yang status 3 / archive tidak dapat dilihat
            if (!$seller && $product->product_status_id == 3) {
                return throw new \Exception("Unauthorization");
            }

            $url = [];
            foreach ($product->productImages as $key => $productImage) {
                $url[$key]["id"] = $productImage->id;
                $url[$key]["url"] = $productImage->product_image_path;
            }
            $data = [
                "id" => $product->id,
                "product_name" => $product->product_name,
                "product_status" => $product->productStatus->product_status_name,
                'product_category' => $product->productCategory == null ? "Kue" : $product->productCategory->product_category_name,
                "product_slug" => $product->product_slug,
                "product_price" => $product->product_price,
                "product_composision" => $product->product_composision,
                "product_description" => $product->product_description,
                "created_at" => $product->created_at,
                "images" => $url
            ];
            return response()->json([
                "data" => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            UserRoleUtil::sellerStrict();
            $product = Product::findOrFail($id);
            $validatedData = $request->validate([
                "product_name" => "required|string|max:255|",
                "product_price" => "required|numeric",
                "product_composision" => "required|string|max:1000",
                "product_description" => "required|string|max:1000",
                "product_category_id" => "nullable|numeric",
                "product_status_id" => "numeric"
            ]);

            $product->update([
                "product_name" => $validatedData["product_name"],
                "product_price" => $validatedData["product_price"],
                "product_composision" => $validatedData["product_composision"],
                "product_description" => $validatedData["product_description"],
                "product_category_id" => $validatedData["product_category_id"],
                "product_status_id" => $validatedData["product_status_id"],
            ]);

            return response()->json([
                "success" => true,
                "message" => "data berhasil di update"
            ]);
            // $validatedData = 
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            UserRoleUtil::sellerStrict();
            $product = Product::findOrFail($id);
            $product->update([
                "product_status_id" => 4
            ]);
            return response()->json([
                "success" => true,
                "message" => "data berhasil di hapus"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
