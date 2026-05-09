<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'major_id',
        'student_code',
        'dob',
        'phone',
        'address',
        'status',
    ];

    // Sinh viên thuộc về 1 User (Tài khoản)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Sinh viên thuộc về 1 Ngành học
    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    // Sinh viên có nhiều lượt đăng ký môn học
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}