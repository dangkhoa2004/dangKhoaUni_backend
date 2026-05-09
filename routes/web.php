<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/link-storage', function () {
    $target = base_path('storage/app/public');
    $shortcut = public_path('storage');
    symlink($target, $shortcut);
    return 'Đã tạo liên kết Storage thành công!';
});