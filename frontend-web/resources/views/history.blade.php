<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Lịch sử đơn hàng</h1>
            <p class="text-sm text-gray-500 mt-1">Tra cứu các đơn hàng đã giao thành công, bị hủy hoặc hoàn trả.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Tháng này
            </button>
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Xuất dữ liệu
            </button>
        </div>
    </div>

    <div id="toast-success" class="fixed top-6 right-6 flex items-center w-full max-w-xs p-4 mb-4 text-gray-600 bg-white rounded-xl shadow-2xl border border-green-100 opacity-0 transform translate-x-full transition-all duration-500 z-50" role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div class="ml-3 text-sm font-semibold text-gray-800" id="toast-message">Xóa thành công.</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">

        <div class="flex border-b border-gray-200 px-2 overflow-x-auto bg-gray-50/30">
            <button onclick="filterHistory('all', this)" class="tab-btn active-tab px-4 py-3.5 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 whitespace-nowrap transition-colors">Tất cả lịch sử</button>
            <button onclick="filterHistory('Giao thành công', this)" class="tab-btn px-4 py-3.5 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent transition-colors whitespace-nowrap">Hoàn thành</button>
            <button onclick="filterHistory('Đã hủy', this)" class="tab-btn px-4 py-3.5 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent transition-colors whitespace-nowrap">Đã hủy</button>
            <button onclick="filterHistory('Giao thất bại', this)" class="tab-btn px-4 py-3.5 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent transition-colors whitespace-nowrap">Giao thất bại</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider font-semibold">
                        <th class="px-6 py-4 pl-8">Mã Đơn</th>
                        <th class="px-4 py-4">Khách hàng</th>
                        <th class="px-4 py-4">Sản phẩm & Tổng tiền</th>
                        <th class="px-4 py-4 text-center">ĐVVC</th>
                        <th class="px-4 py-4 text-center">Trạng thái cuối</th>
                        <th class="px-4 py-4 text-right pr-8">Ghi chú</th>
                        <th class="px-4 py-4 text-center delete-col">Xóa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white" id="history-table-body">

                    @forelse ($orders as $order)
                        <tr class="history-row hover:bg-gray-50 transition-colors" data-status="{{ $order->status }}" id="row-{{ $order->id }}">
                            <td class="px-6 py-4 pl-8 font-bold text-gray-700">#REQ-{{ $order->id }}</td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $order->receiver_name ?? $order->customer_name ?? 'Khách lẻ' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 font-medium">{{ $order->product_name ?? 'Hàng hóa' }}</div>
                                <div class="text-xs font-bold text-gray-600 mt-1">{{ number_format($order->shipping_fee ?? 0) }}đ</div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">{{ $order->shipper ?? 'Chưa rõ' }}</span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($order->status === 'Giao thành công')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 border border-green-200">Hoàn thành</span>
                                @elseif($order->status === 'Đã hủy')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600 border border-gray-200">Đã hủy</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 border border-red-200">Giao thất bại</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right pr-8 text-xs text-red-500 max-w-[200px] truncate" title="{{ $order->note }}">
                                {{ $order->note }}
                            </td>
                            <td class="px-4 py-4 text-center delete-col">
                                <button onclick="deleteHistoryOrder({{ $order->id }})" class="p-2 text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Xóa vĩnh viễn khỏi CSDL">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-state">
                            <td colspan="7" class="px-6 py-20 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 p-4 rounded-full mb-4">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-lg font-bold text-gray-800">Chưa có dữ liệu lịch sử</p>
                                    <p class="text-sm text-gray-400 mt-1 max-w-sm">Các đơn hàng sau khi hoàn tất quy trình (giao xong hoặc bị hủy) sẽ được lưu trữ tại đây.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

    <script>
        // 1. LOGIC ẨN/HIỆN THEO TAB
        function filterHistory(statusFilter, clickedBtn) {
            // Đổi style Tab
            const tabs = document.querySelectorAll('.tab-btn');
            tabs.forEach(tab => {
                tab.classList.remove('text-blue-600', 'border-blue-600', 'font-semibold', 'active-tab');
                tab.classList.add('text-gray-500', 'border-transparent', 'font-medium');
            });
            clickedBtn.classList.remove('text-gray-500', 'border-transparent', 'font-medium');
            clickedBtn.classList.add('text-blue-600', 'border-blue-600', 'font-semibold', 'active-tab');

            // Ẩn/Hiện Cột "Xóa"
            const deleteCols = document.querySelectorAll('.delete-col');
            if (statusFilter === 'all') {
                deleteCols.forEach(col => col.style.display = ''); // Hiện cột Xóa
            } else {
                deleteCols.forEach(col => col.style.display = 'none'); // Giấu cột Xóa đi
            }

            // Ẩn hiện các dòng dữ liệu
            const rows = document.querySelectorAll('.history-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                if (statusFilter === 'all' || rowStatus === statusFilter) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Xử lý thông báo "Trống"
            let emptyState = document.getElementById('empty-state-filter');
            if (visibleCount === 0 && rows.length > 0) {
                if (!emptyState) {
                    // Nếu đang ở tab All thì colspan = 7, các tab khác colspan = 6
                    let colSpanValue = statusFilter === 'all' ? 7 : 6;
                    document.getElementById('history-table-body').insertAdjacentHTML('beforeend', `
                        <tr id="empty-state-filter"><td colspan="${colSpanValue}" class="px-6 py-16 text-center text-gray-500"><p class="text-base font-semibold text-gray-700">Không có đơn hàng nào ở mục này</p></td></tr>
                    `);
                }
            } else if (emptyState) {
                emptyState.remove();
            }
        }

        // 2. LOGIC XÓA VĨNH VIỄN LỊCH SỬ
        function deleteHistoryOrder(id) {
            if (confirm('Sếp có chắc chắn muốn xóa VĨNH VIỄN dữ liệu đơn hàng này khỏi lịch sử không? (Không thể khôi phục!)')) {
                fetch('{{ route("orders.destroy") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ids: [id] })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Làm mờ và xóa dòng
                        const row = document.getElementById('row-' + id);
                        row.style.transition = 'all 0.3s';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);

                        // Hiện thông báo
                        const toast = document.getElementById('toast-success');
                        document.getElementById('toast-message').textContent = 'Đã xóa vĩnh viễn khỏi lịch sử.';
                        toast.classList.remove('opacity-0', 'translate-x-full');
                        toast.classList.add('opacity-100', 'translate-x-0');
                        setTimeout(() => {
                            toast.classList.remove('opacity-100', 'translate-x-0');
                            toast.classList.add('opacity-0', 'translate-x-full');
                        }, 3000);
                    } else {
                        alert('Lỗi: Không thể xóa đơn hàng này!');
                    }
                })
                .catch(error => {
                    alert('Có lỗi mạng xảy ra!');
                });
            }
        }
    </script>
</x-app-layout>
