<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * API: GET /api/announcements
     * Lấy danh sách thông báo phù hợp với vai trò của người dùng
     */
    public function index(Request $request)
    {
        $role = $request->user()->role;

        // Lấy thông báo dành cho tất cả mọi người HOẶC đúng vai trò người đang đăng nhập
        $announcements = Announcement::with('user:id,name')
            ->whereIn('target_role', ['all', $role])
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $announcements]);
    }

    /**
     * API: POST /api/announcements (Dành cho Admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_role' => 'required|in:all,student,teacher'
        ]);

        $announcement = Announcement::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->input('content'),
            'target_role' => $request->target_role
        ]);

        return response()->json(['success' => true, 'message' => 'Đăng thông báo thành công!', 'data' => $announcement], 201);
    }
}