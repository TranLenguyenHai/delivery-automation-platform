<x-app-layout>
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
        <div class="ml-3 text-sm font-semibold text-gray-800" id="toast-message">Đã xác nhận đơn hàng thành công.</div>
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
                                    {{ $order->weight ?? $order->packageWeight ?? 0 }} kg
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
                            <td class="px-4 py-4 text-center pr-6">
                                <input type="checkbox" class="order-checkbox w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer shadow-sm transition-all hover:scale-110" value="{{ $order->id }}">
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-state">
                            <td colspan="7" class="px-6 py-16 text-center text-gray-500">
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
        function confirmSelected() {
            const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
            const count = checkedBoxes.length;

            if (count === 0) {
                alert('Vui lòng tick chọn ít nhất 1 đơn hàng để xác nhận!');
                return;
            }

            checkedBoxes.forEach(box => {
                const row = box.closest('tr');
                row.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                row.style.opacity = '0';
                row.style.transform = 'translateX(30px)';

                setTimeout(() => {
                    row.remove();
                    if(document.querySelectorAll('.order-checkbox').length === 0) {
                        // Tùy chọn: Hiện lại empty state nếu xóa hết
                    }
                }, 400);
            });

            const toast = document.getElementById('toast-success');
            const toastMsg = document.getElementById('toast-message');
            toastMsg.textContent = `Đã chuyển thành công ${count} đơn hàng sang Đang xử lý.`;

            toast.classList.remove('opacity-0', 'translate-x-full');
            toast.classList.add('opacity-100', 'translate-x-0');

            setTimeout(() => {
                toast.classList.remove('opacity-100', 'translate-x-0');
                toast.classList.add('opacity-0', 'translate-x-full');
            }, 3000);
        }
    </script>
<script>
    window.phpOrders = @json($orders);
</script>

<script src="{{ asset('js/chatbot.js') }}"></script>
</x-app-layout>
