<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

Route::post('/api/orders', function (Illuminate\Http\Request $request) {
    $data = $request->all();
    try {
        DB::table('orders')->insert([
            // Đổ vào cả cột customer để Web hiển thị
            'customer_name'    => $data['receiver_name'] ?? 'Thái',
            'customer_phone'   => $data['receiver_phone'] ?? '',
            'address'          => $data['receiver_address'] ?? '',

            // Đổ vào cả cột receiver để lưu trữ
            'product_name'     => $data['product_name'] ?? 'Chuột Razer',
            'receiver_name'    => $data['receiver_name'] ?? 'Thái',
            'receiver_phone'   => $data['receiver_phone'] ?? '',
            'receiver_address' => $data['receiver_address'] ?? '',

            'weight'           => $data['weight'] ?? 1,
            'status'           => 'Chờ điều phối', // Sếp copy đúng chữ này từ DB sếp đang chạy
            'note'             => $data['note'] ?? '',
        ]);
        return response()->json(['status' => 'success']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});
