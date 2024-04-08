<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getUserProfile(){
        return response()->json([
            "user" => auth()->user()    
        ]);
    }

// TODO: Selesaikan updateUserProfile
    public function updateUserProfile(Request $request)
{
    // Validasi data masukan
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255', // Menetapkan aturan validasi untuk nama
    ]);

    // Cek validasi
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Dapatkan pengguna yang sedang terotentikasi
    $user = auth()->user();

    // Periksa dan update nama jika disediakan dalam permintaan
    if ($request->has('name')) {
        $user->name = $request->name; // Update nilai nama dengan nilai baru
    }

    // Simpan perubahan pada model pengguna
    $user->save();

    // Kirim respons JSON dengan pesan sukses
    return response()->json(['message' => 'Nama pengguna berhasil diperbarui']);
}
}
