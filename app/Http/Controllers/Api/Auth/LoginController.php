<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request){

        try {
            // Validasi data input
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|',
            ]);

            // Coba untuk melakukan autentikasi pengguna
            if (Auth::attempt($credentials)) {
                // Autentikasi berhasil, buat token API menggunakan Sanctum
                $user = Auth::user();
                $token = $user->createToken('API Token')->plainTextToken;
                // Kirim respons berhasil dengan token
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil',
                    'user' => $user,
                    'token' => $token
                ], 200);
            }

            // Autentikasi gagal, kirimkan respons kesalahan
            throw \Illuminate\Validation\ValidationException::withMessages([
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangani kesalahan validasi
            return response()->json([
                'success' => false,
                'message' => "Email atau passwod salah",
            ], 422);
        } catch (\Exception $e) {
            // Tangani kesalahan lainnya
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan login: ' . $e->getMessage()
            ], 500);
        }
    }
}
