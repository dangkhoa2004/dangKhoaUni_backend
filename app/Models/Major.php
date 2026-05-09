<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $fillable = [
        'department_id',
        'code',
        'name',
    ];

    // Một Ngành thuộc về một Khoa
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Một Ngành có nhiều Sinh viên
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // Một Ngành có nhiều định mức Học phí (theo từng kỳ)
    public function tuitionFees()
    {
        return $this->hasMany(TuitionFee::class);
    }
}