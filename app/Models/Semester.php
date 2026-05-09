<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    // Ép kiểu dữ liệu (Casts) cho chuẩn định dạng của Laravel 11
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // Một Học kỳ có nhiều Lớp học phần được mở
    public function courseSections()
    {
        return $this->hasMany(CourseSection::class);
    }

    // Một Học kỳ có nhiều định mức Học phí (theo ngành)
    public function tuitionFees()
    {
        return $this->hasMany(TuitionFee::class);
    }

    // Một Học kỳ có nhiều Công nợ/Hóa đơn của sinh viên
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}