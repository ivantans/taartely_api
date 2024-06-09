<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Models\UserContact;
use App\Utils\UserRoleUtil;
use Illuminate\Http\Request;

class UserContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            UserRoleUtil::buyerStrict();
            $user_id = auth()->user()->id;
            $contacts = UserContact::where("user_id", $user_id)->where("is_active", 1)->get();
            return response()->json([
                "success" => true,
                "data" => $contacts
            ], 200);
        } catch(\Exception $e){
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
        try{
            UserRoleUtil::buyerStrict();
            $user_id = auth()->user()->id;
            $validatedData = $request->validate([
                "user_address" => "required|string|max:100",
                "user_phone_number" => "required|string|max:15"
            ]);

            $contacts = UserContact::create([
                "user_id" => $user_id,
                "user_address" => $validatedData["user_address"],
                "user_phone_number" => $validatedData["user_phone_number"],
                "is_active" => 1,
            ]);

            return response()->json([
                "success" => true,
                "message" => $contacts
            ], 201);
        } catch(\Exception $e){
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
        try{
            UserRoleUtil::buyerStrict();
            $user_id = auth()->user()->id;
            $contact = UserContact::findOrFail($id);
            if($contact->user_id != $user_id){
                throw new \Exception("Unauthorization");
            }
            return response()->json([
                "success" => true,
                "message" => $contact
            ], 200);
        } catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ],500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            UserRoleUtil::buyerStrict();
            $user_id = auth()->user()->id;
            $contact = UserContact::findOrFail($id);
            $validatedData = $request->validate([
                "user_address" => "required|string|max:100",
                "user_phone_number" => "required|string|max:15"
            ]);
            if($contact->user_id != $user_id){
                throw new \Exception("Unauthorization");
            }
            $contact->update([
                "user_address" => $validatedData["user_address"],
                "user_phone_number" => $validatedData["user_phone_number"],
            ]);

            return response()->json([
                "success" => true,
                "message" => $contact
            ], 200);
        } catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            UserRoleUtil::buyerStrict();
            $user_id = auth()->user()->id;
            $contact = UserContact::findOrFail($id);
            if($contact->user_id != $user_id){
                throw new \Exception("Unauthorization");
            }
            $contact->update([
                "is_active" => 0,
            ]);
            return response()->json([
                "success" => true,
                "message" => "Data berhasil dihapus"
            ], 204);
        } catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ],500);
        }
    }
}
