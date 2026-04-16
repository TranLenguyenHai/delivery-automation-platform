<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB; // <--- Thêm cái này để gọi Database

Route::get('/', function () {
    return view('welcome');
});

// Chỗ này là chỗ anh em mình sửa để lấy đơn hàng này sếp
Route::get('/dashboard', function () {
    // Kéo toàn bộ đơn hàng từ bảng orders
    $orders = DB::table('orders')->orderBy('id', 'desc')->get();

    // Ném cái mảng $orders sang cho ông Hải vẽ HTML
    return view('dashboard', ['orders' => $orders]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
