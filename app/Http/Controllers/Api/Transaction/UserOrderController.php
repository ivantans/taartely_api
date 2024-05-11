<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use App\Models\UserOrder;
use App\Utils\UserRoleUtil;
use Illuminate\Http\Request;

class UserOrderController extends Controller
{
    public function searchQuery($parameter){
        $seller = UserRoleUtil::sellerRoles();
        $user_id = auth()->user()->id;
        if(empty($parameter)){
            $data = $seller?UserOrder::all():UserOrder::where("user_id", $user_id)->get();
        } else {
            $data = $seller?UserOrder::where("order_status_id", $parameter)->get():
            UserOrder::where("order_status_id", $parameter)->where("user_id", $user_id)->get();
        }
        return $data;
    }
    public function index(Request $request)
    {
        try{
            $data = $this->searchQuery($request->query("status"));
            return response()->json([
                "success" => true,
                "data" => $data
            ],200);

        } catch (\Exception $e){
            return response()->json([
                "success" => false, 
                "message" => $e->getMessage()
            ],500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        UserRoleUtil::buyerStrict();
        
        $validatedData = $request->validate([
            "user_contact_id" => "required|numeric",
            "order_note" => "nullable|string|max:500",
            "order_due_date" => "required|date_format:Y-m-d H:i:s",
        ]);


        // id, user_id, user_contact_id, order_status_id, order_note, 
        // order_due_date, order_total_price, order_total_product, 
        // order_total_quantity, order_reason, created_at, updated_at
        // ! VALIDATED
        // ! user_contact_id, order_note, order_due_date, order_reason
        // ! NO VALIDATED
        // ! user_id, user_status_id, order_total_price, order_total_product, 
        // ! order_total_quantity, order_reason

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
        //
    }
}
