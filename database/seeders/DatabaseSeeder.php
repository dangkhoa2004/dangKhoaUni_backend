<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\Major;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Teacher;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tạo tài khoản Admin
        User::create([
            'name' => 'Quản trị viên Hệ thống',
            'email' => 'admin@dangkhoa.uni',
            'password' => Hash::make('password123'), // Mật khẩu chung là: password123
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 2. Tạo Danh mục cơ bản (Khoa, Ngành, Học kỳ)
        $department = Department::create([
            'code' => 'CNTT',
            'name' => 'Công nghệ thông tin',
        ]);

        $major = Major::create([
            'department_id' => $department->id,
            'code' => 'KTPM',
            'name' => 'Kỹ thuật phần mềm',
        ]);

        Semester::create([
            'name' => 'Học kỳ 1 - 2026',
            'start_date' => '2026-09-05',
            'end_date' => '2027-01-15',
            'is_active' => true,
        ]);

        // 3. Tạo 1 Giảng viên (Gồm tài khoản User + Hồ sơ Teacher)
        $teacherUser = User::create([
            'name' => 'Nguyễn Văn Giảng Viên',
            'email' => 'teacher@dangkhoa.uni',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
            'is_active' => true,
        ]);

        Teacher::create([
            'user_id' => $teacherUser->id,
            'department_id' => $department->id,
            'teacher_code' => 'GV001',
            'degree' => 'Thạc sĩ',
        ]);

        // 4. Tạo 1 Sinh viên (Gồm tài khoản User + Hồ sơ Student)
        $studentUser = User::create([
            'name' => 'Trần Thị Sinh Viên',
            'email' => 'student@dangkhoa.uni',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_active' => true,
        ]);

        Student::create([
            'user_id' => $studentUser->id,
            'major_id' => $major->id,
            'student_code' => 'SV001',
            'dob' => '2005-10-15',
            'phone' => '0987654321',
            'address' => 'Hà Nội',
            'status' => 'studying',
        ]);
    }
}