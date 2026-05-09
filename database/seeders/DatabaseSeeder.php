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
use App\Models\Subject;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\TuitionFee;
use App\Models\Invoice;
use App\Models\Announcement;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. TÀI KHOẢN QUẢN TRỊ ---
        User::create([
            'name' => 'Quản trị viên',
            'email' => 'admin@dangkhoa.uni',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // --- 2. KHOA VÀ NGÀNH HỌC ---
        $cntt = Department::create(['code' => 'CNTT', 'name' => 'Công nghệ thông tin']);
        $kt = Department::create(['code' => 'KT', 'name' => 'Kinh tế']);

        $ktpm = Major::create(['department_id' => $cntt->id, 'code' => 'KTPM', 'name' => 'Kỹ thuật phần mềm']);
        $khmt = Major::create(['department_id' => $cntt->id, 'code' => 'KHMT', 'name' => 'Khoa học máy tính']);
        $qtkd = Major::create(['department_id' => $kt->id, 'code' => 'QTKD', 'name' => 'Quản trị kinh doanh']);

        // --- 3. HỌC KỲ ---
        $hk1 = Semester::create([
            'name' => 'Học kỳ 1 - 2026',
            'start_date' => '2026-09-05',
            'end_date' => '2027-01-15',
            'is_active' => true,
        ]);

        // --- 4. GIẢNG VIÊN ---
        $teachersData = [
            ['name' => 'Nguyễn Văn A', 'email' => 'teacher1@dangkhoa.uni', 'code' => 'GV001', 'dep' => $cntt->id],
            ['name' => 'Trần Thị B', 'email' => 'teacher2@dangkhoa.uni', 'code' => 'GV002', 'dep' => $cntt->id],
            ['name' => 'Lê Văn C', 'email' => 'teacher3@dangkhoa.uni', 'code' => 'GV003', 'dep' => $kt->id],
        ];

        $teachers = [];
        foreach ($teachersData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'role' => 'teacher',
            ]);
            $teachers[] = Teacher::create([
                'user_id' => $user->id,
                'department_id' => $data['dep'],
                'teacher_code' => $data['code'],
                'degree' => 'Thạc sĩ',
            ]);
        }

        // --- 5. SINH VIÊN ---
        $students = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Sinh viên $i",
                'email' => "student$i@dangkhoa.uni",
                'password' => Hash::make('password123'),
                'role' => 'student',
            ]);
            $students[] = Student::create([
                'user_id' => $user->id,
                'major_id' => $ktpm->id,
                'student_code' => "SV00$i",
                'dob' => '2005-01-01',
                'phone' => "090000000$i",
                'address' => 'Hà Nội',
                'status' => 'studying',
            ]);
        }

        // --- 6. MÔN HỌC ---
        $sj1 = Subject::create(['department_id' => $cntt->id, 'code' => 'CSDL', 'name' => 'Cơ sở dữ liệu', 'credits' => 3]);
        $sj2 = Subject::create(['department_id' => $cntt->id, 'code' => 'LTW', 'name' => 'Lập trình Web', 'credits' => 4, 'prerequisite_id' => $sj1->id]);
        $sj3 = Subject::create(['department_id' => $kt->id, 'code' => 'KTVM', 'name' => 'Kinh tế vĩ mô', 'credits' => 3]);

        // --- 7. HỌC PHÍ (Tuition Fees) ---
        TuitionFee::create([
            'major_id' => $ktpm->id,
            'semester_id' => $hk1->id,
            'cost_per_credit' => 500000.00
        ]);

        // --- 8. LỚP HỌC PHẦN (Course Sections) ---
        $section1 = CourseSection::create([
            'subject_id' => $sj1->id,
            'teacher_id' => $teachers[0]->id,
            'semester_id' => $hk1->id,
            'room' => 'Phòng A101',
            'schedule_time' => 'Thứ 2 (7:00 - 9:30)',
            'type_class' => 'Trực tiếp'
        ]);

        $section2 = CourseSection::create([
            'subject_id' => $sj2->id,
            'teacher_id' => $teachers[1]->id,
            'semester_id' => $hk1->id,
            'room' => 'Google Meet',
            'schedule_time' => 'Thứ 4 (13:00 - 15:30)',
            'type_class' => 'Online'
        ]);

        // --- 9. ĐĂNG KÝ HỌC & ĐIỂM SỐ ---
        foreach ($students as $student) {
            // Đăng ký lớp 1
            $enroll1 = Enrollment::create([
                'student_id' => $student->id,
                'course_section_id' => $section1->id,
                'status' => 'approved'
            ]);

            // Tạo điểm mẫu cho lớp 1
            Grade::create([
                'enrollment_id' => $enroll1->id,
                'attendance_score' => 10,
                'midterm_score' => 8.5,
                'final_score' => 7.0,
                'total_score' => 7.8 // Giả sử công thức tính
            ]);

            // --- 10. HÓA ĐƠN (Invoices) ---
            Invoice::create([
                'student_id' => $student->id,
                'semester_id' => $hk1->id,
                'total_amount' => 1500000.00, // 3 tín chỉ * 500k
                'paid_amount' => 0,
                'status' => 'unpaid'
            ]);
        }

        // --- 11. THÔNG BÁO ---
        Announcement::create([
            'user_id' => 1, // Admin
            'title' => 'Chào mừng năm học mới 2026',
            'content' => 'Chúc các bạn sinh viên một học kỳ mới đạt kết quả cao.',
            'target_role' => 'all'
        ]);

        Announcement::create([
            'user_id' => 1,
            'title' => 'Họp hội đồng sư phạm',
            'content' => 'Yêu cầu toàn thể giảng viên có mặt đúng giờ.',
            'target_role' => 'teacher'
        ]);
    }
}