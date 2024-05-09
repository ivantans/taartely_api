<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index(){
        try{
            $all_categories = ProductCategory::all();
    
            $data = $all_categories->map(function ($category){
                return [
                    "id" => $category->id,
                    "category" => $category->product_category_name,
                ];
            });
    
            return response()->json([
                "data" => $data
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request){
        try{

            $validatedData = $request->validate([
                "product_category_name" => "required|max:20|unique:product_categories,product_category_name",
            ]);
    
            $category = ProductCategory::create([
                "user_id" => auth()->user()->id,
                "product_category_name" => $validatedData["product_category_name"],
            ]);
        
            return response()->json([
                "success" => true,
                "data" => $category
            ], 200);

        } catch(\Illuminate\Validation\ValidationException $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], 422);

        } catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id){
        
    }

    public function update(Request $request, string $id){
        try{
            $category = ProductCategory::findOrFail($id);

            $validatedData = $request->validate([
                "product_category_name" => "required|max:20|unique:product_categories,product_category_name",
            ]);
    
    
            $category->product_category_name = $validatedData["product_category_name"];
    
            $category->save();
    
            return response()->json([
                "success" => true,
                "data" => $category
            ], 200);
            
        } catch(\Illuminate\Validation\ValidationException $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ],422);
        } catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ],500);
        } 
    }
    public function destroy(string $id){
        try{
            $category = ProductCategory::findOrFail($id);
            $category->delete();
            return response()->json([
                "success" => true,
                "message" => "data berhasil di hapus"
            ], 200);
        } catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], 500);
        }
    }
}
