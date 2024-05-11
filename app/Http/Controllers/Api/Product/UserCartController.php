<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\UserCart;
use App\Models\UserCartDetail;
use App\Utils\UserRoleUtil;
use Illuminate\Http\Request;

class UserCartController extends Controller
{
    public function createUserCartIfNotExist(){
        $user_id = auth()->user()->id;
        $cart = UserCart::where("user_id", $user_id)->first(); 
        if (!$cart) {
            $cart = UserCart::create([
                'user_id' => $user_id,
                "total_quantity" => 0,
                "total_product" => 0,
                "total_price" => 0,
            ]);
        }
    }

    public function updateCartUser(){
        $user_id = auth()->user()->id;
        $cart = UserCart::where("user_id", $user_id)->first(); 

        $total_product = $cart->userCartDetails->count();
        $total_quantity = 0;
        $total_price = 0;
        foreach($cart->userCartDetails as $userCartDetail){
            $total_quantity = $total_quantity + $userCartDetail->cart_detail_quantity;
            $total_price += ($userCartDetail->product->product_price * $userCartDetail->cart_detail_quantity);
        }

        $cart->update([
            "total_product" => $total_product,
            "total_quantity" => $total_quantity,
            "total_price" => $total_price,
        ]);
    }

    public function productValidation($id){
        $product = Product::findOrFail($id);
        if($product->product_status_id != 1){
            throw new \Exception("Product tidak valid");
        }
    }

    public function deleteProductIfNotValid(){
        $user_id = auth()->user()->id;
        $cart = UserCart::where('user_id', $user_id)->first();

        foreach($cart->userCartDetails as $userCartDetail){
            if($userCartDetail->product_status_id != 1){
                $userCartDetail->delete();
            }
        }
        $this->updateCartUser();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            UserRoleUtil::buyerStrict();
            $this->createUserCartIfNotExist();
            $this->deleteProductIfNotValid();
            $user_id = auth()->user()->id;
            $cart = UserCart::where('user_id', $user_id)->first();
            if($cart->user_id != $user_id){
                return throw new \Exception("Unauthorization");
            }
            $cartDetails = $cart->userCartDetails->map(function ($userCartDetail){
                return [
                    "id" => $userCartDetail->id,
                    "product_id" => $userCartDetail->product->id,
                    "product_name" => $userCartDetail->product->product_name,
                    "product_price" => $userCartDetail->product->product_price,
                    "cart_detail_quantity" => $userCartDetail->cart_detail_quantity,
                ];
            });
            return response()->json([
                "success" => true,
                "total_product" => $cart->total_product,
                "total_quantity" => $cart->total_quantity,
                "total_price" => $cart->total_price,
                "data" => $cartDetails
            ]);
        } catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            UserRoleUtil::buyerStrict();
            $this->createUserCartIfNotExist();
            // Validasi request
            $validatedData = $request->validate([
                "cart_detail_quantity" => "required|numeric|min:1",
                "product_id" => "required|numeric",
            ]);

            $this->productValidation($validatedData["product_id"]);

            $user_id = auth()->user()->id;
            $cart = UserCart::where("user_id", $user_id)->first(); 

            $user_cart_id = $cart->id;
            
            $existingCartDetail = UserCartDetail::where("user_cart_id", $user_cart_id)
                                ->where('product_id', $request->product_id)
                                ->first();
            if($existingCartDetail){
                $existingCartDetail->update([
                    'cart_detail_quantity' => $existingCartDetail->cart_detail_quantity + $validatedData["cart_detail_quantity"]
                ]);
            } else{
                UserCartDetail::create([
                    "user_cart_id" => $user_cart_id,
                    "product_id" => $request->product_id,
                    "cart_detail_quantity" => $validatedData["cart_detail_quantity"],
                ]);
            }
            $this->updateCartUser();

            $cart = UserCart::where("user_id", $user_id)->first(); 
            $cartDetails = $cart->userCartDetails->map(function ($userCartDetail){
                return [
                    "id" => $userCartDetail->id,
                    "product_name" => $userCartDetail->product->product_name,
                    "product_price" => $userCartDetail->product->product_price,
                    "cart_detail_quantity" => $userCartDetail->cart_detail_quantity,
                    "total_price" => $userCartDetail->product->product_price * $userCartDetail->cart_detail_quantity,
                ];
            });
            return response()->json([
                "success" => true,
                "cart_id" => $cart->id,
                "total_product" => $cart->total_product,    
                "total_price" => $cart->total_price,    
                "total_quantity" => $cart->total_quantity,    
                "data" => $cartDetails,    
            ]);

        } catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

           
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            UserRoleUtil::buyerStrict();
            $userCartDetail = UserCartDetail::findOrFail($id);
            if($userCartDetail->userCart->user_id != auth()->user()->id){
                throw new \Exception("Unauthorization");
            }    
    
            $validatedData = $request->validate([
                "cart_detail_quantity" => "required|numeric"
            ]);
            $userCartDetail->update([
                "cart_detail_quantity" => $validatedData["cart_detail_quantity"],
            ]);
            $this->updateCartUser();
            return response()->json([
                "success" => true,
                "message" => "Berhasil menambahkan ke keranjang"
            ],200);
        } catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            UserRoleUtil::buyerStrict();
            $userCartDetail = UserCartDetail::findOrFail($id);
            if($userCartDetail->userCart->user_id != auth()->user()->id){
                throw new \Exception("Unauthorization");
            }    
            $userCartDetail->delete();

            $this->updateCartUser();
            return response()->json([
                "success" => true,
                "message" => "Berhasil dihapus"
            ],200);
        } catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
