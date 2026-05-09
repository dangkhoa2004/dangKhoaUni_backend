<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuitionFee extends Model
{
    protected $fillable = [
        'major_id',
        'semester_id',
        'cost_per_credit',
    ];

    // Học phí áp dụng cho 1 Ngành học
    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    // Học phí áp dụng trong 1 Học kỳ
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}