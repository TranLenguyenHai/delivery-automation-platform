<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Đơn hàng đang xử lý</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý đóng gói, in vận đơn và bàn giao cho đơn vị vận chuyển.</p>
        </div>
        <div class="flex items-center gap-3">
            <button id="btn-print" onclick="printOrders()" class="hidden px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 shadow-sm transition-all items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                In đơn
            </button>
            <button id="btn-handover" onclick="handoverOrders()" class="hidden px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-sm transition-all items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                Bàn giao cho ĐVVC
            </button>
        </div>
    </div>

    <div id="toast-success" class="fixed top-6 right-6 flex items-center w-full max-w-xs p-4 mb-4 text-gray-600 bg-white rounded-xl shadow-2xl border border-green-100 opacity-0 transform translate-x-full transition-all duration-500 z-50" role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div class="ml-3 text-sm font-semibold text-gray-800" id="toast-message">Cập nhật thành công.</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">

        <div class="flex border-b border-gray-200 px-2 overflow-x-auto bg-gray-50/30" id="filter-tabs">
            <button onclick="filterOrders('all', this)" class="tab-btn active-tab px-4 py-3.5 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 whitespace-nowrap transition-colors">Tất cả đang xử lý</button>
            <button onclick="filterOrders('Chờ in đơn', this)" class="tab-btn px-4 py-3.5 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent transition-colors whitespace-nowrap">Chờ in đơn</button>
            <button onclick="filterOrders('Chờ lấy hàng', this)" class="tab-btn px-4 py-3.5 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent transition-colors whitespace-nowrap">Chờ lấy hàng</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider font-semibold">
                        <th class="px-4 py-4 w-10 text-center"><input type="checkbox" id="check-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"></th>
                        <th class="px-4 py-4">Mã Đơn / Ngày tạo</th>
                        <th class="px-4 py-4">Khách hàng</th>
                        <th class="px-4 py-4">Sản phẩm & Thu hộ</th>
                        <th class="px-4 py-4 text-center">Trạng thái kho</th>
                        <th class="px-4 py-4 w-10 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white" id="orders-table-body">

                    @forelse ($orders as $order)
                        <tr class="order-row hover:bg-blue-50/30 transition-colors group" data-status="{{ $order->status }}">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" value="{{ $order->id }}" class="order-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-blue-600 cursor-pointer hover:underline">#REQ-{{ $order->id }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $order->receiver_name ?? $order->customer_name ?? 'Khách lẻ' }}</div>
                                <div class="text-xs text-gray-500">{{ $order->receiver_phone ?? $order->customer_phone ?? 'Chưa cập nhật SĐT' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 font-medium">{{ $order->product_name ?? 'Chưa cập nhật' }}</div>
                                <div class="text-xs font-bold text-red-600 mt-1">
                                    {{ $order->shipping_fee ? number_format($order->shipping_fee).'đ' : '0đ' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                @php
                                    $badgeClass = $order->status === 'Chờ in đơn' ? 'bg-yellow-100 text-yellow-700 border-yellow-200' : 'bg-blue-100 text-blue-700 border-blue-200';
                                @endphp
                                <span class="status-badge px-3 py-1 text-xs font-bold rounded-full border shadow-sm {{ $badgeClass }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <button onclick="cancelOrder({{ $order->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hủy đơn hàng này">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-state">
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-50 p-4 rounded-full mb-3">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                    <p class="text-base font-semibold text-gray-700">Chưa có đơn hàng nào cần xử lý</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

    <script>
        let currentTabStatus = 'all';

        function showToast(message) {
            const toast = document.getElementById('toast-success');
            document.getElementById('toast-message').textContent = message;
            toast.classList.remove('opacity-0', 'translate-x-full');
            toast.classList.add('opacity-100', 'translate-x-0');
            setTimeout(() => {
                toast.classList.remove('opacity-100', 'translate-x-0');
                toast.classList.add('opacity-0', 'translate-x-full');
            }, 3000);
        }

        // --- 1. LOGIC ẨN HIỆN THEO TAB ---
        function filterOrders(statusFilter, clickedBtn) {
            currentTabStatus = statusFilter;

            // Xử lý active trạng thái cho Tab
            const tabs = document.querySelectorAll('.tab-btn');
            tabs.forEach(tab => {
                tab.classList.remove('text-blue-600', 'border-blue-600', 'font-semibold', 'active-tab');
                tab.classList.add('text-gray-500', 'border-transparent', 'font-medium');
            });
            clickedBtn.classList.remove('text-gray-500', 'border-transparent', 'font-medium');
            clickedBtn.classList.add('text-blue-600', 'border-blue-600', 'font-semibold', 'active-tab');

            // Logic ẩn/hiện nút bấm thao tác góc trên
            const btnPrint = document.getElementById('btn-print');
            const btnHandover = document.getElementById('btn-handover');

            if (statusFilter === 'all') {
                // Đang ở "Tất cả": Ẩn cả 2 nút
                btnPrint.classList.add('hidden');
                btnPrint.classList.remove('flex');
                btnHandover.classList.add('hidden');
                btnHandover.classList.remove('flex');
            } else if (statusFilter === 'Chờ in đơn') {
                // Đang ở "Chờ in": Hiện in, ẩn bàn giao
                btnPrint.classList.remove('hidden');
                btnPrint.classList.add('flex');
                btnHandover.classList.add('hidden');
                btnHandover.classList.remove('flex');
            } else if (statusFilter === 'Chờ lấy hàng') {
                // Đang ở "Chờ lấy": Ẩn in, hiện bàn giao
                btnPrint.classList.add('hidden');
                btnPrint.classList.remove('flex');
                btnHandover.classList.remove('hidden');
                btnHandover.classList.add('flex');
            }

            // Lọc dữ liệu trong bảng
            const rows = document.querySelectorAll('.order-row');
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

            // Xử lý Empty State
            let emptyState = document.getElementById('empty-state-filter');
            if (visibleCount === 0 && rows.length > 0) {
                if (!emptyState) {
                    document.getElementById('orders-table-body').insertAdjacentHTML('beforeend', `
                        <tr id="empty-state-filter"><td colspan="6" class="px-6 py-16 text-center text-gray-500"><p class="text-base font-semibold text-gray-700">Trống</p></td></tr>
                    `);
                }
            } else if (emptyState) {
                emptyState.remove();
            }
        }

        // --- 2. LOGIC NÚT IN ĐƠN ---
        function printOrders() {
            const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
            if (checkedBoxes.length === 0) return alert('Vui lòng tick chọn đơn hàng cần In!');

            const orderIds = Array.from(checkedBoxes).map(box => box.value);

            fetch('{{ route("orders.print") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ids: orderIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    checkedBoxes.forEach(box => {
                        const row = box.closest('tr');
                        row.setAttribute('data-status', 'Chờ lấy hàng');
                        const badge = row.querySelector('.status-badge');
                        badge.textContent = 'Chờ lấy hàng';
                        badge.classList.replace('text-yellow-700', 'text-blue-700');
                        badge.classList.replace('bg-yellow-100', 'bg-blue-100');
                        badge.classList.replace('border-yellow-200', 'border-blue-200');
                        box.checked = false;
                    });
                    filterOrders(currentTabStatus, document.querySelector('.active-tab'));
                    showToast(`Đã in thành công ${orderIds.length} đơn hàng. Đã chuyển sang Chờ lấy hàng.`);
                }
            });
        }

        // --- 3. LOGIC NÚT BÀN GIAO ĐVVC ---
        function handoverOrders() {
            const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
            if (checkedBoxes.length === 0) return alert('Vui lòng tick chọn đơn hàng để Bàn giao!');

            const orderIds = Array.from(checkedBoxes).map(box => box.value);

            fetch('{{ route("orders.handover") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ids: orderIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    checkedBoxes.forEach(box => {
                        const row = box.closest('tr');
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);
                    });
                    showToast(`Đã bàn giao ${orderIds.length} đơn hàng. Đã đẩy sang trang Trạng thái.`);
                }
            });
        }

        // --- 4. LOGIC HỦY ĐƠN HÀNG (THÙNG RÁC) ---
        function cancelOrder(id) {
            if (confirm('Sếp muốn HỦY đơn hàng này? Đơn sẽ được chuyển vào mục Lịch sử (Đã hủy).')) {
                fetch('{{ route("orders.cancel") }}', {
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
                        showToast('Đã chuyển đơn hàng sang trạng thái Đã hủy!');
                        // Xóa luôn dòng đó khỏi màn hình cho gọn mắt
                        const row = document.querySelector(`input[value="${id}"]`).closest('tr');
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);
                    } else {
                        alert('Lỗi: Không thể hủy đơn hàng!');
                    }
                })
                .catch(err => {
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                });
            }
        }

        // Tiện ích: Tick chọn tất cả
        document.getElementById('check-all').addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.order-checkbox').forEach(box => {
                if(box.closest('tr').style.display !== 'none') {
                    box.checked = isChecked;
                }
            });
        });
    </script>
<script>
        window.phpOrders = @json(\DB::table('orders')->get());
    </script>

    <x-chatbot />
    <script src="{{ asset('js/chatbot.js') }}?v={{ time() }}"></script>
</x-app-layout>
