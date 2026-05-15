<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; 

Route::redirect('/', '/login');

// 1. TRANG DASHBOARD TỔNG QUAN (Đã tối ưu)
Route::get('/dashboard', function () {
    $orders = DB::table('orders')->where('status', 'Chờ điều phối')->orderBy('id', 'desc')->limit(100)->get();
    $optimizedOrders = DB::table('orders')->whereIn('status', ['Chờ in đơn', 'Đang thẩm định AI...'])->orderBy('id', 'desc')->limit(10)->get();
    $recentDeliveries = DB::table('orders')->whereIn('status', ['Đang giao hàng', 'Giao thành công'])->orderBy('id', 'desc')->limit(10)->get();
    
    $stats = DB::table('orders')->selectRaw("
        COUNT(*) as total,
        SUM(CASE WHEN status != 'Đã hủy' THEN shipping_fee ELSE 0 END) as revenue,
        SUM(CASE WHEN status = 'Giao thành công' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status = 'Chờ điều phối' THEN 1 ELSE 0 END) as pending,
        COUNT(DISTINCT shipper) as shippers
    ")->first();

    return view('dashboard', [
        'orders' => $orders,
        'optimizedOrders' => $optimizedOrders,
        'recentDeliveries' => $recentDeliveries,
        'todayRevenue' => $stats->revenue ?? 0,
        'newOrdersCount' => $stats->total ?? 0,
        'pendingCount' => $stats->pending ?? 0,
        'completionRate' => ($stats->total > 0) ? round(($stats->completed / $stats->total) * 100, 1) : 0,
        'shipperCount' => $stats->shippers > 0 ? $stats->shippers : 45
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// 2. CÁC TRANG QUẢN LÝ TRẠNG THÁI
Route::get('/pending', function () {
    $orders = DB::table('orders')->where('status', 'Chờ điều phối')->orderBy('id', 'desc')->get();
    return view('pending', compact('orders'));
})->name('pending');

Route::get('/processing', function () {
    $orders = DB::table('orders')->whereIn('status', ['Chờ in đơn', 'Chờ lấy hàng', 'Đang thẩm định AI...'])->orderBy('id', 'desc')->get();
    return view('processing', compact('orders'));
})->name('processing');

Route::get('/status', function () {
    $orders = DB::table('orders')->whereIn('status', ['Đang giao hàng', 'Giao thành công', 'Giao thất bại'])->orderBy('id', 'desc')->get();
    return view('status', compact('orders'));
})->name('status');

Route::get('/history', function () {
    $orders = DB::table('orders')->whereIn('status', ['Giao thành công', 'Giao thất bại', 'Đã hủy'])->orderBy('id', 'desc')->get();
    return view('history', compact('orders'));
})->name('history');

// 3. TẠO ĐƠN THỦ CÔNG (ĐÃ KHÔI PHỤC)
Route::get('/create-order', function () {
    return view('create_order');
})->name('create_order');

Route::post('/orders/store', function (Request $request) {
    $cod = (int) ($request->shipping_fee ?? 0);
    $weight = (float) ($request->weight ?? 0);
    $carrierFee = 0; $bestShipper = null; $note = $request->note ?? '';

    try {
        $response = Http::timeout(10)->post(env('N8N_BASE_URL', 'http://localhost:5678') . '/webhook/' . env('N8N_WEBHOOK_OPTIMIZE', 'e99d1f26-3a52-49d9-93e5-ed402977fcb6'), ['weight' => $weight, 'distance' => 10]);
        if ($response->successful()) {
            $result = $response->json();
            $bestShipper = $result['shipper'] ?? 'GHTK';
            $carrierFee = (int) ($result['fee'] ?? 0);
            $note = trim($note . " | ĐVVC: $bestShipper");
        }
    } catch (\Exception $e) {}

    DB::table('orders')->insert([
        'status' => 'Chờ in đơn',
        'sender_name' => $request->sender_name, 'sender_phone' => $request->sender_phone, 'sender_address' => $request->sender_address,
        'receiver_name' => $request->receiver_name, 'receiver_phone' => $request->receiver_phone, 'receiver_address' => $request->receiver_address,
        'product_name' => $request->product_name, 'shipping_fee' => $cod + $carrierFee, 'shipper' => $bestShipper, 'weight' => $weight, 'note' => $note,
    ]);
    return redirect()->route('processing')->with('success', 'Đã tạo đơn!');
})->name('orders.store');

// 4. CÁC THAO TÁC TRÊN ĐƠN HÀNG (ĐÃ KHÔI PHỤC)
Route::post('/orders/confirm', function (Request $request) {
    if (!empty($request->ids)) {
        DB::table('orders')->whereIn('id', $request->ids)->update(['status' => 'Chờ in đơn']);
        
        $results = [];
        foreach ($request->ids as $id) {
            $order = DB::table('orders')->where('id', $id)->first();
            if ($order) {
                try {
                    $n8nBase = env('N8N_BASE_URL', 'http://localhost:5678');
                    $webhookUrl = $n8nBase . '/webhook/ai-logistic-trigger';
                    $response = Http::timeout(120)->post($webhookUrl, ['orderId' => $id, 'distance' => 17]);
                    if ($response->status() == 404) {
                        $webhookUrl = $n8nBase . '/webhook-test/ai-logistic-trigger';
                        $response = Http::timeout(120)->post($webhookUrl, ['orderId' => $id, 'distance' => 17]);
                    }
                    if ($response->successful()) {
                        $aiData = $response->json();
                        $results[] = [
                            'id' => $id,
                            'tinh_chat_hang' => $aiData['tinh_chat_hang'] ?? (str_contains(mb_strtolower($order->note ?? ''), 'dễ vỡ') ? 'FRAGILE' : 'NORMAL'),
                            'thoi_tiet' => $aiData['thoi_tiet'] ?? 'Bình thường',
                            'quang_duong' => $aiData['quang_duong'] ?? 17,
                            'tong_tien' => (int)($aiData['tong_tien'] ?? $aiData['cuoc_phi'] ?? 25000)
                        ];
                    } else {
                        $results[] = ['id' => $id, 'tinh_chat_hang' => str_contains(mb_strtolower($order->note ?? ''), 'dễ vỡ') ? 'FRAGILE' : 'NORMAL', 'thoi_tiet' => 'Bình thường', 'quang_duong' => 17, 'tong_tien' => 25000];
                    }
                } catch (\Exception $e) {
                    $results[] = ['id' => $id, 'tinh_chat_hang' => str_contains(mb_strtolower($order->note ?? ''), 'dễ vỡ') ? 'FRAGILE' : 'NORMAL', 'thoi_tiet' => 'Bình thường', 'quang_duong' => 17, 'tong_tien' => 25000];
                }
            }
        }
        
        return response()->json(['success' => true, 'results' => $results]);
    }
    return response()->json(['success' => false], 400);
})->name('orders.confirm');

Route::post('/orders/print', function (Request $request) {
    if (!empty($request->ids)) {
        DB::table('orders')->whereIn('id', $request->ids)->update(['status' => 'Chờ lấy hàng']);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 400);
})->name('orders.print');

Route::get('/orders/print-labels', function (Request $request) {
    if (!$request->has('ids') || empty($request->ids)) {
        return redirect()->route('processing')->with('error', 'Không có đơn hàng nào được chọn!');
    }
    $ids = explode(',', $request->ids);
    $orders = DB::table('orders')->whereIn('id', $ids)->get();
    return view('print_labels', compact('orders'));
})->name('orders.print_labels');

Route::post('/orders/handover', function (Request $request) {
    if (!empty($request->ids)) {
        DB::table('orders')->whereIn('id', $request->ids)->update(['status' => 'Đang giao hàng']);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 400);
})->name('orders.handover');

Route::post('/orders/complete', function (Request $request) {
    if (!empty($request->ids)) {
        foreach ($request->ids as $id) {
            $order = DB::table('orders')->where('id', $id)->first();
            if ($order && $order->status !== 'Giao thành công') {
                DB::table('orders')->where('id', $id)->update(['status' => 'Giao thành công']);

                try {
                    // Cập nhật URL Webhook Telegram mới của bạn
                    Http::timeout(10)->post(env('N8N_BASE_URL', 'http://localhost:5678') . '/webhook/' . env('N8N_WEBHOOK_TELEGRAM', '627dd940-97f9-472c-b88b-93f953d7520a') . '/webhook', [
                        'id_don' => '#REQ-' . $order->id,
                        'thoi_gian' => date('d/m/Y H:i:s'),
                        'khoi_luong' => (float) $order->weight,
                        'ten_hang' => $order->product_name,
                        'don_vi_giao' => $order->shipper ?? 'Hệ thống',
                        'phi_ship' => (int) $order->shipping_fee
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Telegram Webhook Error: " . $e->getMessage());
                }
            }
        }
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 400);
})->name('orders.complete');

Route::post('/orders/cancel', function (Request $request) {
    if (!empty($request->ids)) {
        DB::table('orders')->whereIn('id', $request->ids)->update(['status' => 'Đã hủy']);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 400);
})->name('orders.cancel');

Route::post('/orders/destroy', function (Request $request) {
    if (!empty($request->ids)) {
        DB::table('orders')->whereIn('id', $request->ids)->delete();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 400);
})->name('orders.destroy');

// 5. TỐI ƯU AI (Giữ nguyên bản sửa lỗi)
Route::post('/orders/optimize', function (Request $request) {
    set_time_limit(120); $ids = $request->ids;
    if (empty($ids)) return response()->json(['success' => false, 'message' => 'Chọn đơn!']);
    $results = []; $errors = [];
    foreach ($ids as $id) {
        $order = DB::table('orders')->where('id', $id)->first();
        if ($order) {
            try {
                $n8nBase = env('N8N_BASE_URL', 'http://localhost:5678');
                $webhookUrl = $n8nBase . '/webhook/ai-logistic-trigger';
                $response = Http::timeout(120)->post($webhookUrl, ['orderId' => $id, 'distance' => 17]);
                if ($response->status() == 404) {
                    $webhookUrl = $n8nBase . '/webhook-test/ai-logistic-trigger';
                    $response = Http::timeout(120)->post($webhookUrl, ['orderId' => $id, 'distance' => 17]);
                }
                if ($response->successful()) {
                    $aiData = $response->json();
                    $results[] = [
                        'ma_don' => "#REQ-$id", 'ten_nguoi_nhan' => $aiData['ten_nguoi_nhan'] ?? $order->receiver_name,
                        'sdt_nhan' => $aiData['sdt_nhan'] ?? $order->receiver_phone, 'quang_duong' => $aiData['quang_duong'] ?? 17,
                        'thoi_tiet' => $aiData['thoi_tiet'] ?? 'Nắng', 'phi_thoi_tiet' => (int)($aiData['phi_thoi_tiet'] ?? 0),
                        'phi_de_vo' => (int)($aiData['phi_de_vo'] ?? 0), 'cuoc_phi' => (int)($aiData['cuoc_phi'] ?? $aiData['tong_tien'] ?? 25000)
                    ];
                }
            } catch (\Exception $e) {}
        }
    }
    return response()->json(['success' => count($results) > 0, 'data' => $results]);
})->name('orders.optimize');

require __DIR__.'/auth.php';