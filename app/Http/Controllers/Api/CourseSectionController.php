<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseSection;
use Illuminate\Http\Request;

class CourseSectionController extends Controller
{
    public function index()
    {
        // Load môn học, giảng viên (kèm tên user), và học kỳ
        $sections = CourseSection::with(['subject', 'teacher.user', 'semester'])->get();
        return response()->json(['success' => true, 'data' => $sections], 200);
    }

    public function show(string $id)
    {
        // Load thêm danh sách sinh viên đã đăng ký vào lớp này
        $section = CourseSection::with(['subject', 'teacher.user', 'semester', 'enrollments.student.user'])->find($id);
        if (!$section) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy lớp học phần'], 404);
        }
        return response()->json(['success' => true, 'data' => $section], 200);
    }

    public function store(Request $request) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}