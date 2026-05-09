<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class StudentController extends Controller
{
    /**
     * API: GET /api/students
     * Lấy danh sách toàn bộ sinh viên
     */
    public function index()
    {
        // Lấy sinh viên kèm theo thông tin User và Major
        $students = Student::with(['user', 'major'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách sinh viên thành công',
            'data' => $students
        ], 200);
    }

    /**
     * API: GET /api/students/{id}
     * Lấy chi tiết 1 sinh viên cụ thể
     */
    public function show(string $id)
    {
        // Tìm sinh viên theo ID, kèm theo thông tin User, Major và Lịch sử đăng ký môn học
        $student = Student::with(['user', 'major', 'enrollments.courseSection.subject'])->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hồ sơ sinh viên'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ], 200);
    }

    /**
     * API: POST /api/students
     * Thêm mới Sinh viên (Tạo User -> Tạo Student)
     */
    public function store(Request $request)
    {
        // 1. Kiểm tra dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
            'major_id' => 'required|exists:majors,id',
            'student_code' => 'required|string|unique:students,student_code',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // 2. Tạo User trước
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'is_active' => true,
            ]);

            // 3. Tạo Student và liên kết với User ID vừa tạo
            $student = Student::create([
                'user_id' => $user->id,
                'major_id' => $request->major_id,
                'student_code' => $request->student_code,
                'dob' => $request->dob,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => 'studying',
            ]);

            DB::commit(); // Lưu vĩnh viễn vào Database

            return response()->json(['success' => true, 'message' => 'Thêm sinh viên thành công!', 'data' => $student], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Hủy bỏ thao tác nếu có lỗi
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: PUT /api/students/{id}
     * Cập nhật thông tin Sinh viên
     */
    public function update(Request $request, string $id)
    {
        $student = Student::find($id);
        if (!$student)
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sinh viên'], 404);

        $user = $student->user; // Lấy thông tin tài khoản của SV này

        // Validate (Bỏ qua kiểm tra unique cho chính ID hiện tại)
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|unique:users,email,' . $user->id,
            'major_id' => 'sometimes|exists:majors,id',
            'student_code' => 'sometimes|string|unique:students,student_code,' . $student->id,
        ]);

        DB::beginTransaction();
        try {
            // Cập nhật User
            if ($request->has('name'))
                $user->name = $request->name;
            if ($request->has('email'))
                $user->email = $request->email;
            if ($request->has('password'))
                $user->password = Hash::make($request->password);
            $user->save();

            // Cập nhật Student
            $student->update($request->only(['major_id', 'student_code', 'dob', 'phone', 'address', 'status']));

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cập nhật thành công!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: DELETE /api/students/{id}
     * Xóa Sinh viên (Thực chất là xóa User, hệ thống sẽ tự cascade xóa Student)
     */
    public function destroy(string $id)
    {
        $student = Student::find($id);
        if (!$student)
            return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);

        // Xóa User chứa sinh viên đó. 
        // Vì trong Migration ta đã set `onDelete('cascade')`, bảng Student và các bảng phụ sẽ tự động bị xóa theo.
        $student->user->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa sinh viên và tài khoản liên quan']);
    }

}