<x-app-layout>
    {{-- 1. THÊM CSS CỦA CHATBOT --}}
    <link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">

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

    {{-- 2. GỌI COMPONENT CHATBOT CỦA SẾP RA --}}
    <x-chatbot />

    {{-- 3. THÊM JS CỦA CHATBOT --}}
    <script src="{{ asset('js/chatbot.js') }}"></script>
</x-app-layout>
