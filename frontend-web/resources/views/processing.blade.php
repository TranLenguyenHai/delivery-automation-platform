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

    {{-- 1. FLOATING ACTION BAR (PREMIUM UI) --}}
    <div id="bulk-action-bar" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 bg-gray-900/95 backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-6 transition-all duration-500 translate-y-32 opacity-0 border border-white/10">
        <div class="flex items-center gap-3 pr-6 border-r border-white/20">
            <div class="relative">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-[10px] font-bold text-white shadow-lg animate-pulse" id="selected-count">0</span>
            </div>
            <span class="text-sm font-medium text-gray-300">đơn hàng đã chọn</span>
        </div>
        
        <div class="flex items-center gap-3">
            <button id="bulk-btn-print" onclick="printOrders()" class="flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-semibold transition-all active:scale-95">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                In vận đơn
            </button>
            <button id="bulk-btn-handover" onclick="handoverOrders()" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 rounded-xl text-sm font-bold transition-all shadow-lg shadow-blue-900/20 active:scale-95">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                Bàn giao ĐVVC
            </button>
        </div>

        <button onclick="resetSelection()" class="ml-2 p-2 text-gray-500 hover:text-white transition-colors rounded-lg hover:bg-white/5" title="Bỏ chọn tất cả">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <script>
        let currentTabStatus = 'all';

        // --- 1. QUẢN LÝ TRẠNG THÁI CHECKBOX ---
        function updateActionBar() {
            const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
            const count = checkedBoxes.length;
            const bar = document.getElementById('bulk-action-bar');
            const countLabel = document.getElementById('selected-count');
            
            // Cập nhật số lượng
            countLabel.textContent = count;

            // Hiển thị/Ẩn thanh công cụ
            if (count > 0) {
                bar.classList.remove('translate-y-32', 'opacity-0');
                bar.classList.add('translate-y-0', 'opacity-100');
                
                // Logic hiển thị nút theo Tab hiện tại
                const btnPrint = document.getElementById('bulk-btn-print');
                const btnHandover = document.getElementById('bulk-btn-handover');
                
                if (currentTabStatus === 'Chờ in đơn') {
                    btnPrint.style.display = 'flex';
                    btnHandover.style.display = 'none';
                } else if (currentTabStatus === 'Chờ lấy hàng') {
                    btnPrint.style.display = 'none';
                    btnHandover.style.display = 'flex';
                } else {
                    // Ở tab "Tất cả": Hiện cả 2 nhưng ưu tiên In nếu có đơn cần in
                    btnPrint.style.display = 'flex';
                    btnHandover.style.display = 'flex';
                }
            } else {
                bar.classList.add('translate-y-32', 'opacity-0');
                bar.classList.remove('translate-y-0', 'opacity-100');
            }
        }

        function resetSelection() {
            document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('check-all').checked = false;
            updateActionBar();
        }

        // Đăng ký sự kiện cho checkbox
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('order-checkbox') || e.target.id === 'check-all') {
                updateActionBar();
            }
        });

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

        // --- 2. LOGIC CHUYỂN TAB & FIX STATE LEAKAGE ---
        function filterOrders(statusFilter, clickedBtn) {
            currentTabStatus = statusFilter;

            // BẮT BUỘC: Reset trạng thái khi chuyển tab (Fix Bug 3)
            resetSelection();

            // Cập nhật UI Tab
            const tabs = document.querySelectorAll('.tab-btn');
            tabs.forEach(tab => {
                tab.classList.remove('text-blue-600', 'border-blue-600', 'font-semibold', 'active-tab');
                tab.classList.add('text-gray-500', 'border-transparent', 'font-medium');
            });
            clickedBtn.classList.remove('text-gray-500', 'border-transparent', 'font-medium');
            clickedBtn.classList.add('text-blue-600', 'border-blue-600', 'font-semibold', 'active-tab');

            // Lọc dữ liệu (Fix Bug 2)
            const rows = document.querySelectorAll('.order-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status').trim();
                // Logic lọc chính xác theo status hoặc hiển thị tất cả nếu là 'all'
                // Bao gồm cả trạng thái "Đang thẩm định AI..." vào tab "Tất cả"
                if (statusFilter === 'all' || rowStatus === statusFilter) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Xử lý Trạng thái trống
            let emptyState = document.getElementById('empty-state-filter');
            if (visibleCount === 0 && rows.length > 0) {
                if (!emptyState) {
                    document.getElementById('orders-table-body').insertAdjacentHTML('beforeend', `
                        <tr id="empty-state-filter"><td colspan="6" class="px-6 py-16 text-center text-gray-500"><p class="text-base font-semibold text-gray-700">Không tìm thấy đơn hàng nào trong mục này</p></td></tr>
                    `);
                }
            } else if (emptyState) {
                emptyState.remove();
            }
        }

        // --- 3. XỬ LÝ HÀNH ĐỘNG HÀNG LOẠT ---
        function printOrders() {
            const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
            if (checkedBoxes.length === 0) return;

            const orderIds = Array.from(checkedBoxes).map(box => box.value);

            // Mở trang in vận đơn
            window.open('/orders/print-labels?ids=' + orderIds.join(','), '_blank');

            // Sau khi mở trang in, tự động cập nhật trạng thái
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
                    showToast(`Đã chuyển ${orderIds.length} đơn hàng sang "Chờ lấy hàng".`);
                    filterOrders(currentTabStatus, document.querySelector('.active-tab'));
                    updateActionBar();
                }
            });
        }

        function handoverOrders() {
            const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
            if (checkedBoxes.length === 0) return;

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
                        row.classList.add('scale-95', 'opacity-0');
                        setTimeout(() => row.remove(), 300);
                    });
                    showToast(`Đã bàn giao ${orderIds.length} đơn hàng.`);
                    setTimeout(() => updateActionBar(), 400);
                }
            });
        }

        function cancelOrder(id) {
            if (confirm('Sếp muốn HỦY đơn hàng này?')) {
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
                        showToast('Đã hủy đơn hàng thành công!');
                        const checkbox = document.querySelector(`.order-checkbox[value="${id}"]`);
                        if (checkbox) {
                            const row = checkbox.closest('tr');
                            row.classList.add('scale-95', 'opacity-0');
                            setTimeout(() => {
                                row.remove();
                                updateActionBar();
                            }, 300);
                        }
                    }
                });
            }
        }

        // Select All Tool
        document.getElementById('check-all').addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.order-checkbox').forEach(box => {
                const row = box.closest('tr');
                if(row.style.display !== 'none') {
                    box.checked = isChecked;
                }
            });
            updateActionBar();
        });
    </script>
<script>
        window.phpOrders = @json(\DB::table('orders')->get());
    </script>

    <x-chatbot />
    <script src="{{ asset('js/chatbot.js') }}?v={{ time() }}"></script>
</x-app-layout>
