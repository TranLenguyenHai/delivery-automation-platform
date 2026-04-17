<?php
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // BẮT BUỘC THÊM ĐỂ GỌI N8N

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    $orders = DB::table('orders')->orderBy('id', 'desc')->get();
    return view('dashboard', ['orders' => $orders]);
})->middleware(['auth', 'verified'])->name('dashboard');

// 1. TRANG CHỜ XÁC NHẬN (Đơn từ Telegram)
Route::get('/pending', function () {
    $orders = DB::table('orders')->where('status', 'Chờ điều phối')->orderBy('id', 'desc')->get();
    return view('pending', compact('orders'));
})->middleware(['auth', 'verified'])->name('pending');


// NÚT XÁC NHẬN: Gọi AI thẩm định từ n8n
Route::post('/orders/confirm', function (Request $request) {
    if (!empty($request->ids)) {
        foreach ($request->ids as $id) {
            $order = DB::table('orders')->where('id', $id)->first();

            if ($order) {
                // Đổi trạng thái tạm để Admin biết AI đang chạy
                DB::table('orders')->where('id', $id)->update(['status' => 'Đang thẩm định AI...']);

                // Gọi sang Webhook của n8n (Nhớ đổi lại IP/Port nếu máy Thái chạy khác)
                try {
                    Http::post('http://127.0.0.1:5678/webhook/ai-logistic-trigger', [
                        'orderId' => $id,
                        'note' => $order->note ?? '',
                        'distance' => 5 // Mock số km, hoặc lấy từ DB nếu ông có cột distance
                    ]);
                } catch (\Exception $e) {
                    // Nếu n8n chưa bật thì tự động đẩy vào kho luôn để khỏi lỗi web
                    DB::table('orders')->where('id', $id)->update(['status' => 'Chờ in đơn']);
                }
            }
        }
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 400);
})->name('orders.confirm');


// 2. TRANG ĐANG XỬ LÝ (Kho)
Route::get('/processing', function () {
    // Kéo thêm cái trạng thái 'Đang thẩm định AI...' vào để nó không bị biến mất khỏi giao diện
    $orders = DB::table('orders')->whereIn('status', ['Chờ in đơn', 'Chờ lấy hàng', 'Đang thẩm định AI...'])->orderBy('id', 'desc')->get();
    return view('processing', compact('orders'));
})->middleware(['auth', 'verified'])->name('processing');


// ==========================================
// NÚT IN ĐƠN (CHỈ TÍNH TIỀN CHO ĐƠN TELEGRAM CHƯA CÓ SHIPPER)
// ==========================================
Route::post('/orders/print', function (Request $request) {
    if (!empty($request->ids)) {
        foreach ($request->ids as $id) {
            $order = DB::table('orders')->where('id', $id)->first();

            if ($order) {
                // CHỐT CHẶN QUAN TRỌNG:
                // Nếu đơn đã có Shipper (đơn tạo thủ công đã tính phí lúc tạo)
                // thì KHÔNG GỌI n8n nữa, chỉ đổi trạng thái.
                if (!empty($order->shipper)) {
                    DB::table('orders')->where('id', $id)->update(['status' => 'Chờ lấy hàng']);
                    continue;
                }

                // Nếu là đơn từ Telegram đổ về (chưa có Shipper) thì mới gọi n8n tính phí
                try {
                    $response = Http::timeout(5)->post('http://127.0.0.1:5678/webhook/e99d1f26-3a52-49d9-93e5-ed402977fcb6', [
                        'weight' => (float) $order->weight,
                        'distance' => 10
                    ]);

                    if ($response->successful()) {
                        $result = $response->json();
                        $bestShipper = $result['shipper'] ?? 'GHTK';
                        $carrierFee = (int) ($result['fee'] ?? 0);

                        DB::table('orders')->where('id', $id)->update([
                            'status' => 'Chờ lấy hàng',
                            'shipper' => $bestShipper,
                            'shipping_fee' => DB::raw("shipping_fee + $carrierFee"),
                            'note' => DB::raw("CONCAT(IFNULL(note, ''), ' | ĐVVC: $bestShipper')")
                        ]);
                    } else {
                        DB::table('orders')->where('id', $id)->update(['status' => 'Chờ lấy hàng']);
                    }
                } catch (\Exception $e) {
                    DB::table('orders')->where('id', $id)->update(['status' => 'Chờ lấy hàng']);
                }
            }
        }
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 400);
})->name('orders.print');


// Bàn giao: Bây giờ chỉ việc đổi trạng thái vì ĐVVC đã chốt ở bước In đơn
Route::post('/orders/handover', function (Request $request) {
    if (!empty($request->ids)) {
        DB::table('orders')->whereIn('id', $request->ids)->update(['status' => 'Đang giao hàng']);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 400);
})->name('orders.handover');


// 3. TRANG TRẠNG THÁI VẬN CHUYỂN
Route::get('/status', function () {
    $orders = DB::table('orders')
                ->whereIn('status', ['Đang giao hàng', 'Giao thành công', 'Giao thất bại'])
                ->orderBy('id', 'desc')
                ->get();
    return view('status', compact('orders'));
})->middleware(['auth', 'verified'])->name('status');


// 4. TẠO ĐƠN THỦ CÔNG
Route::get('/create-order', function () {
    return view('create_order');
})->middleware(['auth', 'verified'])->name('create_order');


