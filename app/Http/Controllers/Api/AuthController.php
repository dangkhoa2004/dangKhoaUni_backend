<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        $user = User::where('email', $request->email)->first();

        // Kiểm tra sai email hoặc mật khẩu
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Email hoặc mật khẩu không chính xác'], 401);
        }

        // Kiểm tra tài khoản có bị admin khóa không
        if (!$user->is_active) {
            return response()->json(['success' => false, 'message' => 'Tài khoản của bạn đã bị khóa'], 403);
        }

        // Sinh ra Token bảo mật
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user // Trả về thông tin user (kèm role) để Frontend phân luồng giao diện
        ]);
    }

    /**
     * API: POST /api/logout
     */
    public function logout(Request $request)
    {
        // Xóa token hiện tại của user
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['success' => true, 'message' => 'Đã đăng xuất thành công']);
    }
}