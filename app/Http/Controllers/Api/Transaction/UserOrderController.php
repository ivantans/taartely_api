<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\UserCart;
use App\Models\UserContact;
use App\Models\UserOrder;
use App\Models\UserOrderDetail;
use App\Utils\UserRoleUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class UserOrderController extends Controller
{
    public function checkPaymentStatus()
    {
        $user_orders = UserOrder::where("order_status_id", 2)->get();
        foreach ($user_orders as $user_order) {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic U0ItTWlkLXNlcnZlci1YQUZYVlA4UTI0UDJranYwM0E2dzZuM2Y6'
            ])->get("https://api.sandbox.midtrans.com/v1/payment-links/xyz111-" . $user_order->id);
            $data = $response->json();
            $transaction_status = $data["last_snap_transaction_status"];

            if ($transaction_status == "EXPIRE") {
                $user_order->update([
                    "order_status_id" => 7,
                    "payment_link_url" => ""
                ]);
            }
            if ($transaction_status == "SETTLEMENT") {
                $user_order->update([
                    "order_status_id" => 3,
                    "payment_link_url" => ""
                ]);
            }
        }


    }
    public function validateUserContact($user_contact_id)
    {
        $user_id = auth()->user()->id;
        $user_contact = UserContact::findOrFail($user_contact_id);
        if ($user_contact->user_id != $user_id) {
            throw new \Exception("user contact tidak valid");
        }
    }
    public function searchQuery($parameter)
    {
        $seller = UserRoleUtil::sellerRoles();
        $user_id = auth()->user()->id;

        if (empty($parameter)) {
            if ($seller) {
                $data = UserOrder::with(['orderStatus', 'userOrderDetails'])->get();
            } else {
                $data = UserOrder::where('user_id', $user_id)->with(['orderStatus', 'userOrderDetails'])->get();
            }
        } else {
            if ($seller) {
                $data = UserOrder::where('order_status_id', $parameter)->with(['orderStatus', 'userOrderDetails'])->get();
            } else {
                $data = UserOrder::where('order_status_id', $parameter)->where('user_id', $user_id)->with(['orderStatus', 'userOrderDetails'])->get();
            }
        }

        return $data;
    }
    public function index(Request $request)
    {
        try {
            $this->checkPaymentStatus();
            $data = $this->searchQuery($request->query('status'));

            // Transform the data to include order_status_name and orderDetails
            $transformedData = $data->map(function ($order) {
                return [
                    'id' => $order->id,
                    'user_id' => $order->user_id,
                    'user_contact_id' => $order->user_contact_id,
                    'order_status_id' => $order->order_status_id,
                    'order_contact_name' => $order->userContact->name,
                    'order_status_name' => $order->orderStatus->order_status_name ?? null, // Include order_status_name
                    'order_note' => $order->order_note,
                    'payment_link_url' => $order->payment_link_url,
                    'order_due_date' => $order->order_due_date,
                    'order_total_price' => $order->order_total_price,
                    'order_total_product' => $order->order_total_product,
                    'order_total_quantity' => $order->order_total_quantity,
                    'order_reason' => $order->order_reason,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                    'order_details' => $order->userOrderDetails->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'product_id' => $detail->product->id,
                            'product_name' => $detail->product->product_name,
                            'product_price' => $detail->product->product_price,
                            'quantity' => $detail->order_detail_quantity,
                        ];
                    }),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function show(string $id)
    {
        try {
            $order = UserOrder::findOrFail($id);
            $orderDetails = [];
            foreach ($order->userOrderDetails as $userOrderDetail) {
                $orderDetails[] = [
                    "product_name" => $userOrderDetail->product->product_name,
                    "order_detail_quantity" => $userOrderDetail->order_detail_quantity,
                ];
            }

            $responseData = [
                "order_id" => $order->id,
                "order_status_name" => $order->orderStatus->order_status_name,
                'order_note' => $order->order_note,
                'payment_link_url' => $order->payment_link_url,
                'order_due_date' => $order->order_due_date,
                'order_total_price' => $order->order_total_price,
                'order_total_product' => $order->order_total_product,
                'order_total_quantity' => $order->order_total_quantity,
                'order_reason' => $order->order_reason,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                "buyer_name" => $order->userContact->name,
                "user_address" => $order->userContact->user_address,
                "user_phone_number" => $order->userContact->user_phone_number,
                "order_details" => $orderDetails,
            ];

            return response()->json([
                "success" => true,
                "data" => $responseData
            ]);
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
            UserRoleUtil::buyerStrict();

            $validatedData = $request->validate([
                "user_contact_id" => "required|numeric",
                "order_note" => "nullable|string|max:500",
                "order_due_date" => "required|date|after:today",
            ]);

            $this->validateUserContact($validatedData["user_contact_id"]);
            $user_id = auth()->user()->id;
            $userCart = UserCart::where("user_id", $user_id)->first();
            if ($userCart->userCartDetails->count() == 0) {
                throw new \Exception("Masukkan setidaknya 1 barang");
            }
            $user_order = UserOrder::create([
                "user_id" => $user_id,
                "user_contact_id" => $validatedData["user_contact_id"],
                "order_status_id" => 1,
                "order_note" => $validatedData["order_note"],
                "order_due_date" => $validatedData["order_due_date"],
                "order_total_price" => $userCart->total_price,
                "order_total_product" => $userCart->total_product,
                "order_total_quantity" => $userCart->total_quantity,
                "order_reason" => "",
                "payment_link_url" => "",
            ]);

            $user_order_id = $user_order->id;
            foreach ($userCart->userCartDetails as $userCartDetail) {
                UserOrderDetail::create([
                    "user_order_id" => $user_order_id,
                    "product_id" => $userCartDetail->product->id,
                    "order_detail_quantity" => $userCartDetail->cart_detail_quantity,
                ]);
                $userCartDetail->delete();
            }

            $userCart->delete();

            return response()->json([
                "success" => true,
                "data" => $user_order
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }

    }

    public function approve($id)
    {
        try {
            UserRoleUtil::sellerStrict();
            $order = UserOrder::findOrFail($id);

            if ($order->order_status_id != 1) {
                return throw new \Exception("Order harus belum diterima");
            }

            $order->update([
                "order_status_id" => 2,
            ]);

            $buyer_id = $order->user_id;
            $currentDateTime = Carbon::now()->format('Y-m-d H:i:s O');
            $itemDetails = [];
            foreach ($order->userOrderDetails as $userOrderDetail) {
                $itemDetails[] = [
                    'id' => $userOrderDetail->product->id,
                    'name' => $userOrderDetail->product->product_name,
                    'price' => $userOrderDetail->product->product_price,
                    'quantity' => $userOrderDetail->order_detail_quantity,
                    'brand' => 'Taartely',
                    'category' => $userOrderDetail->product->productCategory->product_category_name,
                ];
            }
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => env("AUTHORIZATION"),
            ])->post('https://api.sandbox.midtrans.com/v1/payment-links', [
                        'transaction_details' => [
                            'order_id' => "xyz111-" . $order->id,
                            'gross_amount' => (int) $order->order_total_price,
                        ],
                        'customer_required' => true,
                        'usage_limit' => 1,
                        'expiry' => [
                            "start_time" => $currentDateTime,
                            'duration' => 15,
                            'unit' => 'minutes'
                        ],
                        "page_expiry" => [
                            "duration" => 3,
                            "unit" => "hours"
                        ],
                        'customer_details' => [
                            'first_name' => $order->user->name,
                            'email' => $order->user->email,
                            'phone' => $order->userContact->user_phone_number,
                            "address" => $order->userContact->user_address,
                            'notes' => 'Thank you for your purchase. Please follow the instructions to pay.',
                            'customer_details_required_fields' => [
                                'first_name',
                                'phone',
                                'email'
                            ]
                        ],
                        'item_details' => $itemDetails,
                    ]);

            $status = $response->status();
            if ($status == 200) {
                $data = $response->json();
                $payment_link_url = $data["payment_url"];
                $order->update([
                    "payment_link_url" => $payment_link_url
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "product berhasil diupdate",
                "data" => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }

    public function complete($id)
    {
        try {
            UserRoleUtil::sellerStrict();
            $order = UserOrder::findOrFail($id);
            if ($order->order_status_id != 3) {
                throw new \Exception("Data harus sedang diproses");
            }
            $order->update([
                "order_status_id" => "4"
            ]);
            return response()->json([
                "success" => true,
                "message" => "data berhasil diupdate"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
    public function cancel($id)
    {
        try {
            $seller = UserRoleUtil::sellerRoles();
            $order = UserOrder::findOrFail($id);
            if ($order->order_status_id >= 4) {
                throw new \Exception("data tidak dapat di cancel");
            }
            if ($seller) {
                $order->update([
                    "order_status_id" => "6"
                ]);
            } else {
                $order->update([
                    "order_status_id" => "5"
                ]);
            }
            return response()->json([
                "success" => true,
                "message" => "data berhasil dicancel"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

}
