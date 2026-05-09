<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        // Môn học kèm khoa và môn tiên quyết
        $subjects = Subject::with(['department', 'prerequisite'])->get();
        return response()->json(['success' => true, 'data' => $subjects], 200);
    }

    public function show(string $id)
    {
        $subject = Subject::with(['department', 'prerequisite'])->find($id);
        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy môn học'], 404);
        }
        return response()->json(['success' => true, 'data' => $subject], 200);
    }

    public function store(Request $request) {}
    public function update(Request $request, string $id)
    {
        $subject = Subject::find($id);
        if (!$subject) return response()->json(['success' => false, 'message' => 'Không tìm thấy môn học'], 404);

        $request->validate([
            'attachment_file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:5120', // Tối đa 5MB
        ]);

        if ($request->hasFile('attachment_file')) {
            $file = $request->file('attachment_file');
            
            // Đặt tên file theo mã môn học để dễ quản lý
            $fileName = $subject->code . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Di chuyển file vào thư mục public/uploads/documents (Phù hợp với cPanel)
            $file->move(public_path('uploads/documents'), $fileName);
            
            // Lưu đường dẫn vào database
            $subject->attachment = '/uploads/documents/' . $fileName;
        }

        $subject->update($request->except('attachment_file'));

        return response()->json(['success' => true, 'message' => 'Cập nhật tài liệu thành công', 'data' => $subject]);
    }
    public function destroy(string $id) {}
}