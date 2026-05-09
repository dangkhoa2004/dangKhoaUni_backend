<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * API: GET /api/invoices/student/{student_id}
     * Sinh viên xem toàn bộ công nợ của mình qua các học kỳ
     */
    public function getByStudent(string $student_id)
    {
        $invoices = Invoice::with('semester')
                            ->where('student_id', $student_id)
                            ->get();
                            
        return response()->json(['success' => true, 'data' => $invoices], 200);
    }

    /**
     * API: POST /api/invoices/{id}/pay
     * Nhà trường (Admin) ghi nhận sinh viên nộp tiền
     */
    public function pay(Request $request, string $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000' // Nhập số tiền đóng (ví dụ nộp 1.000.000)
        ]);

        $invoice = Invoice::find($id);
        if (!$invoice) return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn'], 404);

        if ($invoice->status == 'paid') {
            return response()->json(['success' => false, 'message' => 'Hóa đơn này đã được thanh toán đủ'], 400);
        }

        // Cộng số tiền sinh viên vừa đóng vào mục đã thanh toán
        $invoice->paid_amount += $request->amount;

        // Kiểm tra xem đã đóng đủ hay chưa để cập nhật trạng thái
        if ($invoice->paid_amount >= $invoice->total_amount) {
            $invoice->status = 'paid';
            // Tùy chọn: Nếu đóng dư tiền ($invoice->paid_amount > $invoice->total_amount), 
            // có thể lưu số dư vào ví điện tử của sinh viên (nếu hệ thống phát triển thêm sau này).
        } else {
            $invoice->status = 'partial';
        }

        $invoice->save();

        return response()->json([
            'success' => true, 
            'message' => 'Đã ghi nhận thanh toán thành công',
            'data' => $invoice
        ], 200);
    }
}