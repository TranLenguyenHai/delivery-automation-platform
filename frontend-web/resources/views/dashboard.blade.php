<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(Auth::user()->email === 'admin@gmail.com')
                {{ __('Quản Trị Hệ Thống Điều Phối Logistics AI') }}
            @else
                {{ __('Cổng Quản Lý Đơn Hàng Của Đối Tác') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style> .section-title { color: #0d6efd; font-size: 1.1rem; font-weight: bold; border-bottom: 2px solid #e9ecef; padding-bottom: 8px; margin-bottom: 16px; margin-top: 24px; } .header-title { color: #2c3e50; font-weight: 800; } </style>

                    @if(Auth::user()->email === 'admin@gmail.com')
                        <style>
                            .table-hover tbody tr:hover { background-color: #f8f9fa; cursor: pointer; }
                            .badge-shop { background-color: #6f42c1; font-size: 0.85rem; padding: 5px 10px; border-radius: 6px;}
                            #loadingOverlay, #aiInvoiceModal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.75); z-index: 9999; justify-content: center; align-items: center; color: white; text-align: center; }
                            .invoice-box { background: white; width: 90%; max-width: 450px; border-radius: 12px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative; color: #333; text-align: left;}
                            .invoice-header { text-align: center; border-bottom: 2px dashed #ccc; padding-bottom: 15px; margin-bottom: 15px; }
                            .invoice-header h3 { color: #0d6efd; margin: 0; font-weight: 800; font-size: 1.5rem;}
                            .invoice-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 1rem; }
                            .invoice-row .label { font-weight: bold; color: #555; }
                            .total-row { border-top: 2px solid #000; padding-top: 15px; margin-top: 15px; font-size: 1.3rem; font-weight: 900; color: #d9534f;}
                        </style>

                        <div id="pendingOrdersTable" class="container mt-2">
                            <h2 class="header-title mb-4">📦 DANH SÁCH ĐƠN CHỜ ĐIỀU PHỐI</h2>
                            <table class="table table-bordered table-hover align-middle" id="adminTable">
                                <thead class="table-primary">
                                    <tr><th>Mã Yêu Cầu</th><th>Đối tác (Người Gửi)</th><th>Khách hàng (Người Nhận)</th><th>Thao tác</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold text-muted">MẪU-001</td>
                                        <td><span class="badge badge-shop mb-1">Cửa hàng Máy Tính</span><br><strong>Laptop Vũ Gia</strong><br><small>18 Võ Chí Công, Đà Nẵng</small></td>
                                        <td><strong>Vyy</strong><br><small>480 Võ Chí Công, Đà Nẵng</small></td>
                                        <td><button class="btn btn-primary btn-sm fw-bold" onclick="openOrderForm('Laptop Vũ Gia', '0901111111', '18 võ chí công', 'Vyy', '098767787113', '480 võ chí công', 'máy tính', 1000, 'Hàng dễ vỡ')">⚡ Điều Phối</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="orderProcessingForm" class="container mt-2 mb-5" style="display: none;">
                            <button class="btn btn-outline-secondary mb-3 fw-bold" onclick="backToTable()">🔙 Quay lại danh sách</button>
                            <div class="card p-4 border border-primary">
                                <h2 class="text-center header-title text-primary">⚡ XỬ LÝ ĐIỀU PHỐI ĐƠN HÀNG</h2>
                                <form id="adminForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-2"><label class="form-label fw-bold">Tên Shop</label><input type="text" class="form-control bg-light" id="senderName" readonly></div>
                                        <div class="col-md-6 mb-2"><label class="form-label fw-bold">Địa chỉ lấy</label><input type="text" class="form-control bg-light" id="senderAddress" readonly></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2"><label class="form-label fw-bold">Tên Khách</label><input type="text" class="form-control bg-light" id="receiverName" readonly></div>
                                        <div class="col-md-6 mb-2"><label class="form-label fw-bold">Địa chỉ giao</label><input type="text" class="form-control bg-light" id="receiverAddress" readonly></div>
                                    </div>
                                    <input type="hidden" id="senderPhone"><input type="hidden" id="receiverPhone"><input type="hidden" id="productName"><input type="hidden" id="packageWeight"><input type="hidden" id="note">

                                    <div class="p-3 my-4 rounded border" style="background-color: #fff4f4; border-color: #dc3545 !important;">
                                        <label class="form-label fw-bold text-danger">Khoảng cách di chuyển (km) - Admin cần xác nhận</label>
                                        <div class="input-group">
                                            <input type="number" step="0.1" class="form-control border-danger" id="distance" required>
                                            <button class="btn btn-danger fw-bold" type="button" id="calcDistanceBtn">Đo Khoảng Cách OSRM 🚀</button>
                                        </div>
                                        <small class="text-muted mt-1" id="distanceStatus">Bấm nút để tự động đo khoảng cách giữa 2 địa chỉ.</small>
                                    </div>
                                    <button type="button" id="submitBtn" class="btn btn-primary btn-lg fw-bold w-100">🤖 PHÂN TÍCH AI & XUẤT HÓA ĐƠN</button>
                                </form>
                            </div>
                        </div>

                        <div id="loadingOverlay"><div><div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div><h4>Đang phân tích AI...</h4></div></div>
                        <div id="aiInvoiceModal">
                                                    <div class="invoice-box">
                                                        <div class="invoice-header"><h3>🧾 PHIẾU GIAO HÀNG AI</h3></div>

                                                        <div class="invoice-row"><span class="label">👤 Khách hàng:</span><span class="value" id="inv-name">---</span></div>
                                                        <div class="invoice-row"><span class="label">📞 Điện thoại:</span><span class="value fw-bold text-primary" id="inv-phone">---</span></div>
                                                        <div class="invoice-row"><span class="label">📍 Quãng đường:</span><span class="value" id="inv-distance">--- km</span></div>

                                                        <div style="margin: 15px 0;">
                                                            <div class="invoice-row fee-row" id="row-fragile" style="display:none;">
                                                                <span class="label">📦 Tính chất (<span id="inv-tag"></span>):</span>
                                                                <span class="value fw-bold text-danger">+ <span id="inv-fragile-fee">0</span> đ</span>
                                                            </div>
                                                            <div class="invoice-row weather-fee-row" id="row-weather">
                                                                <span class="label">🌤️ Thời tiết (<span id="inv-weather">---</span>):</span>
                                                                <span class="value fw-bold" id="inv-weather-fee">0 đ</span>
                                                            </div>
                                                        </div>

                                                        <div class="invoice-row total-row"><span class="label">TỔNG CHI PHÍ:</span><span class="value" id="inv-total">0 đ</span></div>
                                                        <button class="btn btn-primary w-100 mt-3 fw-bold" onclick="document.getElementById('aiInvoiceModal').style.display='none'">ĐÓNG HÓA ĐƠN & GỬI TÀI XẾ</button>
                                                    </div>
                                                </div>

                        <script>
                            // TỰ ĐỘNG ĐỌC ĐƠN HÀNG TỪ SHOP KHI ADMIN ĐĂNG NHẬP
                            window.onload = function() {
                                let orders = JSON.parse(localStorage.getItem('fakeOrders')) || [];
                                let tbody = document.querySelector('#adminTable tbody');
                                orders.forEach(o => {
                                    let tr = document.createElement('tr');
                                    tr.className = 'table-warning';
                                    tr.innerHTML = `
                                        <td class="fw-bold text-danger">${o.id}</td>
                                        <td><span class="badge bg-danger mb-1">Vừa đặt</span><br><strong>${o.sName}</strong><br><small>${o.sAddr}</small></td>
                                        <td><strong>${o.rName}</strong><br><small>${o.rAddr}</small></td>
                                        <td><button class="btn btn-primary btn-sm fw-bold" onclick="openOrderForm('${o.sName}', '${o.sPhone}', '${o.sAddr}', '${o.rName}', '${o.rPhone}', '${o.rAddr}', '${o.pName}', ${o.weight}, '${o.note}')">⚡ Điều Phối</button></td>
                                    `;
                                    tbody.prepend(tr); // Nhét đơn mới nhất lên đầu
                                });
                            };

                            function openOrderForm(sN, sP, sA, rN, rP, rA, pN, w, n) {
                                document.getElementById('pendingOrdersTable').style.display = 'none'; document.getElementById('orderProcessingForm').style.display = 'block';
                                document.getElementById('senderName').value=sN; document.getElementById('senderPhone').value=sP; document.getElementById('senderAddress').value=sA;
                                document.getElementById('receiverName').value=rN; document.getElementById('receiverPhone').value=rP; document.getElementById('receiverAddress').value=rA;
                                document.getElementById('productName').value=pN; document.getElementById('packageWeight').value=w; document.getElementById('note').value=n;
                                document.getElementById('distance').value=""; document.getElementById('distanceStatus').innerHTML="Sẵn sàng đo.";
                            }
                            function backToTable() { document.getElementById('pendingOrdersTable').style.display = 'block'; document.getElementById('orderProcessingForm').style.display = 'none'; }

                            // Nút đo khoảng cách Admin
                            document.getElementById("calcDistanceBtn").addEventListener("click", async function() {
                                const sA = document.getElementById("senderAddress").value, rA = document.getElementById("receiverAddress").value, sT = document.getElementById("distanceStatus");
                                sT.innerHTML = "Đang đo..."; sT.className = "text-warning";
                                try {
                                    const r1 = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(sA)}`), d1 = await r1.json();
                                    const r2 = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(rA)}`), d2 = await r2.json();
                                    const rR = await fetch(`https://router.project-osrm.org/route/v1/driving/${d1[0].lon},${d1[0].lat};${d2[0].lon},${d2[0].lat}?overview=false`), rD = await rR.json();
                                    document.getElementById("distance").value = (rD.routes[0].distance / 1000).toFixed(1); sT.innerHTML = "✅ Đo xong!"; sT.className = "text-success fw-bold mt-1 d-block";
                                } catch (e) { sT.innerHTML = "Lỗi đo, nhập tay đi sếp!"; sT.className = "text-danger"; }
                            });

                            // Nút gửi AI Admin
                            // Nút gửi AI Admin (ĐÃ FIX SĐT VÀ THỜI TIẾT)
                                                        document.getElementById("submitBtn").addEventListener("click", function() {
                                                            const payload = { senderName: document.getElementById('senderName').value, senderPhone: document.getElementById('senderPhone').value, senderAddress: document.getElementById('senderAddress').value, receiverName: document.getElementById('receiverName').value, receiverPhone: document.getElementById('receiverPhone').value, receiverAddress: document.getElementById('receiverAddress').value, productName: document.getElementById('productName').value, weight: parseInt(document.getElementById('packageWeight').value), distance: parseFloat(document.getElementById('distance').value), note: document.getElementById('note').value };

                                                            if(isNaN(payload.distance)) return alert("Vui lòng đo khoảng cách trước!");
                                                            document.getElementById('loadingOverlay').style.display = 'flex';

                                                            fetch('http://localhost:8080/api/orders/create', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
                                                            .then(res => res.json()).then(data => {
                                                                document.getElementById('loadingOverlay').style.display = 'none';
                                                                try {
                                                                    let aiData = typeof data.ai_message === 'string' ? JSON.parse(data.ai_message) : data.ai_message;

                                                                    // 1. GÁN TÊN, SĐT, QUÃNG ĐƯỜNG
                                                                    document.getElementById("inv-name").innerText = aiData.ten_nguoi_nhan || payload.receiverName;
                                                                    // Lấy SĐT từ n8n gửi về, nếu ko có thì lấy từ Form Admin
                                                                    document.getElementById("inv-phone").innerText = aiData.sdt_nhan || payload.receiverPhone || "Chưa cập nhật";
                                                                    document.getElementById("inv-distance").innerText = aiData.quang_duong || payload.distance;

                                                                    // 2. GÁN PHÍ DỄ VỠ (Hiện/Ẩn)
                                                                    if (aiData.tinh_chat_hang && aiData.tinh_chat_hang !== "NORMAL" && parseInt(aiData.phi_de_vo) > 0) {
                                                                        document.getElementById("inv-tag").innerText = "Dễ vỡ";
                                                                        document.getElementById("inv-fragile-fee").innerText = parseInt(aiData.phi_de_vo).toLocaleString('vi-VN');
                                                                        document.getElementById("row-fragile").style.display = "flex";
                                                                    } else {
                                                                        document.getElementById("row-fragile").style.display = "none";
                                                                    }

                                                                    // 3. XỬ LÝ THỜI TIẾT (LUÔN LUÔN HIỆN)
                                                                    document.getElementById("row-weather").style.display = "flex"; // Lệnh gỡ tàng hình ở đây!

                                                                    let tenThoiTiet = aiData.thoi_tiet || "Bình thường";
                                                                    let phiThoiTiet = parseInt(aiData.phi_thoi_tiet) || 0;

                                                                    document.getElementById("inv-weather").innerText = tenThoiTiet; // Sẽ hiện "mây rải rác" từ n8n

                                                                    if (phiThoiTiet > 0) {
                                                                        // Thời tiết xấu -> Bị phạt tiền -> Chữ đỏ
                                                                        document.getElementById("inv-weather-fee").innerText = "+ " + phiThoiTiet.toLocaleString('vi-VN') + " đ";
                                                                        document.getElementById("inv-weather-fee").className = "value fw-bold text-danger";
                                                                    } else {
                                                                        // Thời tiết đẹp -> Free -> Chữ xanh lá cây
                                                                        document.getElementById("inv-weather-fee").innerText = "0 đ";
                                                                        document.getElementById("inv-weather-fee").className = "value fw-bold text-success";
                                                                    }

                                                                    // 4. TÍNH TỔNG VÀ MỞ BẢNG
                                                                    document.getElementById("inv-total").innerText = parseInt(aiData.tong_tien || 0).toLocaleString('vi-VN') + " đ";
                                                                    document.getElementById('aiInvoiceModal').style.display = 'flex';
                                                                } catch(e) {
                                                                    alert("Đã nhận dữ liệu nhưng lỗi hiển thị!");
                                                                    console.error(e);
                                                                }
                                                            }).catch(err => {
                                                                document.getElementById('loadingOverlay').style.display = 'none';
                                                                alert("❌ Lỗi kết nối với Backend Java!");
                                                            });
                                                        });
                        </script>

                    @else

                        <div class="container mt-2">
                            <h2 class="text-center header-title text-success mb-2">🏪 TẠO YÊU CẦU GIAO HÀNG</h2>
                            <p class="text-center text-muted mb-4">Nhập thông tin đơn hàng để hệ thống Admin điều phối</p>

                            <form id="shopOrderForm">
                                <div class="section-title text-success">📍 Thông tin Cửa hàng (Tự động)</div>
                                <div class="row">
                                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Tên Cửa hàng</label><input type="text" class="form-control bg-light" id="shop_sName" value="{{ Auth::user()->name }}" readonly></div>
                                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Điện thoại Shop</label><input type="text" class="form-control" id="shop_sPhone" required></div>
                                </div>
                                <div class="mb-3"><label class="form-label fw-bold">Địa chỉ lấy hàng của Shop</label><input type="text" class="form-control" id="shop_sAddr" required></div>

                                <div class="section-title text-success">🏠 Thông tin Khách mua hàng</div>
                                <div class="row">
                                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Tên khách hàng</label><input type="text" class="form-control" id="shop_rName" required></div>
                                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Điện thoại khách</label><input type="text" class="form-control" id="shop_rPhone" required></div>
                                </div>
                                <div class="mb-3"><label class="form-label fw-bold">Địa chỉ giao hàng</label><input type="text" class="form-control" id="shop_rAddr" required></div>

                                <div class="section-title text-success">📦 Thông tin Sản phẩm</div>
                                <div class="row">
                                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Tên mặt hàng</label><input type="text" class="form-control" id="shop_pName" required></div>
                                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Khối lượng (gram)</label><input type="number" class="form-control" id="shop_weight" required></div>
                                </div>
                                <div class="mb-3">
                                                                <label class="form-label fw-bold text-danger">Loại hàng hóa</label>
                                                                <select class="form-select border-danger" id="shop_category">
                                                                    <option value="Hàng bình thường">📦 Hàng bình thường</option>
                                                                    <option value="Hàng dễ vỡ">🍷 Hàng dễ vỡ (Thủy tinh, gốm sứ...)</option>
                                                                    <option value="Hàng cồng kềnh">🛋️ Hàng cồng kềnh</option>
                                                                    <option value="Thực phẩm tươi sống">🥩 Thực phẩm tươi sống</option>
                                                                </select>
                                </div>
                                <div class="mb-4"><label class="form-label fw-bold">Ghi chú cho Admin</label><textarea class="form-control" id="shop_note" rows="2" placeholder="Ví dụ: Gọi khách trước khi giao..."></textarea></div>

                                <button type="button" class="btn btn-success btn-lg fw-bold w-100" onclick="submitShopOrder()">🚀 GỬI YÊU CẦU LÊN HỆ THỐNG</button>
                            </form>
                        </div>

                        <script>
                                                    function submitShopOrder() {
                                                        // Gộp Loại hàng và Ghi chú lại thành 1 câu cho AI dễ đọc
                                                        const loaiHang = document.getElementById('shop_category').value;
                                                        const ghiChuGoc = document.getElementById('shop_note').value;
                                                        const ghiChuChoAI = `[Phân loại: ${loaiHang}] - Ghi chú thêm: ${ghiChuGoc}`;

                                                        const newOrder = {
                                                            id: 'REQ-' + Math.floor(Math.random() * 10000),
                                                            sName: document.getElementById('shop_sName').value,
                                                            sPhone: document.getElementById('shop_sPhone').value,
                                                            sAddr: document.getElementById('shop_sAddr').value,
                                                            rName: document.getElementById('shop_rName').value,
                                                            rPhone: document.getElementById('shop_rPhone').value,
                                                            rAddr: document.getElementById('shop_rAddr').value,
                                                            pName: document.getElementById('shop_pName').value,
                                                            weight: document.getElementById('shop_weight').value,
                                                            note: ghiChuChoAI // Gửi cái ghi chú đã gộp sang cho Admin
                                                        };

                                                        if(!newOrder.sPhone || !newOrder.rName || !newOrder.rAddr) {
                                                            alert("Vui lòng điền đầy đủ thông tin!");
                                                            return;
                                                        }

                                                        let orders = JSON.parse(localStorage.getItem('fakeOrders')) || [];
                                                        orders.push(newOrder);
                                                        localStorage.setItem('fakeOrders', JSON.stringify(orders));

                                                        alert("✅ Đã gửi yêu cầu thành công!\n\nĐơn hàng của bạn đã được chuyển đến màn hình của Admin.");
                                                        document.getElementById("shopOrderForm").reset();
                                                    }
                        </script>

                    @endif
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>
