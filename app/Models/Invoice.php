<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'student_id',
        'semester_id',
        'total_amount',
        'paid_amount',
        'status',
    ];

    // Hóa đơn/Công nợ của 1 Sinh viên
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Hóa đơn tính cho 1 Học kỳ
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}