<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\TuitionFee;
use App\Models\Invoice;
class EnrollmentController extends Controller
{
/**
     * API: POST /api/enrollments
     * Đăng ký môn -> Tạo bảng điểm -> TÍNH CỘNG CÔNG NỢ
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_section_id' => 'required|exists:course_sections,id',
        ]);

        $studentId = $request->student_id;
        $sectionId = $request->course_section_id;

        if (Enrollment::where('student_id', $studentId)->where('course_section_id', $sectionId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Bạn đã đăng ký lớp học phần này rồi!'], 400);
        }

        DB::beginTransaction();
        try {
            // 1. Tạo record đăng ký môn và điểm
            $enrollment = Enrollment::create(['student_id' => $studentId, 'course_section_id' => $sectionId, 'status' => 'approved']);
            Grade::create(['enrollment_id' => $enrollment->id]);

            // === LOGIC TÍNH HỌC PHÍ ===
            $section = CourseSection::with('subject')->find($sectionId);
            $student = Student::find($studentId);
            
            $credits = $section->subject->credits; // Lấy số tín chỉ của môn học
            $semesterId = $section->semester_id;

            // Tìm đơn giá tín chỉ của ngành đó trong học kỳ đó
            $tuitionFee = TuitionFee::where('major_id', $student->major_id)
                                    ->where('semester_id', $semesterId)
                                    ->first();
            
            // Nếu admin chưa cấu hình giá tiền, mặc định giá là 0
            $costPerCredit = $tuitionFee ? $tuitionFee->cost_per_credit : 0; 
            $amountToAdd = $credits * $costPerCredit;

            if ($amountToAdd > 0) {
                // Tìm hóa đơn của kỳ này, nếu chưa có thì tạo mới với tổng nợ = 0
                $invoice = Invoice::firstOrCreate(
                    ['student_id' => $studentId, 'semester_id' => $semesterId],
                    ['total_amount' => 0, 'paid_amount' => 0, 'status' => 'unpaid']
                );

                // Cộng dồn tiền học phí môn này vào hóa đơn
                $invoice->total_amount += $amountToAdd;
                
                // Cập nhật lại trạng thái hóa đơn
                if ($invoice->paid_amount >= $invoice->total_amount) {
                    $invoice->status = 'paid';
                } else {
                    $invoice->status = ($invoice->paid_amount > 0) ? 'partial' : 'unpaid';
                }
                $invoice->save();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Đăng ký thành công và đã cập nhật công nợ!'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: DELETE /api/enrollments/{id}
     * Hủy môn -> TÍNH TRỪ CÔNG NỢ
     */
    public function destroy(string $id)
    {
        $enrollment = Enrollment::with(['courseSection.subject', 'student'])->find($id);
        if (!$enrollment) return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);

        DB::beginTransaction();
        try {
            // === LOGIC TRỪ HỌC PHÍ ===
            $credits = $enrollment->courseSection->subject->credits;
            $semesterId = $enrollment->courseSection->semester_id;
            $majorId = $enrollment->student->major_id;

            $tuitionFee = TuitionFee::where('major_id', $majorId)->where('semester_id', $semesterId)->first();
            $costPerCredit = $tuitionFee ? $tuitionFee->cost_per_credit : 0;
            $amountToSubtract = $credits * $costPerCredit;

            if ($amountToSubtract > 0) {
                $invoice = Invoice::where('student_id', $enrollment->student_id)
                                  ->where('semester_id', $semesterId)->first();
                if ($invoice) {
                    $invoice->total_amount -= $amountToSubtract;
                    if ($invoice->total_amount < 0) $invoice->total_amount = 0; // Đề phòng lỗi số âm
                    
                    if ($invoice->total_amount == 0) {
                        $invoice->status = 'unpaid'; // Không nợ gì
                    } else {
                        $invoice->status = ($invoice->paid_amount >= $invoice->total_amount) ? 'paid' : ($invoice->paid_amount > 0 ? 'partial' : 'unpaid');
                    }
                    $invoice->save();
                }
            }

            // Tiến hành xóa môn học
            $enrollment->delete(); 
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Đã hủy môn và giảm công nợ']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }
}