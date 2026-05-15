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
            <button id="btn-confirm-ai" onclick="confirmSelected()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl text-sm font-bold hover:from-blue-700 hover:to-indigo-800 shadow-lg shadow-blue-200 transition-all flex items-center gap-2 group active:scale-95">
                <span id="btn-text">Tính tiền AI / Thanh toán</span>
                <svg id="btn-spinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <svg id="btn-icon" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </button>
        </div>
    </div>

    {{-- MODAL HIỂN THỊ KẾT QUẢ AI --}}
    <div id="ai-modal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeAiModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-blue-100">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        KẾT QUẢ THẨM ĐỊNH AI LOGISTICS
                    </h3>
                    <button onclick="closeAiModal()" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6">
                    <div id="ai-results-content" class="space-y-4">
                        {{-- Kết quả sẽ đổ vào đây --}}
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button onclick="closeAiModal()" class="px-6 py-2 bg-blue-600 text-white rounded-xl font-bold shadow-md hover:bg-blue-700 transition-all">
                        Đã hiểu, chuyển vào Kho
                    </button>
                </div>
            </div>
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
        function closeAiModal() {
            document.getElementById('ai-modal').classList.add('hidden');
        }

        function deleteOrderPermanently(id) {
            if (confirm('Sếp có chắc chắn muốn xóa VĨNH VIỄN đơn rác này không?')) {
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
                        row.remove();
                    }
                });
            }
        }

        function confirmSelected() {
            const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
            const count = checkedBoxes.length;

            if (count === 0) {
                alert('Vui lòng chọn ít nhất 1 đơn hàng!');
                return;
            }

            // Hiệu ứng Loading
            const btn = document.getElementById('btn-confirm-ai');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');
            const btnIcon = document.getElementById('btn-icon');

            btn.disabled = true;
            btnText.innerText = "Đang thẩm định AI...";
            btnSpinner.classList.remove('hidden');
            btnIcon.classList.add('hidden');

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
                btn.disabled = false;
                btnText.innerText = "Tính tiền AI / Thanh toán";
                btnSpinner.classList.add('hidden');
                btnIcon.classList.remove('hidden');

                if (data.success && data.results) {
                    renderAiModal(data.results);
                    
                    // Xóa các dòng đã chọn khỏi bảng
                    checkedBoxes.forEach(box => {
                        const row = box.closest('tr');
                        row.remove();
                    });
                }
            })
            .catch(error => {
                btn.disabled = false;
                btnText.innerText = "Tính tiền AI / Thanh toán";
                btnSpinner.classList.add('hidden');
                btnIcon.classList.remove('hidden');
                alert('Lỗi kết nối AI!');
            });
        }

        function renderAiModal(results) {
            const container = document.getElementById('ai-results-content');
            container.innerHTML = '';
            
            results.forEach(res => {
                const item = `
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 shadow-sm">
                    <div class="flex justify-between items-center mb-3">
                        <span class="font-black text-blue-700">ĐƠN HÀNG #REQ-${res.id}</span>
                        <span class="px-2 py-1 bg-white border border-blue-200 rounded text-[10px] font-bold text-blue-600 uppercase tracking-tighter">${res.tinh_chat_hang || 'NORMAL'}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-white p-2 rounded-lg border border-blue-50">
                            <p class="text-xs text-gray-400">Thời tiết</p>
                            <p class="font-bold text-gray-700">${res.thoi_tiet || 'Bình thường'}</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-blue-50 text-right">
                            <p class="text-xs text-gray-400">Quãng đường</p>
                            <p class="font-bold text-gray-700">${res.quang_duong || 0} KM</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-blue-100 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-500 uppercase">Tổng tiền AI tính:</span>
                        <span class="text-lg font-black text-red-600">${new Intl.NumberFormat('vi-VN').format(res.tong_tien || 0)}đ</span>
                    </div>
                </div>`;
                container.innerHTML += item;
            });

            document.getElementById('ai-modal').classList.remove('hidden');
        }
    </script>
<script>
        window.phpOrders = @json(\DB::table('orders')->get());
    </script>

    <x-chatbot />
    <script src="{{ asset('js/chatbot.js') }}?v={{ time() }}"></script>
</x-app-layout>
