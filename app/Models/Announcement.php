<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'target_role',
    ];

    // Thông báo do 1 User (thường là Admin) đăng
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}