<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    // Một Khoa có nhiều Ngành học
    public function majors()
    {
        return $this->hasMany(Major::class);
    }

    // Một Khoa có nhiều Giảng viên
    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    // Một Khoa quản lý nhiều Môn học
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}