<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Kiểm tra nếu chưa đăng nhập hoặc role không khớp với yêu cầu
        if (!$request->user() || $request->user()->role !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'Truy cập bị từ chối. Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        return $next($request);
    }
}