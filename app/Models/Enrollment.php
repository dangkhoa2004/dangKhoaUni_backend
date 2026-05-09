<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'course_section_id',
        'status',
    ];

    // Lượt đăng ký thuộc về 1 Sinh viên
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Lượt đăng ký thuộc về 1 Lớp học phần
    public function courseSection()
    {
        return $this->belongsTo(CourseSection::class);
    }

    // Mỗi lượt đăng ký môn học có 1 bảng Điểm tương ứng
    public function grade()
    {
        return $this->hasOne(Grade::class);
    }
}