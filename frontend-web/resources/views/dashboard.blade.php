<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(Auth::user()->email === 'admin@gmail.com')
                <i class="fa-solid fa-user-shield text-primary me-2"></i> {{ __('Quản Trị Hệ Thống Điều Phối Logistics AI') }}
            @else
                <i class="fa-solid fa-store text-success me-2"></i> {{ __('Cổng Quản Lý Đơn Hàng Của Đối Tác') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <style>
                        body { background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
                        .card { border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none; transition: transform 0.3s ease, box-shadow 0.3s ease; }
                        .section-title { color: #4f46e5; font-size: 1.15rem; font-weight: 800; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; margin-top: 25px; text-transform: uppercase; letter-spacing: 0.5px; }
                        .header-title { color: #2c3e50; font-weight: 800; }
                        .form-control, .form-select { border-radius: 10px; padding: 12px 15px; border: 1px solid #cbd5e1; transition: border-color 0.2s, box-shadow 0.2s;}
                        .form-control:focus, .form-select:focus { border-color: #4f46e5; box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25); }
                        .btn-primary, .btn-success, .btn-info, .btn-danger { border-radius: 10px; padding: 10px 20px; letter-spacing: 0.5px; transition: all 0.2s ease-in-out; color: white !important;}
                        .btn:hover { transform: scale(1.02); }
                        .table { border-collapse: separate; border-spacing: 0 8px; }
                        .table thead th { border-bottom: none; background-color: #e0e7ff; color: #3730a3; border-radius: 8px; }
                        .table tbody tr { box-shadow: 0 2px 10px rgba(0,0,0,0.02); background: white; border-radius: 8px; transition: all 0.2s; }
                        .table tbody tr:hover { transform: scale(1.01); background-color: #f1f5f9; cursor: pointer; }
                        .table td { border-top: none; padding: 15px; vertical-align: middle; }

                        /* CSS BẢN ĐỒ */
                        #trackingModal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.85); z-index: 9999; justify-content: center; align-items: center; }
                        .map-container { background: white; width: 95%; max-width: 900px; border-radius: 15px; padding: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); animation: zoomIn 0.3s ease-out; }
                        @keyframes zoomIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
                        #realTimeMap { height: 450px; border-radius: 10px; border: 2px solid #e2e8f0; }
                    </style>

                    @if(Auth::user()->email === 'admin@gmail.com')
                        <style>
                            .badge-shop { background-color: #6f42c1; font-size: 0.85rem; padding: 5px 10px; border-radius: 6px;}
                            #loadingOverlay, #aiInvoiceModal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.75); z-index: 9999; justify-content: center; align-items: center; color: white; text-align: center; }
                            .invoice-box { background: white; width: 90%; max-width: 450px; border-radius: 12px; padding: 25px; color: #333; text-align: left; animation: slideDown 0.3s ease-out;}
                            @keyframes slideDown { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
                            .invoice-header { text-align: center; border-bottom: 2px dashed #ccc; padding-bottom: 15px; margin-bottom: 15px; }
                            .invoice-header h3 { color: #0d6efd; margin: 0; font-weight: 800; font-size: 1.5rem;}
                            .invoice-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 1rem; }
                            .invoice-row .label { font-weight: bold; color: #555; }
                            .fee-row { background: #fff3cd; padding: 8px 10px; border-radius: 6px; margin-bottom: 8px; border-left: 4px solid #ffc107;}
                            .weather-fee-row { background: #d1e7dd; padding: 8px 10px; border-radius: 6px; margin-bottom: 8px; border-left: 4px solid #198754;}
                            .total-row { border-top: 2px solid #000; padding-top: 15px; margin-top: 15px; font-size: 1.3rem; font-weight: 900; color: #d9534f;}
                        </style>

                        <div id="pendingOrdersTable" class="container mt-2">
                            <h2 class="header-title mb-4"><i class="fa-solid fa-boxes-packing me-2 text-primary"></i> DANH SÁCH ĐƠN CHỜ ĐIỀU PHỐI</h2>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="adminTable">
                                    <thead><tr><th>Mã Yêu Cầu</th><th>Đối tác (Người Gửi)</th><th>Khách hàng (Người Nhận)</th><th>Thao tác</th></tr></thead>
                                    <tbody>
                                        @foreach($orders as $o)
                                            <tr>
                                                <td class="fw-bold text-danger">#{{ $o->id }} <span class="badge bg-danger ms-1">Mới</span></td>
                                                <td>
                                                    <span class="badge badge-shop mb-1"><i class="fa-solid fa-store"></i> Đối tác</span><br>
                                                    <strong>{{ $o->sender_name ?? 'FastLogistics' }}</strong><br>
                                                    <small><i class="fa-solid fa-location-dot text-danger"></i> {{ $o->sender_address ?? 'Kho trung tâm' }}</small>
                                                </td>
                                                <td>
                                                    <strong>{{ $o->receiver_name }}</strong><br>
                                                    <small><i class="fa-solid fa-location-dot text-primary"></i> {{ $o->receiver_address }}</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm fw-bold text-white"
                                                        onclick="openOrderForm('{{ $o->id }}', '{{ $o->sender_name }}', '{{ $o->sender_phone }}', '{{ $o->sender_address }}', '{{ $o->receiver_name }}', '{{ $o->receiver_phone }}', '{{ $o->receiver_address }}', '{{ $o->product_name }}', {{ $o->weight ?? 1 }}, '{{ $o->note ?? '' }}')">
                                                        <i class="fa-solid fa-bolt"></i> Điều Phối
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>

                        <div id="orderProcessingForm" class="container mt-2 mb-5" style="display: none;">
                            <button class="btn btn-outline-secondary mb-3 fw-bold" onclick="backToTable()"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</button>
                            <div class="card p-4 border border-primary">
                                <h2 class="text-center header-title text-primary"><i class="fa-solid fa-bolt text-warning"></i> XỬ LÝ ĐIỀU PHỐI ĐƠN HÀNG</h2>
                                <form id="adminForm">
                                    <div class="section-title text-primary"><i class="fa-solid fa-store me-2"></i> Thông tin Đối tác</div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2"><label class="form-label fw-bold">Tên Shop</label><input type="text" class="form-control bg-light" id="senderName" readonly></div>
                                        <div class="col-md-6 mb-2"><label class="form-label fw-bold">Điện thoại Shop</label><input type="text" class="form-control bg-light" id="senderPhone" readonly></div>
                                    </div>
                                    <div class="mb-3"><label class="form-label fw-bold">Địa chỉ lấy hàng</label><input type="text" class="form-control bg-light" id="senderAddress" readonly></div>

                                    <div class="section-title text-primary"><i class="fa-solid fa-house-user me-2"></i> Thông tin Khách hàng</div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2"><label class="form-label fw-bold">Tên Khách</label><input type="text" class="form-control bg-light" id="receiverName" readonly></div>
                                        <div class="col-md-6 mb-2"><label class="form-label fw-bold">Điện thoại Khách</label><input type="text" class="form-control bg-light" id="receiverPhone" readonly></div>
                                    </div>
                                    <div class="mb-3"><label class="form-label fw-bold">Địa chỉ giao</label><input type="text" class="form-control bg-light" id="receiverAddress" readonly></div>
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Hệ thống Điều phối AI - Tổng quan</h1>
        <p class="text-sm text-gray-500 mt-1">Chào mừng quay trở lại! Dưới đây là hiệu suất vận hành trong ngày hôm nay.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Doanh thu ngày</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">24.500.000đ</h3>
                <p class="text-[10px] text-green-500 font-bold mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    +12.5% so với hôm qua
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="p-3 bg-orange-50 rounded-xl text-orange-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Đơn hàng mới</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">128 đơn</h3>
                <p class="text-[10px] text-orange-500 font-bold mt-1 italic leading-none">Cần xác nhận: 12 đơn</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="p-3 bg-green-50 rounded-xl text-green-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hoàn thành</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">98.2%</h3>
                <p class="text-[10px] text-gray-400 font-medium mt-1 uppercase tracking-tighter">AI Optimization Active</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Shipper Online</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">45</h3>
                <p class="text-[10px] text-purple-500 font-bold mt-1">Sẵn sàng điều phối</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-extrabold text-gray-900">Biểu đồ tăng trưởng doanh thu</h3>
                <select class="text-sm border-gray-200 rounded-lg bg-gray-50 focus:ring-blue-500">
                    <option>7 ngày qua</option>
                    <option>30 ngày qua</option>
                </select>
            </div>
            <div class="h-[300px] w-full relative">
                <canvas id="growthChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
            <h3 class="text-lg font-extrabold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
                Thông báo quan trọng
            </h3>
            <div class="space-y-6">
                <div class="flex gap-4">
                    <div class="w-1.5 h-auto bg-red-500 rounded-full"></div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 line-clamp-1">Quá tải tại trạm trung chuyển Đà Nẵng</p>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">Lượng đơn hàng Telegram đổ về tăng 200%. AI đang điều phối lại tuyến đường.</p>
                        <span class="text-[10px] font-bold text-red-500 mt-2 block uppercase tracking-widest">Khẩn cấp</span>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-1.5 h-auto bg-blue-500 rounded-full"></div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 line-clamp-1">Bảo trì bot Telegram Shop</p>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">Dự kiến bảo trì lúc 00:00 - 02:00 ngày 17/04.</p>
                        <span class="text-[10px] font-bold text-blue-500 mt-2 block uppercase tracking-widest">Thông tin</span>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-1.5 h-auto bg-green-500 rounded-full"></div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 line-clamp-1">Top Shipper trong tuần</p>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">Lê Văn Thái đã hoàn thành 150 đơn giao hỏa tốc.</p>
                        <span class="text-[10px] font-bold text-green-500 mt-2 block uppercase tracking-widest">Khen thưởng</span>
                    </div>
                </div>
            </div>
            <button class="w-full mt-8 py-3 bg-gray-50 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-100 transition-colors">Xem tất cả thông báo</button>
        </div>
    </div>
                                    <div class="section-title text-primary"><i class="fa-solid fa-box-open me-2"></i> Thông tin Sản phẩm</div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2"><label class="form-label fw-bold">Sản phẩm</label><input type="text" class="form-control bg-light" id="productName" readonly></div>
                                        <div class="col-md-6 mb-2"><label class="form-label fw-bold">Khối lượng (gram)</label><input type="text" class="form-control bg-light" id="packageWeight" readonly></div>
                                    </div>
                                    <div class="mb-2"><label class="form-label fw-bold text-danger"><i class="fa-solid fa-comment-dots me-1"></i> Ghi chú từ Shop (AI sẽ phân tích)</label><textarea class="form-control bg-light text-danger fw-bold" id="note" rows="2" readonly></textarea></div>

                                    <div class="p-3 my-4 rounded border" style="background-color: #fff4f4; border-color: #dc3545 !important;">
                                        <label class="form-label fw-bold text-danger">Khoảng cách di chuyển (km) - Admin cần xác nhận</label>
                                        <div class="input-group">
                                            <input type="number" step="0.1" class="form-control border-danger" id="distance" required>
                                            <button class="btn btn-danger fw-bold text-white" type="button" id="calcDistanceBtn"><i class="fa-solid fa-location-dot"></i> Đo Khoảng Cách OSRM</button>
                                        </div>
                                        <small class="text-muted mt-1" id="distanceStatus">Bấm nút để tự động đo khoảng cách giữa 2 địa chỉ.</small>
                                    </div>
                                    <button type="button" id="submitBtn" class="btn btn-primary btn-lg fw-bold w-100 text-white"><i class="fa-solid fa-microchip fa-fade me-2"></i> PHÂN TÍCH AI & XUẤT HÓA ĐƠN</button>
                                </form>
                            </div>
                        </div>

                        <div id="loadingOverlay"><div><div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div><h4>Đang nạp dữ liệu Thời Tiết & gọi AI...</h4></div></div>
                        <div id="aiInvoiceModal">
                            <div class="invoice-box">
                                <div class="invoice-header"><h3>🧾 PHIẾU GIAO HÀNG AI</h3></div>
                                <div class="invoice-row"><span class="label">👤 Khách hàng:</span><span class="value" id="inv-name">---</span></div>
                                <div class="invoice-row"><span class="label">📞 Điện thoại:</span><span class="value fw-bold text-primary" id="inv-phone">---</span></div>
                                <div class="invoice-row"><span class="label">📍 Quãng đường:</span><span class="value" id="inv-distance">--- km</span></div>
                                <div style="margin: 15px 0;">
                                    <div class="invoice-row fee-row" id="row-fragile" style="display:none;"><span class="label">📦 Tính chất (<span id="inv-tag"></span>):</span><span class="value fw-bold text-danger">+ <span id="inv-fragile-fee">0</span> đ</span></div>
                                    <div class="invoice-row weather-fee-row" id="row-weather"><span class="label">🌤️ Thời tiết (<span id="inv-weather">---</span>):</span><span class="value fw-bold" id="inv-weather-fee">0 đ</span></div>
                                </div>
                                <div class="invoice-row total-row"><span class="label">TỔNG CHI PHÍ:</span><span class="value" id="inv-total">0 đ</span></div>
                                <button class="btn btn-primary w-100 mt-4 fw-bold text-white" onclick="completeOrder()"><i class="fa-solid fa-motorcycle me-2"></i> ĐÓNG HÓA ĐƠN & GỬI TÀI XẾ</button>
                            </div>
                        </div>

                        <script>
                            let currentOrderId = null;
                            function openOrderForm(id, sN, sP, sA, rN, rP, rA, pN, w, n) {
                                currentOrderId = id;
                                document.getElementById('pendingOrdersTable').style.display = 'none'; document.getElementById('orderProcessingForm').style.display = 'block';
                                document.getElementById('senderName').value=sN; document.getElementById('senderPhone').value=sP; document.getElementById('senderAddress').value=sA;
                                document.getElementById('receiverName').value=rN; document.getElementById('receiverPhone').value=rP; document.getElementById('receiverAddress').value=rA;
                                document.getElementById('productName').value=pN; document.getElementById('packageWeight').value=w; document.getElementById('note').value=n;
                                document.getElementById('distance').value=""; document.getElementById('distanceStatus').innerHTML="Sẵn sàng đo.";
                            }

                            function backToTable() {
                                document.getElementById('pendingOrdersTable').style.display = 'block';
                                document.getElementById('orderProcessingForm').style.display = 'none';
                                loadAdminTable();
                            }

                            document.getElementById("calcDistanceBtn").addEventListener("click", async function() {
                                const sA = document.getElementById("senderAddress").value, rA = document.getElementById("receiverAddress").value, sT = document.getElementById("distanceStatus");
                                sT.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang đo...'; sT.className = "text-warning mt-1";
                                try {
                                    const r1 = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(sA)}`), d1 = await r1.json();
                                    const r2 = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(rA)}`), d2 = await r2.json();
                                    const rR = await fetch(`https://router.project-osrm.org/route/v1/driving/${d1[0].lon},${d1[0].lat};${d2[0].lon},${d2[0].lat}?overview=false`), rD = await rR.json();
                                    document.getElementById("distance").value = (rD.routes[0].distance / 1000).toFixed(1); sT.innerHTML = "<i class='fa-solid fa-check'></i> Đo thành công!"; sT.className = "text-success fw-bold mt-1 d-block";
                                } catch (e) { sT.innerHTML = "Lỗi đo OSRM, hãy tự nhập km nhé sếp!"; sT.className = "text-danger mt-1"; }
                            });

                            document.getElementById("submitBtn").addEventListener("click", function() {
                                const payload = { senderName: document.getElementById('senderName').value, senderPhone: document.getElementById('senderPhone').value, senderAddress: document.getElementById('senderAddress').value, receiverName: document.getElementById('receiverName').value, receiverPhone: document.getElementById('receiverPhone').value, receiverAddress: document.getElementById('receiverAddress').value, productName: document.getElementById('productName').value, weight: parseInt(document.getElementById('packageWeight').value), distance: parseFloat(document.getElementById('distance').value), note: document.getElementById('note').value };

                                if(isNaN(payload.distance) || payload.distance === 0) { Swal.fire({ icon: 'warning', title: 'Khoan đã', text: 'Quãng đường đang bằng 0, vui lòng nhập số km hợp lệ!' }); return; }

                                document.getElementById('loadingOverlay').style.display = 'flex';

                                fetch('http://localhost:8080/api/orders/create', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
                                .then(res => res.json()).then(data => {
                                    document.getElementById('loadingOverlay').style.display = 'none';
                                    try {
                                        let aiData = typeof data.ai_message === 'string' ? JSON.parse(data.ai_message) : data.ai_message;

                                        document.getElementById("inv-name").innerText = aiData.ten_nguoi_nhan || payload.receiverName;
                                        document.getElementById("inv-phone").innerText = aiData.sdt_nhan || payload.receiverPhone || "Chưa cập nhật";
                                        document.getElementById("inv-distance").innerText = aiData.quang_duong || payload.distance;

                                        // HIỂN THỊ PHÍ DỄ VỠ (Bất chấp AI nói tiếng gì, cứ tiền > 0 là hiện)
                                        let phiDeVo = parseInt(aiData.phi_de_vo) || 0;
                                        if (phiDeVo > 0) {
                                            document.getElementById("inv-tag").innerText = "Dễ vỡ / Gấp";
                                            document.getElementById("inv-fragile-fee").innerText = phiDeVo.toLocaleString('vi-VN');
                                            document.getElementById("row-fragile").style.display = "flex";
                                        } else {
                                            document.getElementById("row-fragile").style.display = "none";
                                        }

                                        // HIỂN THỊ PHÍ THỜI TIẾT
                                        document.getElementById("row-weather").style.display = "flex";
                                        let tenThoiTiet = aiData.thoi_tiet || "Bình thường";
                                        let phiThoiTiet = parseInt(aiData.phi_thoi_tiet) || 0;

                                        document.getElementById("inv-weather").innerText = tenThoiTiet;
                                        if (phiThoiTiet > 0) {
                                            document.getElementById("inv-weather-fee").innerText = "+ " + phiThoiTiet.toLocaleString('vi-VN') + " đ";
                                            document.getElementById("inv-weather-fee").className = "value fw-bold text-danger";
                                        } else {
                                            document.getElementById("inv-weather-fee").innerText = "0 đ";
                                            document.getElementById("inv-weather-fee").className = "value fw-bold text-success";
                                        }

                                        document.getElementById("inv-total").innerText = parseInt(aiData.tong_tien || 0).toLocaleString('vi-VN') + " đ";
                                        document.getElementById('aiInvoiceModal').style.display = 'flex';
                                    } catch(e) { Swal.fire({ icon: 'error', title: 'Lỗi hiển thị', text: 'Lỗi đọc dữ liệu AI.' }); }
                                }).catch(err => { document.getElementById('loadingOverlay').style.display = 'none'; Swal.fire({ icon: 'error', title: 'Mất kết nối', text: 'Lỗi kết nối Java (Cổng 8080)!' }); });
                            });

                            function completeOrder() {
                                document.getElementById('aiInvoiceModal').style.display = 'none';
                                let orders = JSON.parse(localStorage.getItem('fakeOrders')) || [];
                                let approved = JSON.parse(localStorage.getItem('approvedOrders')) || [];

                                let orderIndex = orders.findIndex(o => o.id === currentOrderId);
                                if(orderIndex !== -1) {
                                    let orderToMove = orders[orderIndex];
                                    orderToMove.status = 'Đã nhận hàng';
                                    orderToMove.totalFee = document.getElementById("inv-total").innerText;
                                    approved.push(orderToMove);
                                    orders.splice(orderIndex, 1);

                                    localStorage.setItem('fakeOrders', JSON.stringify(orders));
                                    localStorage.setItem('approvedOrders', JSON.stringify(approved));
                                }

                                Swal.fire({ icon: 'success', title: 'Đã giao cho tài xế!', text: 'Shop đã nhận được thông báo.', showConfirmButton: false, timer: 1500 });
                                backToTable(); // Trở về bảng Admin mà không reload web
                            }
                        </script>

                    @else

                        <div class="container mt-2">
                            <div class="row">
                                <div class="col-lg-5 mb-4">
                                    <h2 class="header-title text-success mb-3 text-center"><i class="fa-solid fa-store"></i> TẠO YÊU CẦU GIAO HÀNG</h2>
                                    <p class="text-center text-muted mb-4" style="font-size: 0.9rem;">Gửi thông tin cho Admin để hệ thống điều phối</p>

                                    <div class="card p-4">
                                        <form id="shopOrderForm">

                                            <div class="section-title text-success"><i class="fa-solid fa-location-dot me-2"></i>Thông tin Cửa hàng (Tự động)</div>
                                            <div class="mb-3"><label class="form-label fw-bold">Tên Cửa hàng</label><input type="text" class="form-control bg-light" id="shop_sName" value="{{ Auth::user()->name }}" readonly></div>
                                            <div class="mb-3"><label class="form-label fw-bold">Điện thoại Shop</label><input type="text" class="form-control" id="shop_sPhone" required></div>
                                            <div class="mb-3"><label class="form-label fw-bold">Địa chỉ lấy hàng của Shop</label><input type="text" class="form-control" id="shop_sAddr" required></div>

                                            <div class="section-title text-success mt-4"><i class="fa-solid fa-house-user me-2"></i>Thông tin Khách mua hàng</div>
                                            <div class="mb-3"><label class="form-label fw-bold">Tên Khách Hàng</label><input type="text" class="form-control" id="shop_rName" required></div>
                                            <div class="mb-3"><label class="form-label fw-bold">Điện thoại Khách</label><input type="text" class="form-control" id="shop_rPhone" required></div>
                                            <div class="mb-3"><label class="form-label fw-bold">Địa chỉ giao hàng</label><input type="text" class="form-control" id="shop_rAddr" required></div>

                                            <div class="section-title text-success mt-4"><i class="fa-solid fa-box-open me-2"></i>Thông tin Sản phẩm</div>
                                            <div class="mb-3"><label class="form-label fw-bold">Tên mặt hàng</label><input type="text" class="form-control" id="shop_pName" required></div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Khối lượng (gram)</label>
                                                    <input type="number" class="form-control" id="shop_weight" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold text-danger">Loại hàng</label>
                                                    <select class="form-select border-danger" id="shop_category">
                                                        <option value="Hàng bình thường">📦 Hàng bình thường</option>
                                                        <option value="Hàng dễ vỡ">🍷 Hàng dễ vỡ</option>
                                                        <option value="Hàng cồng kềnh">🛋️ Hàng cồng kềnh</option>
                                                        <option value="Thực phẩm tươi sống">🥩 Thực phẩm tươi sống</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-4"><label class="form-label fw-bold">Ghi chú cho Admin</label><textarea class="form-control" id="shop_note" rows="2" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea></div>

                                            <button type="button" class="btn btn-success btn-lg fw-bold w-100 text-white" onclick="submitShopOrder()"><i class="fa-solid fa-paper-plane me-2"></i> GỬI YÊU CẦU LÊN HỆ THỐNG</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="col-lg-7">
                                    <h2 class="header-title text-primary mb-3"><i class="fa-solid fa-motorcycle"></i> ĐƠN HÀNG ĐANG GIAO</h2>
                                    <div class="card p-4 border-top border-primary border-4" style="min-height: 500px;">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle" id="shopApprovedTable">
                                                <thead class="table-primary">
                                                    <tr><th>Mã Đơn</th><th>Khách Nhận</th><th>Trạng thái</th><th>Hành Trình</th></tr>
                                                </thead>
                                                <tbody>
                                                    </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="trackingModal">
                            <div class="map-container">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h3 class="text-primary fw-bold m-0"><i class="fa-solid fa-route me-2"></i> LIVE TRACKING: Hành Trình Shipper</h3>
                                    <button class="btn-close" onclick="closeMap()"></button>
                                </div>
                                <div id="realTimeMap"></div>
                                <button class="btn btn-danger w-100 mt-4 fw-bold text-white" onclick="closeMap()"><i class="fa-solid fa-xmark me-2"></i> ĐÓNG BẢN ĐỒ</button>
                            </div>
                        </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('growthChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Th 2', 'Th 3', 'Th 4', 'Th 5', 'Th 6', 'Th 7', 'CN'],
                datasets: [{
                    label: 'Doanh thu (Triệu đồng)',
                    data: [12, 19, 15, 25, 22, 30, 24.5],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</x-app-layout>
