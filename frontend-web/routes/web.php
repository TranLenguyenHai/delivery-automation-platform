<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB; // <--- Thêm cái này để gọi Database

Route::redirect('/', '/login');

// Chỗ này là chỗ anh em mình sửa để lấy đơn hàng này sếp
// Trang Tổng quan (Dashboard mới)
Route::get('/dashboard', function () {
    // Kéo toàn bộ đơn hàng từ bảng orders
    $orders = DB::table('orders')->orderBy('id', 'desc')->get();

    // Ném cái mảng $orders sang cho ông Hải vẽ HTML
    return view('dashboard', ['orders' => $orders]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Trang Chờ xác nhận (Dashboard cũ đổi tên)
Route::get('/pending', function () {
    return view('pending');
})->middleware(['auth', 'verified'])->name('pending');

Route::get('/processing', function () {
    return view('processing');
})->middleware(['auth', 'verified'])->name('processing');

Route::get('/status', function () {
    return view('status');
})->middleware(['auth', 'verified'])->name('status');

Route::get('/history', function () {
    return view('history');
})->middleware(['auth', 'verified'])->name('history');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
