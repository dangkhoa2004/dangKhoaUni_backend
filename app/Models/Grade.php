<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'enrollment_id',
        'attendance_score',
        'midterm_score',
        'final_score',
        'total_score',
    ];

    // Điểm số thuộc về 1 lượt Đăng ký học
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
}