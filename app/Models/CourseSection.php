<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    protected $fillable = [
        'subject_id',
        'teacher_id',
        'semester_id',
        'room',
        'schedule_time',
        'type_class',
    ];

    // Lớp học phần thuộc về 1 Môn học
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Lớp học phần do 1 Giảng viên dạy
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // Lớp học phần thuộc về 1 Học kỳ
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    // Lớp học phần có nhiều lượt Đăng ký của sinh viên
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}