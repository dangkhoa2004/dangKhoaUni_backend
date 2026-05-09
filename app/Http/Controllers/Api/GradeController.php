<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * API: PUT /api/grades/{enrollment_id}
     * Chức năng: Giảng viên cập nhật điểm cho sinh viên
     */
    public function update(Request $request, string $enrollment_id)
    {
        // 1. Validate điểm nhập vào phải từ 0 đến 10
        $request->validate([
            'attendance_score' => 'nullable|numeric|min:0|max:10',
            'midterm_score' => 'nullable|numeric|min:0|max:10',
            'final_score' => 'nullable|numeric|min:0|max:10',
        ]);

        $grade = Grade::where('enrollment_id', $enrollment_id)->first();

        if (!$grade) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bảng điểm'], 404);
        }

        // 2. Cập nhật các đầu điểm
        $grade->attendance_score = $request->attendance_score ?? $grade->attendance_score;
        $grade->midterm_score = $request->midterm_score ?? $grade->midterm_score;
        $grade->final_score = $request->final_score ?? $grade->final_score;

        // 3. Tự động tính điểm tổng kết (Nếu đã có đủ 3 đầu điểm)
        if ($grade->attendance_score !== null && $grade->midterm_score !== null && $grade->final_score !== null) {
            // Trọng số: CC 10%, GK 30%, CK 60%
            $total = ($grade->attendance_score * 0.1) + ($grade->midterm_score * 0.3) + ($grade->final_score * 0.6);
            $grade->total_score = round($total, 2); // Làm tròn 2 chữ số thập phân
        }

        $grade->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật điểm thành công',
            'data' => $grade
        ], 200);
    }
}