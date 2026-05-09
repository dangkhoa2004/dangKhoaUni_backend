<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Thêm dòng này
use App\Models\User;

class AuthController extends Controller
{
    /**
     * API: POST /api/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        // Sử dụng Auth::guard('api') để PHP Intelisense và Laravel hiểu rõ là dùng JWT
        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            return response()->json([
                'success' => false, 
                'message' => 'Email hoặc mật khẩu không chính xác'
            ], 401);
        }

        $user = Auth::guard('api')->user();

        // Kiểm tra trạng thái hoạt động
        if (!$user->is_active) {
            return response()->json([
                'success' => false, 
                'message' => 'Tài khoản của bạn đã bị khóa'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }

    /**
     * API: POST /api/logout
     */
    public function logout()
    {
        Auth::guard('api')->logout();
        
        return response()->json([
            'success' => true, 
            'message' => 'Đã đăng xuất thành công'
        ]);
    }
}