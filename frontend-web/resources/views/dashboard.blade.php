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
                <button id="btnOptimize" onclick="runAIOptimization()" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition-all flex items-center gap-2">
                    <span id="btnText">🚀 Kích hoạt Thuật toán AI</span>
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
                    <tbody>
                        @foreach($orders->whereIn('status', ['Chờ điều phối', 'Chờ in đơn', 'Chờ lấy hàng']) as $order)
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

            <div id="aiResultContainer" class="hidden border-t-2 border-dashed border-gray-200 pt-6 mt-2">
                <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex gap-4 mb-6">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 text-white shadow-md">🤖</div>
                    <div id="aiMessage" class="text-sm text-blue-900 leading-relaxed self-center">
                        </div>
                </div>

                <h4 class="text-md font-bold text-gray-800 mb-4 uppercase tracking-wider">📦 Chi tiết các chuyến xe tối ưu</h4>
                <div id="aiInvoicesGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    </div>
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
                </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('growthChart').getContext('2d');
        new Chart(ctx, { type: 'line', data: { labels: ['Th 2', 'Th 3', 'Th 4', 'Th 5', 'Th 6', 'Th 7', 'CN'], datasets: [{ label: 'Doanh thu', data: [12, 19, 15, 25, 22, 30, 24.5], borderColor: '#2563eb', backgroundColor: 'rgba(37, 99, 235, 0.1)', fill: true, tension: 0.4, borderWidth: 3 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } } });
    </script>


    <script>
        // Xử lý nút Chọn tất cả
        document.getElementById('selectAllAi').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.ai-order-cb');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        function runAIOptimization() {
            let selectedIds = [];
            document.querySelectorAll('.ai-order-cb:checked').forEach(cb => {
                selectedIds.push(cb.value);
            });

            if (selectedIds.length === 0) {
                alert("Sếp phải chọn ít nhất 1 đơn hàng để AI làm việc chứ!");
                return;
            }

            // Hiển thị hiệu ứng Loading
            let btn = document.getElementById('btnOptimize');
            let btnText = document.getElementById('btnText');
            let btnSpinner = document.getElementById('btnSpinner');

            btn.disabled = true;
            btnText.innerText = "AI đang tính toán...";
            btnSpinner.classList.remove('hidden');
            document.getElementById('aiResultContainer').classList.add('hidden');

            // Bắn API gọi n8n
            fetch('/orders/optimize', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ids: selectedIds })
            })
            .then(response => response.json())
            .then(res => {
                btn.disabled = false;
                btnText.innerText = "🚀 Kích hoạt Thuật toán AI";
                btnSpinner.classList.add('hidden');

                if (res.success && res.data) {
                    renderAIResults(res.data);
                } else {
                    alert(res.message || "Lỗi kết nối tới AI. Vui lòng thử lại!");
                }
            })
            .catch(error => {
                btn.disabled = false;
                btnText.innerText = "🚀 Kích hoạt Thuật toán AI";
                btnSpinner.classList.add('hidden');
                alert("Có lỗi xảy ra: " + error);
            });
        }

        function renderAIResults(aiData) {
            let container = document.getElementById('aiResultContainer');
            let msgBox = document.getElementById('aiMessage');
            let gridBox = document.getElementById('aiInvoicesGrid');

            container.classList.remove('hidden');
            gridBox.innerHTML = ''; // Xóa kết quả cũ

            // 1. Đổ tin nhắn của Bot
            msgBox.innerHTML = aiData.chatbot_message;

            // 2. Render từng chuyến xe
            if (aiData.invoices && aiData.invoices.length > 0) {
                aiData.invoices.forEach(inv => {
                    let colorTheme = inv.tripName.includes("Tải") ? "orange" : "blue";

                    // Tạo HTML cho bảng chi tiết phí
                    let feeHtml = '';
                    for (const [key, value] of Object.entries(inv.breakdown_fee)) {
                        feeHtml += `
                            <div class="flex justify-between text-xs py-1 border-b border-gray-100 last:border-0">
                                <span class="text-gray-500">${key}</span>
                                <span class="font-bold text-gray-700">${value.toLocaleString('vi-VN')} đ</span>
                            </div>`;
                    }

                    // Card hóa đơn
                    let card = `
                    <div class="bg-white border-2 border-${colorTheme}-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-${colorTheme}-50 rounded-bl-full -z-10"></div>

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h5 class="text-lg font-black text-${colorTheme}-700">${inv.tripName}</h5>
                                <p class="text-xs font-bold text-gray-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    Lộ trình: ${inv.distance} | Gồm: ${inv.totalOrders} đơn
                                </p>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-3 mb-4 border border-gray-100">
                            ${feeHtml}
                        </div>

                        <div class="flex justify-between items-center pt-2 border-t-2 border-dashed border-gray-200">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng cước</span>
                            <span class="text-xl font-black text-${colorTheme}-600">${inv.totalTripCost}</span>
                        </div>
                    </div>`;

                    gridBox.innerHTML += card;
                });
            }
        }
    <script>
            window.phpOrders = @json($orders);
        </script>

        <x-chatbot />

        <script src="{{ asset('js/chatbot.js') }}"></script>
</x-app-layout>