// ==========================================
// TẠO ĐƠN THỦ CÔNG (TÍNH TIỀN SHIP NGAY LẬP TỨC)
// ==========================================
Route::post('/orders/store', function (Request $request) {
    // Lấy tiền COD người dùng nhập (nếu không nhập thì là 0)
    $cod = (int) ($request->shipping_fee ?? 0);
    $weight = (float) ($request->weight ?? 0);
    $note = $request->note ?? '';

    $carrierFee = 0;
    $bestShipper = null;

    // Gọi ngay luồng n8n "Cước Phí" để tính tiền ship
    try {
        $response = Http::timeout(5)->post('http://127.0.0.1:5678/webhook/e99d1f26-3a52-49d9-93e5-ed402977fcb6', [
            'weight' => $weight,
            'distance' => 10 // Mock khoảng cách 10km
        ]);

        if ($response->successful()) {
            $result = $response->json();
            $bestShipper = $result['shipper'] ?? 'GHTK';
            $carrierFee = (int) ($result['fee'] ?? 0);

            $note = trim($note . " | ĐVVC: $bestShipper");
        }
    } catch (\Exception $e) {
        // Bỏ qua lỗi kết nối n8n
    }

    // TỔNG TIỀN = Tiền thu hộ COD + Tiền ship
    $totalFee = $cod + $carrierFee;

    DB::table('orders')->insert([
        'status' => 'Chờ in đơn',
        'receiver_name' => $request->receiver_name,
        'receiver_phone' => $request->receiver_phone,
        'receiver_address' => $request->receiver_address,
        'product_name' => $request->product_name,
        'shipping_fee' => $totalFee,
        'shipper' => $bestShipper,
        'weight' => $weight,
        'note' => $note,
    ]);

    return redirect()->route('processing')->with('success', 'Đã tạo đơn và tính phí ship tự động!');
})->name('orders.store');


// NÚT CHỐT ĐƠN (GIAO THÀNH CÔNG) - Bắn data sang n8n ghi sổ Google Sheets
Route::post('/orders/complete', function (Request $request) {
    if (!empty($request->ids)) {
        foreach ($request->ids as $id) {
            $order = DB::table('orders')->where('id', $id)->first();

            if ($order && $order->status !== 'Giao thành công') {
                // 1. Cập nhật trạng thái thành công trong Database
                DB::table('orders')->where('id', $id)->update(['status' => 'Giao thành công']);

                // 2. BẮN TÍN HIỆU CHO N8N (FIRE & FORGET)
                try {
                    Http::timeout(3)->post('http://127.0.0.1:5678/webhook/9102f4a1-7868-44a3-b33c-78114c981656', [
                        'id_don' => '#REQ-' . $order->id,
                        'thoi_gian' => date('d/m/Y H:i:s'),
                        'khoi_luong' => (float) $order->weight,
                        'ten_hang' => $order->product_name,
                        'don_vi_giao' => $order->shipper ?? 'Hệ thống',
                        'phi_ship' => (int) $order->shipping_fee
                    ]);
                } catch (\Exception $e) {
                    // Cố tình bẫy lỗi ở đây để web vẫn chạy bình thường
                }
            }
        }
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 400);
})->name('orders.complete');


// =========================================================================
// API TRÙM CUỐI: TỐI ƯU HÓA LỘ TRÌNH (Gọi từ Dashboard)
// =========================================================================
Route::post('/orders/optimize', function (Request $request) {
    $ids = $request->ids;

    if (empty($ids)) {
        return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất 1 đơn hàng.']);
    }

    $orders = DB::table('orders')->whereIn('id', $ids)->get();

    $payloadOrders = [];
    foreach ($orders as $order) {
        $payloadOrders[] = [
            'id' => '#REQ-' . $order->id,
            'weight' => (float) $order->weight,
            'tinh_chat' => stripos($order->note ?? '', 'dễ vỡ') !== false ? 'FRAGILE' : 'NORMAL',
            'lat' => 16.05 + (rand(0, 50) / 1000),
            'lng' => 108.20 + (rand(0, 50) / 1000)
        ];
    }

    try {
        $response = Http::timeout(15)->post('http://127.0.0.1:5678/webhook/7e4fcc97-1ea5-4bdd-8c8f-f25c2fe75a50', [
            'orders' => $payloadOrders
        ]);

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'data' => $response->json()
            ]);
        }
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Lỗi kết nối hệ thống AI: ' . $e->getMessage()]);
    }

    return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, không nhận được phản hồi từ AI.']);
})->name('orders.optimize');


Route::get('/history', function () {
    // Chỉ lấy những đơn đã chốt hạ (Thành công, Thất bại, Đã hủy)
    $orders = DB::table('orders')
                ->whereIn('status', ['Giao thành công', 'Giao thất bại', 'Đã hủy'])
                ->orderBy('id', 'desc')
                ->get();
    return view('history', compact('orders'));
})->middleware(['auth', 'verified'])->name('history');


// 1. XÓA VĨNH VIỄN (Dùng cho trang Chờ xác nhận - Đơn rác)
Route::post('/orders/destroy', function (Request $request) {
    if (!empty($request->ids)) {
        DB::table('orders')->whereIn('id', $request->ids)->delete();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 400);
})->name('orders.destroy');


// 2. HỦY ĐƠN HÀNG (Dùng cho trang Đang xử lý - Chuyển sang Lịch sử/Đã hủy)
Route::post('/orders/cancel', function (Request $request) {
    if (!empty($request->ids)) {
        DB::table('orders')->whereIn('id', $request->ids)->update([
            'status' => 'Đã hủy',
            'note' => DB::raw("CONCAT(IFNULL(note, ''), ' | [Đã hủy bởi Admin]')")
        ]);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 400);
})->name('orders.cancel');

require __DIR__.'/auth.php';
