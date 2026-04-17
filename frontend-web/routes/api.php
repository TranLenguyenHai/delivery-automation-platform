<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; // THÊM DÒNG NÀY ĐỂ KHÔNG LỖI ROUTE

// API NÀY N8N GỌI ĐỂ TẠO ĐƠN TỪ TELEGRAM
Route::post('/api/orders', function (Request $request) {
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

// CỔNG NÀY N8N GỌI LẠI SAU KHI AI THẨM ĐỊNH XONG
Route::put('/api/orders/{id}/ai-update', function (Request $request, $id) {
    // Lấy dữ liệu từ node "HTTP Request" của n8n bắn sang
    $additionalFee = (int) $request->input('additionalFee', 0);
    $noteAppend = $request->input('noteAppend', '');

    // N8n gửi status là "STANDARD_SHIPPING", nhưng web mình cần "Chờ in đơn" để nó nằm ở kho
    try {
        DB::table('orders')->where('id', $id)->update([
            'status' => 'Chờ in đơn', // Ép về kho luôn
            'shipping_fee' => DB::raw("IFNULL(shipping_fee, 0) + $additionalFee"), // Cộng dồn phụ phí AI tính
            'note' => DB::raw("CONCAT(IFNULL(note, ''), '$noteAppend')") // Gắn thêm cái AI_TAG
        ]);

        return response()->json(['status' => 'success', 'message' => 'AI đã cập nhật đơn hàng!']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});


// =========================================================================
// CÁC CỔNG API CHO N8N TỰ ĐỘNG ĐIỀU PHỐI (BACKGROUND CRON JOBS)
// =========================================================================

// 1. CỔNG CHO N8N QUÉT ĐƠN HÀNG MỖI PHÚT (Schedule Trigger)
// N8n sẽ gọi vào: /api/orders/status/AI_PROCESSED
Route::get('/api/orders/status/{status}', function ($status) {
    // Map trạng thái tiếng Anh của n8n sang tiếng Việt của Web
    $dbStatus = $status;
    if ($status === 'AI_PROCESSED') {
        $dbStatus = 'Chờ lấy hàng'; // Quét các đơn đã in xong nhưng chưa bàn giao
    }

    $orders = DB::table('orders')->where('status', $dbStatus)->get();

    // N8n cần trả về mảng JSON
    return response()->json($orders);
});


// 2. CỔNG CHO N8N TỰ ĐỘNG CẬP NHẬT TRẠNG THÁI (VD: Sang Đang giao hàng)
Route::put('/api/orders/{id}/status', function (Request $request, $id) {
    $status = $request->input('status');

    // Map tiếng Anh sang tiếng Việt
    if ($status === 'DELIVERING') $status = 'Đang giao hàng';
    if ($status === 'CANCELLED') $status = 'Giao thất bại';

    DB::table('orders')->where('id', $id)->update(['status' => $status]);

    return response()->json(['success' => true]);
});


// 3. CỔNG CHO N8N TỰ ĐỘNG GÁN ĐVVC VÀ ĐẨY ĐI GIAO
Route::put('/api/orders/{id}/assign-delivery', function (Request $request, $id) {
    $status = $request->input('status') === 'DELIVERING' ? 'Đang giao hàng' : $request->input('status');
    $shipper = $request->input('shipper');
    $shippingFee = (int) $request->input('shippingFee', 0);

    DB::table('orders')->where('id', $id)->update([
        'status' => $status,
        'shipper' => $shipper,
        'shipping_fee' => DB::raw("IFNULL(shipping_fee, 0) + $shippingFee"),
        'note' => DB::raw("CONCAT(IFNULL(note, ''), ' | Auto-Assigned: $shipper')") // Note lại để biết hệ thống tự gán
    ]);

    return response()->json(['success' => true]);
});


// 4. CỔNG CHO LÚC 23:00 N8N VÀO LẤY BÁO CÁO DOANH THU GỬI MAIL SẾP THÁI
Route::get('/api/orders/stats', function () {
    // Lấy tổng đơn và tổng doanh thu
    $totalOrders = DB::table('orders')->count();
    $totalRevenue = DB::table('orders')->sum('shipping_fee');

    // N8n dang chờ cục JSON có trường 'tong_don' và 'tong_tien'
    return response()->json([
        'tong_don' => $totalOrders,
        'tong_tien' => number_format($totalRevenue, 0, ',', '.')
    ]);
});
