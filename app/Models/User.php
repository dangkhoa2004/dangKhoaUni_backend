<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject; // Thêm interface này

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable; // Đã loại bỏ HasApiTokens của Sanctum

    /**
     * Các trường có thể gán giá trị hàng loạt (Mass Assignable).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',       // Trường phân quyền (admin, teacher, student,...)
        'is_active',  // Trạng thái tài khoản
    ];

    /**
     * Các trường sẽ bị ẩn khi chuyển đổi sang JSON (Bảo mật).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Thiết lập kiểu dữ liệu cho các trường.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // =========================================================================
    // JWT METHODS (Bắt buộc phải có để JWT hoạt động)
    // =========================================================================

    /**
     * Lấy định danh lưu trữ trong phần 'sub' của JWT (thường là khóa chính).
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Trả về một mảng chứa các thông tin tùy chỉnh muốn lưu vào trong Token.
     * Đây là nơi bạn nhét 'role' để Frontend có thể giải mã mà không cần gọi API.
     */
    public function getJWTCustomClaims()
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'role'  => $this->role,
        ];
    }

    // =========================================================================
    // RELATIONSHIPS (Các hàm liên kết giữ nguyên từ file cũ của bạn)
    // =========================================================================

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }
}