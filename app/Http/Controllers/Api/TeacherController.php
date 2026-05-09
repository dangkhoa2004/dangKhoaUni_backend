<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class TeacherController extends Controller
{
    public function index()
    {
        // Lấy giảng viên kèm thông tin tài khoản và khoa
        $teachers = Teacher::with(['user', 'department'])->get();
        return response()->json(['success' => true, 'data' => $teachers], 200);
    }

    public function show(string $id)
    {
        // Xem chi tiết giảng viên kèm danh sách lớp đang dạy
        $teacher = Teacher::with(['user', 'department', 'courseSections.subject'])->find($id);

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy giảng viên'], 404);
        }
        return response()->json(['success' => true, 'data' => $teacher], 200);
    }

    /**
     * API: POST /api/teachers
     * Thêm mới Giảng viên
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
            'department_id' => 'required|exists:departments,id',
            'teacher_code' => 'required|string|unique:teachers,teacher_code',
            'degree' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'teacher',
                'is_active' => true,
            ]);

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'teacher_code' => $request->teacher_code,
                'degree' => $request->degree,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Thêm giảng viên thành công!', 'data' => $teacher], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: PUT /api/teachers/{id}
     * Cập nhật Giảng viên
     */
    public function update(Request $request, string $id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher)
            return response()->json(['success' => false, 'message' => 'Không tìm thấy giảng viên'], 404);

        $user = $teacher->user;

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|unique:users,email,' . $user->id,
            'department_id' => 'sometimes|exists:departments,id',
            'teacher_code' => 'sometimes|string|unique:teachers,teacher_code,' . $teacher->id,
        ]);

        DB::beginTransaction();
        try {
            if ($request->has('name'))
                $user->name = $request->name;
            if ($request->has('email'))
                $user->email = $request->email;
            if ($request->has('password'))
                $user->password = Hash::make($request->password);
            $user->save();

            $teacher->update($request->only(['department_id', 'teacher_code', 'degree']));

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cập nhật thành công!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: DELETE /api/teachers/{id}
     */
    public function destroy(string $id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher)
            return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);

        $teacher->user->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa giảng viên']);
    }
}