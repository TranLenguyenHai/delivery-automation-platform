<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Trạng thái vận chuyển</h1>
        <p class="text-sm text-gray-500 mt-1">Theo dõi các đơn hàng đã bàn giao cho đơn vị vận chuyển.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="flex border-b border-gray-200 px-2 overflow-x-auto bg-gray-50/30">
            <button onclick="filterOrders('all', this)" class="tab-btn active-tab px-4 py-3.5 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 whitespace-nowrap transition-colors">Tất cả đơn ({{ count($orders) }})</button>
            <button onclick="filterOrders('Đang giao hàng', this)" class="tab-btn px-4 py-3.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-colors whitespace-nowrap">Đang giao hàng</button>
            <button onclick="filterOrders('Giao thành công', this)" class="tab-btn px-4 py-3.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-colors whitespace-nowrap">Giao thành công</button>
            <button onclick="filterOrders('Giao thất bại', this)" class="tab-btn px-4 py-3.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-colors whitespace-nowrap">Giao thất bại</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider font-semibold">
                        <th class="px-6 py-4 pl-8">Mã Đơn</th>
                        <th class="px-4 py-4">Khách hàng</th>
                        <th class="px-4 py-4 min-w-[200px]">Địa chỉ giao</th>
                        <th class="px-4 py-4">Sản phẩm</th>
                        <th class="px-4 py-4 text-center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white" id="orders-table-body">
                    @forelse ($orders as $order)
                        <tr class="order-row hover:bg-blue-50/30 transition-colors" data-status="{{ $order->status }}">
                            <td class="px-6 py-4 pl-8">
                                <div class="font-bold text-blue-600">#REQ-{{ $order->id }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $order->receiver_name ?? 'Khách lẻ' }}</div>
                                <div class="text-xs text-gray-500">{{ $order->receiver_phone }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-600 truncate max-w-[250px]" title="{{ $order->receiver_address }}">{{ $order->receiver_address }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900">{{ $order->product_name }}</td>
                            <td class="px-4 py-4 text-center">
                                @php
                                    $bg = 'bg-blue-100 text-blue-700 border-blue-200';
                                    if($order->status === 'Giao thành công') $bg = 'bg-green-100 text-green-700 border-green-200';
                                    if($order->status === 'Giao thất bại') $bg = 'bg-red-100 text-red-700 border-red-200';
                                @endphp
                                <span class="px-3 py-1 text-xs font-bold rounded-full border shadow-sm {{ $bg }}">{{ $order->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-16 text-center text-gray-500">Chưa có dữ liệu vận chuyển.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function filterOrders(statusFilter, clickedBtn) {
            const tabs = document.querySelectorAll('.tab-btn');
            tabs.forEach(tab => {
                tab.classList.remove('text-blue-600', 'border-blue-600', 'font-semibold');
                tab.classList.add('text-gray-500', 'border-transparent', 'font-medium');
            });
            clickedBtn.classList.remove('text-gray-500', 'border-transparent', 'font-medium');
            clickedBtn.classList.add('text-blue-600', 'border-blue-600', 'font-semibold');

            const rows = document.querySelectorAll('.order-row');
            rows.forEach(row => {
                row.style.display = (statusFilter === 'all' || row.getAttribute('data-status') === statusFilter) ? '' : 'none';
            });
        }
    </script>
</x-app-layout>
