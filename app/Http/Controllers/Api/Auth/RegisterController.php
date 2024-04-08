<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request){

        try {
            // Validasi data input
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            // Buat pengguna baru
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Jika berhasil, kirimkan respons dengan token
            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'user' => $user,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Jika validasi gagal, kirimkan respons kesalahan validasi
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal: ' . $e->getMessage(),
                'errors' => $e->validator->errors()
            ], 422);

        } catch (\Exception $e) {
            // Tangani kesalahan lainnya
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan registrasi: ' . $e->getMessage()
            ], 500);
        }
    }


}
