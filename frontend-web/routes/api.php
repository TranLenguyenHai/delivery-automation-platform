<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; 

// 1. API N8N GỌI ĐỂ TẠO ĐƠN TỪ TELEGRAM
Route::post('/orders', function (Request $request) {
    $data = $request->all();
    try {
        DB::table('orders')->insert([
            'customer_name'    => $data['receiver_name'] ?? 'Khách',
            'customer_phone'   => $data['receiver_phone'] ?? '',
            'address'          => $data['receiver_address'] ?? '',

            'product_name'     => $data['product_name'] ?? 'Hàng hóa',
            'receiver_name'    => $data['receiver_name'] ?? 'Khách',
            'receiver_phone'   => $data['receiver_phone'] ?? '',
            'receiver_address' => $data['receiver_address'] ?? '',

            'weight'           => $data['weight'] ?? 1,
            'status'           => 'Chờ điều phối',
            'note'             => $data['note'] ?? '',
        ]);
        return response()->json(['status' => 'success']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

// 2. CỔNG NÀY N8N GỌI LẠI SAU KHI AI THẨM ĐỊNH XONG (Đã sửa COALESCE)
Route::put('/orders/{id}/ai-update', function (Request $request, $id) {
    $additionalFee = (int) $request->input('additionalFee', 0);
    $noteAppend = $request->input('noteAppend', '');

    try {
        DB::table('orders')->where('id', $id)->update([
            'status' => 'Chờ in đơn', 
            'shipping_fee' => DB::raw("COALESCE(shipping_fee, 0) + $additionalFee"), 
            'note' => DB::raw("CONCAT(COALESCE(note, ''), '$noteAppend')") 
        ]);

        return response()->json(['status' => 'success', 'message' => 'AI đã cập nhật đơn hàng!']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

// 3. CỔNG CHO N8N QUÉT ĐƠN HÀNG (Schedule Trigger)
Route::get('/orders/status/{status}', function ($status) {
    $dbStatus = $status;
    if ($status === 'AI_PROCESSED') {
        $dbStatus = 'Chờ lấy hàng';
    } elseif ($status === 'DELIVERING') {
        $dbStatus = 'Đang giao hàng';
    }
    $orders = DB::table('orders')->where('status', $dbStatus)->get();
    return response()->json($orders);
});

// 4. CỔNG CHO N8N TỰ ĐỘNG CẬP NHẬT TRẠNG THÁI
Route::put('/orders/{id}/status', function (Request $request, $id) {
    $status = $request->input('status');
    if ($status === 'DELIVERING') $status = 'Đang giao hàng';
    if ($status === 'CANCELLED') $status = 'Giao thất bại';
    if ($status === 'DELIVERED') $status = 'Giao thành công';

    DB::table('orders')->where('id', $id)->update(['status' => $status]);
    return response()->json(['success' => true]);
});

// 5. CỔNG CHO N8N TỰ ĐỘNG GÁN ĐVVC (Đã sửa COALESCE)
Route::put('/orders/{id}/assign-delivery', function (Request $request, $id) {
    $status = $request->input('status') === 'DELIVERING' ? 'Đang giao hàng' : $request->input('status');
    $shipper = $request->input('shipper');
    $shippingFee = (int) $request->input('shippingFee', 0);

    DB::table('orders')->where('id', $id)->update([
        'status' => $status,
        'shipper' => $shipper,
        'shipping_fee' => DB::raw("COALESCE(shipping_fee, 0) + $shippingFee"),
        'note' => DB::raw("CONCAT(COALESCE(note, ''), ' | Auto-Assigned: $shipper')") 
    ]);
    return response()->json(['success' => true]);
});

// 6. CỔNG LẤY BÁO CÁO DOANH THU
Route::get('/orders/stats', function () {
    $totalOrders = DB::table('orders')->count();
    $totalRevenue = DB::table('orders')->sum('shipping_fee');
    return response()->json([
        'tong_don' => $totalOrders,
        'tong_tien' => number_format($totalRevenue, 0, ',', '.')
    ]);
});

// 7. CỔNG LẤY ĐƠN HÀNG MỚI NHẤT (ĐÃ VÁ ĐỦ TRƯỜNG CHO N8N)
Route::get('/orders/latest', function () {
    $order = DB::table('orders')->orderBy('id', 'desc')->first();

    if (!$order) {
        return response()->json(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng nào!'], 404);
    }

    return response()->json([
        'weight'           => $order->weight,
        'sender_address'   => $order->sender_address ?? 'Đà Nẵng', 
        'receiver_address' => $order->receiver_address ?? $order->address,
        'receiverName'     => $order->receiver_name ?? 'Khách',
        'receiverPhone'    => $order->receiver_phone ?? '0912345678',
        'distanceKm'       => 17.0 // Mock quãng đường chuẩn cho n8n tính tiền
    ]);
});