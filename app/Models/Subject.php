<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'department_id',
        'code',
        'name',
        'credits',
        'prerequisite_id',
        'attachment',
    ];

    // Môn học thuộc về 1 Khoa
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Môn học có thể có 1 môn tiên quyết (cũng là Môn học)
    public function prerequisite()
    {
        return $this->belongsTo(Subject::class, 'prerequisite_id');
    }

    // Môn học có nhiều Lớp học phần được mở
    public function courseSections()
    {
        return $this->hasMany(CourseSection::class);
    }
}