<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng doanh thu</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">{{ number_format($todayRevenue, 0, ',', '.') }}đ</h3>
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
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng đơn hàng</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">{{ $newOrdersCount }} đơn</h3>
                <p class="text-[10px] text-orange-500 font-bold mt-1 italic leading-none text-right">Chờ điều phối: {{ $pendingCount }} đơn</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="p-3 bg-green-50 rounded-xl text-green-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hoàn thành</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">{{ $completionRate }}%</h3>
                <p class="text-[10px] text-gray-400 font-medium mt-1 uppercase tracking-tighter">AI Optimization Active</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Shipper Online</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">{{ $shipperCount }}</h3>
                <p class="text-[10px] text-purple-500 font-bold mt-1">Sẵn sàng điều phối</p>
            </div>
        </div>
    </div>


    <div class="bg-gradient-to-br from-indigo-900 to-blue-900 p-1 rounded-3xl shadow-lg mb-8">
        <div class="bg-white p-6 md:p-8 rounded-[22px] h-full">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h3 class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        TRẠM TỐI ƯU LỘ TRÌNH AI (VRP)
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Chọn các đơn hàng cần đi giao để AI tự động gom chuyến và vẽ đường.</p>
                </div>
                <button id="btnOptimize" onclick="runAIOptimization()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl shadow-lg transition-all flex items-center gap-2 hidden">
                    <span id="btnText">🚀 Kích hoạt thuật toán AI</span>
                    <svg id="btnSpinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden max-h-64 overflow-y-auto mb-6">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 sticky top-0 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="p-3 w-10 text-center"><input type="checkbox" id="selectAllAi" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"></th>
                            <th class="p-3">Mã đơn</th>
                            <th class="p-3">Hàng hóa</th>
                            <th class="p-3">Khối lượng</th>
                            <th class="p-3">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody id="ai-order-table-body">
                        @foreach($orders->where('status', 'Chờ điều phối') as $order)
                        <tr class="border-t border-gray-100 hover:bg-blue-50/50 transition-colors">
                            <td class="p-3 text-center"><input type="checkbox" class="ai-order-cb rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $order->id }}"></td>
                            <td class="p-3 font-bold text-gray-800">#REQ-{{ $order->id }}</td>
                            <td class="p-3">{{ $order->product_name }}</td>
                            <td class="p-3"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">{{ $order->weight }} gram</span></td>
                            <td class="p-3 text-xs text-red-500">{{ $order->note }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modal Phiếu Giao Hàng AI (Receipt Style) -->
            <div id="aiReceiptModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-black bg-opacity-60 transition-opacity" aria-hidden="true" onclick="closeAIModal()"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border-t-[12px] border-blue-600">
                        <div class="bg-white px-8 py-10">
                            <div class="text-center mb-6">
                                <h3 class="text-2xl font-black text-blue-600 tracking-tight" id="modal-title">🧾 PHIẾU GIAO HÀNG AI</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Hệ thống phân tích thông minh</p>
                            </div>
                            <div class="border-t-2 border-dashed border-gray-100 my-8"></div>
                            <div id="aiReceiptBody" class="space-y-8"></div>
                            <div class="border-t-2 border-dashed border-gray-100 my-8"></div>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-sm font-black text-gray-800 uppercase tracking-wider">TỔNG CHI PHÍ:</span>
                                <span id="modalTotalFee" class="text-3xl font-black text-red-600 tracking-tighter">0đ</span>
                            </div>
                            <p class="text-center text-xs italic text-green-600 font-bold mb-10 opacity-80">Cảm ơn bạn đã tin tưởng dịch vụ của chúng tôi! 💖</p>
                            <button onclick="closeAIModal()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-blue-100 transition-all transform active:scale-95 uppercase tracking-widest text-sm text-center">Đóng hóa đơn</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BẢNG KẾT QUẢ CHI TIẾT (HIỆN SAU KHI CHẠY AI) -->
    <div id="aiResultContainer" class="{{ (isset($optimizedOrders) && count($optimizedOrders) > 0) ? '' : 'hidden' }} animate-fade-in mb-8">
        <h3 class="text-lg font-black text-blue-600 mb-4 flex items-center gap-2">
            <span class="p-2 bg-blue-100 rounded-lg text-blue-600">📊</span> CHI TIẾT PHÂN TÍCH AI LOGISTICS
        </h3>
        <div class="bg-white rounded-3xl shadow-xl border border-blue-100 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-blue-600 text-white font-bold uppercase text-[10px] tracking-widest">
                    <tr>
                        <th class="p-4">Mã Đơn</th>
                        <th class="p-4">Người Nhận</th>
                        <th class="p-4">Quãng Đường</th>
                        <th class="p-4">Phí Mưa</th>
                        <th class="p-4">Phí Dễ Vỡ</th>
                        <th class="p-4">Tổng Phí AI</th>
                    </tr>
                </thead>
                <tbody id="aiResultTableBody" class="divide-y divide-gray-50">
                    @if(isset($optimizedOrders) && count($optimizedOrders) > 0)
                        @foreach($optimizedOrders as $item)
                        <tr class="hover:bg-blue-50 transition-colors font-bold border-b border-gray-50">
                            <td class="p-4 text-blue-600">#REQ-{{ $item->id }}</td>
                            <td class="p-4">
                                <span class="text-gray-900">{{ $item->receiver_name ?? 'Khách hàng' }}</span><br>
                                <span class="text-[10px] text-gray-400 font-normal italic">{{ $item->receiver_phone ?? '09xxxxxxxx' }}</span>
                            </td>
                            <td class="p-4 text-gray-600">{{ $item->distance_km ?? 17 }} km</td>
                            <td class="p-4 text-cyan-600 text-[10px]">0đ <span class="font-normal text-gray-400">(Trời nắng)</span></td>
                            <td class="p-4 text-orange-600 text-[10px]">0đ <span class="font-normal text-gray-400">(Bình thường)</span></td>
                            <td class="p-4 text-red-600 text-lg font-black">{{ number_format($item->shipping_fee, 0, ',', '.') }}đ</td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- BẢNG LỊCH SỬ GIAO NHẬN (CẬP NHẬT KHI BÀN GIAO) -->
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 mb-8">
        <h3 class="text-lg font-extrabold text-gray-900 mb-6 flex items-center gap-2">
            <span class="p-2 bg-green-100 rounded-lg text-green-600">🚚</span> LỊCH SỬ GIAO NHẬN GẦN ĐÂY
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-gray-400 font-bold uppercase text-[10px] tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="py-3 px-2">Mã đơn</th>
                        <th class="py-3 px-2">Người nhận</th>
                        <th class="py-3 px-2">Trạng thái</th>
                        <th class="py-3 px-2">ĐVVC</th>
                        <th class="py-3 px-2 text-right">Phí Ship</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentDeliveries as $delivery)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2 font-black text-blue-600 italic">#REQ-{{ $delivery->id }}</td>
                        <td class="py-4 px-2 font-bold text-gray-700">{{ $delivery->receiver_name }}</td>
                        <td class="py-4 px-2">
                            <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase {{ $delivery->status === 'Giao thành công' ? 'bg-green-100 text-green-700' : 'bg-indigo-100 text-indigo-700' }}">
                                {{ $delivery->status }}
                            </span>
                        </td>
                        <td class="py-4 px-2 text-gray-500 font-medium">{{ $delivery->shipper ?? 'N/A' }}</td>
                        <td class="py-4 px-2 text-right font-black text-gray-900">{{ number_format($delivery->shipping_fee, 0, ',', '.') }}đ</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-400 italic">Chưa có lịch sử bàn giao đơn hàng hôm nay.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('growthChart').getContext('2d');
        new Chart(ctx, { type: 'line', data: { labels: ['Th 2', 'Th 3', 'Th 4', 'Th 5', 'Th 6', 'Th 7', 'CN'], datasets: [{ label: 'Doanh thu', data: [12, 19, 15, 25, 22, 30, 24.5], borderColor: '#2563eb', backgroundColor: 'rgba(37, 99, 235, 0.1)', fill: true, tension: 0.4, borderWidth: 3 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } } });
    </script>

    <script>
        const btnOptimize = document.getElementById('btnOptimize');
        const selectAllAi = document.getElementById('selectAllAi');
        
        function updateOptimizeButton() {
            const checkedCount = document.querySelectorAll('.ai-order-cb:checked').length;
            if (checkedCount > 0) {
                btnOptimize.classList.remove('hidden');
            } else {
                btnOptimize.classList.add('hidden');
            }
        }

        selectAllAi.addEventListener('change', function() {
            document.querySelectorAll('.ai-order-cb').forEach(cb => cb.checked = this.checked);
            updateOptimizeButton();
        });

        document.getElementById('ai-order-table-body').addEventListener('change', function(e) {
            if (e.target.classList.contains('ai-order-cb')) {
                updateOptimizeButton();
            }
        });

        function runAIOptimization() {
            let selectedIds = Array.from(document.querySelectorAll('.ai-order-cb:checked')).map(cb => cb.value);
            
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            btnOptimize.disabled = true;
            btnText.innerText = "Vui lòng chờ AI phân tích...";
            btnSpinner.classList.remove('hidden');

            fetch('/orders/optimize', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ids: selectedIds })
            })
            .then(response => response.json())
            .then(res => {
                btnOptimize.disabled = false;
                btnText.innerText = "🚀 Kích hoạt Thuật toán AI";
                btnSpinner.classList.add('hidden');

                if (res.success && res.data) {
                    renderResultTable(res.data);
                    showAIReceiptModal(res.data);
                }
                
                // Nếu có lỗi (ví dụ n8n trả về 404 hoặc timeout)
                if (res.errors && res.errors.length > 0) {
                    alert("⚠️ Cảnh báo AI:\n" + res.errors.join("\n"));
                } else if (!res.success) {
                    alert(res.message || "❌ Không nhận được phản hồi từ AI!");
                }
            })
            .catch(err => {
                btnOptimize.disabled = false;
                btnSpinner.classList.add('hidden');
                console.error("Connection Error:", err);
                alert("🔴 Lỗi hệ thống: Không thể kết nối đến AI. Vui lòng kiểm tra lại ngrok hoặc server n8n!");
            });
        }

        function renderResultTable(data) {
            const container = document.getElementById('aiResultContainer');
            const tbody = document.getElementById('aiResultTableBody');
            tbody.innerHTML = '';
            
            data.forEach(item => {
                const weatherText = item.phi_thoi_tiet > 0 ? `+${new Intl.NumberFormat('vi-VN').format(item.phi_thoi_tiet)}đ` : '0đ';
                const weatherNote = item.phi_thoi_tiet > 0 ? `(Trời mưa)` : `(${item.thoi_tiet || 'Trời nắng'})`;
                
                const fragileText = item.phi_de_vo > 0 ? `+${new Intl.NumberFormat('vi-VN').format(item.phi_de_vo)}đ` : '0đ';
                const fragileNote = item.phi_de_vo > 0 ? `(Đồ dễ vỡ)` : `(Bình thường)`;

                tbody.innerHTML += `
                <tr class="hover:bg-blue-50 transition-colors font-bold border-b border-gray-50">
                    <td class="p-4 text-blue-600">${item.ma_don}</td>
                    <td class="p-4">
                        <span class="text-gray-900">${item.ten_nguoi_nhan}</span><br>
                        <span class="text-[10px] text-gray-400 font-normal italic">${item.sdt_nhan}</span>
                    </td>
                    <td class="p-4 text-gray-600">${item.quang_duong} km</td>
                    <td class="p-4 text-cyan-600 text-[10px]">${weatherText} <span class="font-normal text-gray-400">${weatherNote}</span></td>
                    <td class="p-4 text-orange-600 text-[10px]">${fragileText} <span class="font-normal text-gray-400">${fragileNote}</span></td>
                    <td class="p-4 text-red-600 text-lg font-black">${new Intl.NumberFormat('vi-VN').format(item.cuoc_phi)}đ</td>
                </tr>`;
            });
            
            container.classList.remove('hidden');
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Xóa các dòng đã chọn khỏi bảng trên
            let checkedCbs = document.querySelectorAll('.ai-order-cb:checked');
            checkedCbs.forEach(cb => {
                cb.closest('tr').remove();
            });
            updateOptimizeButton();
        }

        function showAIReceiptModal(data) {
            const modal = document.getElementById('aiReceiptModal');
            const body = document.getElementById('aiReceiptBody');
            const totalFeeEl = document.getElementById('modalTotalFee');
            
            body.innerHTML = '';
            let totalFee = 0;

            data.forEach(item => {
                totalFee += parseInt(item.cuoc_phi);
                const section = `
                <div class="border-l-4 border-blue-600 pl-4 py-1">
                    <p class="text-xs text-gray-400 uppercase font-black mb-1">${item.ma_don} - ${item.ten_hang || 'Sản phẩm'}</p>
                    <div class="space-y-1">
                        <p class="text-sm">👤 <b>Người nhận:</b> ${item.ten_nguoi_nhan}</p>
                        <p class="text-sm">📍 <b>Lộ trình:</b> ${item.quang_duong} km (${item.thoi_tiet})</p>
                        <p class="text-sm font-black text-blue-600 mt-2">Phí AI: ${new Intl.NumberFormat('vi-VN').format(item.cuoc_phi)}đ</p>
                    </div>
                </div>`;
                body.insertAdjacentHTML('beforeend', section);
            });

            totalFeeEl.innerText = new Intl.NumberFormat('vi-VN').format(totalFee) + 'đ';
            modal.classList.remove('hidden');
        }

        function closeAIModal() {
            document.getElementById('aiReceiptModal').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', updateOptimizeButton);
    </script>
    <x-chatbot />
    <script src="{{ asset('js/chatbot.js') }}?v={{ time() }}"></script>
</x-app-layout>
