<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Đơn hàng chờ xác nhận</h1>
            <p class="text-sm text-gray-500 mt-1">Kiểm tra thông tin và xác nhận để chuyển đơn sang kho xử lý.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Xuất file
            </button>
            <button onclick="confirmSelected()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Xác nhận đơn đã chọn
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
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider font-semibold">
                        <th class="px-6 py-4 pl-8">Mã Đơn / Ngày tạo</th>
                        <th class="px-4 py-4">Khách hàng</th>
                        <th class="px-4 py-4 min-w-[200px]">Địa chỉ</th>
                        <th class="px-4 py-4">Sản phẩm & Thu hộ</th>
                        <th class="px-4 py-4 text-center">Khối lượng</th>
                        <th class="px-4 py-4 text-center">Dễ vỡ</th>
                        <th class="px-4 py-4 text-center">Chọn</th>
                        <th class="px-4 py-4 text-center text-red-500">Xóa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white" id="orders-table-body">

                    @forelse ($orders as $order)
                        <tr class="hover:bg-blue-50/30 transition-colors group" id="row-{{ $order->id }}">
                            <td class="px-6 py-4 pl-8">
                                <div class="font-bold text-blue-600 cursor-pointer hover:underline">#REQ-{{ $order->id }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $order->status ?? 'Chờ điều phối' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $order->receiver_name ?? $order->customer_name ?? 'Khách lẻ' }}</div>
                                <div class="text-xs text-gray-500">{{ $order->receiver_phone ?? $order->customer_phone ?? 'Chưa cập nhật SĐT' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-600 truncate max-w-[250px]" title="{{ $order->receiver_address ?? $order->address }}">
                                    {{ $order->receiver_address ?? $order->address ?? 'Chưa có địa chỉ' }}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 font-medium">{{ $order->product_name ?? 'Chưa cập nhật' }}</div>
                                <div class="text-xs font-bold text-red-600 mt-1">
                                    {{ $order->shipping_fee ? number_format($order->shipping_fee).'đ' : '0đ' }}
                                    <span class="text-gray-400 font-normal">(COD)</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">
                                    {{ $order->weight ?? $order->packageWeight ?? 0 }} gram
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($order->note && str_contains(mb_strtolower($order->note), 'dễ vỡ'))
                                    <div class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600" title="Hàng dễ vỡ">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @else
                                    <div class="inline-flex items-center justify-center w-6 h-6 text-gray-300" title="Hàng bình thường">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center pr-2">
                                <input type="checkbox" class="order-checkbox w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer shadow-sm transition-all hover:scale-110" value="{{ $order->id }}">
                            </td>
                            <td class="px-4 py-4 text-center pr-6">
                                <button onclick="deleteOrderPermanently({{ $order->id }})" class="p-2 text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Xóa vĩnh viễn đơn rác">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-state">
                            <td colspan="8" class="px-6 py-16 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-50 p-4 rounded-full mb-3">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <p class="text-base font-semibold text-gray-700">Chưa có đơn hàng nào mới</p>
                                    <p class="text-sm text-gray-400 mt-1">Hệ thống đang chờ dữ liệu đổ về từ Telegram...</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Hàm Xóa Vĩnh Viễn Đơn Rác
        function deleteOrderPermanently(id) {
            if (confirm('Sếp có chắc chắn muốn xóa VĨNH VIỄN đơn rác này không? Không thể khôi phục lại đâu nhé!')) {
                fetch('{{ route('orders.destroy') }}', {
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
                        const row = document.getElementById('row-' + id);
                        row.style.transition = 'all 0.3s';
                        row.style.opacity = '0';
                        row.style.transform = 'scale(0.95)';
                        setTimeout(() => row.remove(), 300);

                        // Hiển thị toast
                        const toast = document.getElementById('toast-success');
                        document.getElementById('toast-message').textContent = 'Đã xóa vĩnh viễn đơn rác.';
                        toast.classList.remove('opacity-0', 'translate-x-full');
                        toast.classList.add('opacity-100', 'translate-x-0');
                        setTimeout(() => {
                            toast.classList.remove('opacity-100', 'translate-x-0');
                            toast.classList.add('opacity-0', 'translate-x-full');
                        }, 3000);
                    } else {
                        alert('Lỗi: Không thể xóa đơn hàng!');
                    }
                });
            }
        }

        // Hàm Xác nhận đơn hàng (Giữ nguyên của ông)
        function confirmSelected() {
            const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
            const count = checkedBoxes.length;

            if (count === 0) {
                alert('Vui lòng tick chọn ít nhất 1 đơn hàng để xác nhận!');
                return;
            }

            const orderIds = Array.from(checkedBoxes).map(box => box.value);

            fetch('{{ route("orders.confirm") }}', {
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
                        row.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(30px)';
                        setTimeout(() => {
                            row.remove();
                        }, 400);
                    });

                    const toast = document.getElementById('toast-success');
                    document.getElementById('toast-message').textContent = `Đã chuyển thành công ${count} đơn hàng sang kho.`;
                    toast.classList.remove('opacity-0', 'translate-x-full');
                    toast.classList.add('opacity-100', 'translate-x-0');
                    setTimeout(() => {
                        toast.classList.remove('opacity-100', 'translate-x-0');
                        toast.classList.add('opacity-0', 'translate-x-full');
                    }, 3000);
                } else {
                    alert('Có lỗi xảy ra, không thể cập nhật CSDL!');
                }
            })
            .catch(error => alert('Không thể kết nối đến máy chủ!'));
        }
    </script>
<script>
        window.phpOrders = @json($orders);
    </script>

    <x-chatbot />

    <script src="{{ asset('js/chatbot.js') }}"></script>
</x-app-layout>
