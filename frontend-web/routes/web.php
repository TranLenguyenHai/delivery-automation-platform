<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB; // Kéo thư viện DB vào để dùng giống Thái

Route::redirect('/', '/login');

// Trang Tổng quan (Dashboard mới)
Route::get('/dashboard', function () {
    // Code của Thái: Lấy toàn bộ đơn hàng
    $orders = DB::table('orders')->orderBy('id', 'desc')->get();
    return view('dashboard', ['orders' => $orders]);
})->middleware(['auth', 'verified'])->name('dashboard');


// Trang Chờ xác nhận (Dashboard cũ đổi tên)
Route::get('/pending', function () {
    // Code chuẩn của Thái: Dùng DB::table chọc thẳng vào bảng orders
    $orders = DB::table('orders')
                ->where('status', 'Chờ điều phối')
                ->orderBy('id', 'desc')
                ->get();

    return view('pending', compact('orders'));
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
