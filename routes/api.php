<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- IMPORT TẤT CẢ CONTROLLERS ---
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\CourseSectionController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\AnnouncementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. PUBLIC ROUTES (Không cần đăng nhập)
Route::post('/login', [AuthController::class, 'login'])->name('login');

// 2. PROTECTED ROUTES (Bắt buộc phải có Token Bearer)
Route::middleware('auth:sanctum')->group(function () {

    // --- Tài khoản & Cá nhân ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    // --- Thông báo (Xem chung cho tất cả user đã đăng nhập) ---
    Route::get('/announcements', [AnnouncementController::class, 'index']);


    // ---------------------------------------------------
    // NHÓM QUYỀN: QUẢN TRỊ VIÊN (ADMIN)
    // ---------------------------------------------------
    Route::middleware('role:admin')->group(function () {
        // Quản lý nhân sự & danh mục
        Route::apiResource('students', StudentController::class);
        Route::apiResource('teachers', TeacherController::class);
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('course-sections', CourseSectionController::class);

        // Quản lý tài chính (Ghi nhận nộp tiền)
        Route::post('invoices/{id}/pay', [InvoiceController::class, 'pay']);

        // Đăng thông báo mới
        Route::post('/announcements', [AnnouncementController::class, 'store']);
    });


    // ---------------------------------------------------
    // NHÓM QUYỀN: GIẢNG VIÊN (TEACHER)
    // ---------------------------------------------------
    Route::middleware('role:teacher')->group(function () {
        // Nhập điểm cho sinh viên
        Route::put('/grades/{enrollment_id}', [GradeController::class, 'update']);

        // Upload tài liệu cho môn học
        Route::post('/subjects/{id}/upload-document', [SubjectController::class, 'update']);

        // API xem danh sách lớp học mình được phân công (Ví dụ mở rộng)
        Route::get('/teacher/my-sections', function (Request $request) {
            return $request->user()->teacher->courseSections()->with('subject', 'semester')->get();
        });
    });


    // ---------------------------------------------------
    // NHÓM QUYỀN: SINH VIÊN (STUDENT)
    // ---------------------------------------------------
    Route::middleware('role:student')->group(function () {
        // Đăng ký và hủy môn học
        Route::post('/enrollments', [EnrollmentController::class, 'store']);
        Route::delete('/enrollments/{id}', [EnrollmentController::class, 'destroy']);

        // Xem lịch học cá nhân
        Route::get('/student/my-schedule', function (Request $request) {
            return $request->user()->student->enrollments()->with('courseSection.subject', 'courseSection.teacher.user')->get();
        });

        // Xem bảng điểm cá nhân
        Route::get('/student/my-grades', function (Request $request) {
            return $request->user()->student->enrollments()->with('courseSection.subject', 'grade')->get();
        });

        // Xem công nợ/học phí cá nhân
        Route::get('invoices/my-invoices', function (Request $request) {
            $studentId = $request->user()->student->id;
            return app()->call([InvoiceController::class, 'getByStudent'], ['student_id' => $studentId]);
        });
    });

});