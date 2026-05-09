<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'teacher_code',
        'degree',
    ];

    // Giảng viên thuộc về 1 User (Tài khoản)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Giảng viên thuộc về 1 Khoa
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Giảng viên dạy nhiều Lớp học phần
    public function courseSections()
    {
        return $this->hasMany(CourseSection::class);
    }
}