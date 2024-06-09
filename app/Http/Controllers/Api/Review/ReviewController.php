<?php

namespace App\Http\Controllers\Api\Review;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductReviewImage;
use App\Models\UserOrder;
use App\Models\UserOrderDetail;
use App\Utils\UserRoleUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store(Request $request, $productId, $userOrderId)
    {
        try {
            UserRoleUtil::buyerStrict();
            if ($request->hasFile("images")) {
                $validator = Validator::make($request->all(), [
                    'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Contoh validasi: file gambar, maksimal 2MB
                ]);
                if ($validator->fails()) {
                    return throw new \Illuminate\Validation\ValidationException($validator);
                }
            }

            $validatedData = $request->validate([
                'product_review_rating' => 'required|integer|min:1|max:5',
                'product_review_comment' => 'required|string|max:500',
            ]);

            $user = auth()->user();
            // Pastikan user memiliki order dengan userOrderId yang valid dan status completed
            $userOrder = UserOrder::where('id', $userOrderId)
                ->where('user_id', $user->id)
                ->where('order_status_id', 4)
                ->firstOrFail();

            // Pastikan userOrder memiliki detail yang terkait dengan productId
            $userOrderDetail = $userOrder->userOrderDetails()
                ->where('product_id', $productId)
                ->first();

            if (!$userOrderDetail) {
                throw new \Exception('Anda belum membeli produk ini');
            }

            // Pastikan user belum memberikan review untuk produk ini
            $existingReview = ProductReview::where('user_order_detail_id', $userOrderDetail->id)
                ->where('product_id', $productId)
                ->exists();

            if ($existingReview) {
                throw new \Exception('Anda sudah memberikan ulasan untuk produk ini');
            }


            $product_review = ProductReview::create([
                "user_id" => $user->id,
                "product_id" => $productId,
                "user_order_detail_id" =>  $userOrderDetail->id,
                "product_review_comment" => $validatedData["product_review_comment"],
                "product_review_rating" => $validatedData["product_review_rating"],
            ]);

            if ($request->hasFile("images")) {
                $images = $request->file("images");
                foreach ($images as $key => $image) {
                    $path = $image->store('images', 's3');
                    $url[] = Storage::disk('s3')->url($path);
                    ProductReviewImage::create([
                        "user_id" => $user->id,
                        "product_review_id" => $product_review->id,
                        "product_review_image_path" => $url[$key]
                    ]);
                }
            }

            return response()->json([
                "success" => true,
                "message" => "Review berhasil ditambahkan",
                "id_rview" => $product_review->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function show($productId){
        try{
            $seller = UserRoleUtil::sellerRoles();
            $product = Product::findOrFail($productId);
            if($seller){
                $reviews = $product->productReviews()->get();
                return response()->json([
                    "success" => true,
                    "reviews" => $reviews
                ]);
            }
            // Ambil semua ulasan yang terkait dengan produk
            $reviews = $product->productReviews()->where('is_active', 1)->get();
            return response()->json([
                "success" => true,
                "reviews" => $reviews
            ]);
        } catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], 500);
        }   
    }

    public function hideReview($idReview){
        UserRoleUtil::sellerStrict();
        $review = productReview::findOrFail($idReview);
        
        $review->update([
            "is_active" => 0,
        ]);
    }

    public function showReview($idReview){
        try{
            UserRoleUtil::sellerStrict();
            $review = productReview::findOrFail($idReview);
            
            $review->update([
                "is_active" => 1,
            ]);
            return response()->json([
                "success" => true,
                "message" => "berhasil di tampilkan"
            ]);
        } catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e
            ]);
        }
        
    }
}
