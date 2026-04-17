<x-app-layout>
    <div class="max-w-5xl mx-auto py-6">
        <div class="mb-4">
            <a href="{{ route('pending') }}" class="text-sm font-semibold text-gray-500 hover:text-blue-600 flex items-center gap-1 transition-colors w-max">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại danh sách
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="text-center py-6 border-b border-gray-100 bg-gray-50/50">
                <h1 class="text-2xl font-black text-gray-800 uppercase tracking-wide">TẠO ĐƠN HÀNG</h1>
                <p class="text-sm text-gray-500 mt-1">Hệ thống tối ưu chi phí giao hàng</p>
            </div>

            <form action="{{ route('orders.store') }}" method="POST" class="p-6 sm:p-10">
                @csrf

                <div class="grid grid-cols-2 gap-x-12 gap-y-6 mb-10 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">

                    <div>
                        <h3 class="text-base font-bold text-blue-600 flex items-center gap-2 border-b border-gray-100 pb-2">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                            Thông tin Người Gửi
                        </h3>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-blue-600 flex items-center gap-2 border-b border-gray-100 pb-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Thông tin Người Nhận
                        </h3>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tên người gửi <span class="text-red-500">*</span></label>
                        <input type="text" name="sender_name" required class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm px-4 py-2" placeholder="Tên người gửi">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tên người nhận <span class="text-red-500">*</span></label>
                        <input type="text" name="receiver_name" required class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm px-4 py-2" placeholder="Tên người nhận">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                        <input type="text" name="sender_phone" required class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm px-4 py-2" placeholder="SĐT người gửi">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                        <input type="text" name="receiver_phone" required class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm px-4 py-2" placeholder="SĐT người nhận">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Địa chỉ lấy hàng <span class="text-red-500">*</span></label>
                        <input type="text" id="sender_address" name="sender_address" required class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm px-4 py-2" placeholder="Địa chỉ lấy hàng">
                        <input type="hidden" name="sender_lat" id="sender_lat">
                        <input type="hidden" name="sender_lng" id="sender_lng">
                        <p id="sender_status" class="text-[10px] mt-1 text-gray-400 italic"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Địa chỉ giao hàng <span class="text-red-500">*</span></label>
                        <input type="text" id="receiver_address" name="receiver_address" required class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm px-4 py-2" placeholder="Địa chỉ giao hàng">
                        <input type="hidden" name="receiver_lat" id="receiver_lat">
                        <input type="hidden" name="receiver_lng" id="receiver_lng">
                        <p id="receiver_status" class="text-[10px] mt-1 text-gray-400 italic"></p>
                    </div>
                </div>

                <div class="space-y-5 mb-8 border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-base font-bold text-blue-600 flex items-center gap-2 border-b border-gray-100 pb-2">
                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Thông tin Hàng Hóa
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="sm:col-span-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tên sản phẩm <span class="text-red-500">*</span></label>
                            <input type="text" name="product_name" required class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm px-4 py-2">
                        </div>
                        <div class="sm:col-span-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Khối lượng (gram)</label>
                            <input type="number" name="weight" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm px-4 py-2" placeholder="VD: 500">
                        </div>
                        <div class="sm:col-span-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tiền thu hộ COD (VNĐ)</label>
                            <input type="number" name="shipping_fee" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm px-4 py-2" placeholder="0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-1">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Ghi chú cho hệ thống (Tùy chọn)
                        </label>
                        <textarea name="note" rows="2" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm px-4 py-2 text-sm" placeholder="Ví dụ: Hàng thủy tinh dễ vỡ, giao trong giờ hành chính..."></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white text-base font-bold uppercase tracking-wider py-4 rounded-lg shadow-md hover:bg-blue-700 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    TẠO ĐƠN HÀNG
                </button>

            </form>
        </div>
    </div>
<script>
    function geocodeAddress(address, type) {
        if (address.length < 10) return; // Địa chỉ ngắn quá thì chưa dò

        const statusEl = document.getElementById(`${type}_status`);
        statusEl.innerText = "🛰️ Đang dò tọa độ vệ tinh...";
        statusEl.className = "text-[10px] mt-1 text-blue-500 italic";

        // Gọi API miễn phí Nominatim
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = data[0].lat;
                    const lon = data[0].lon;

                    // Điền tọa độ vào ô ẩn
                    document.getElementById(`${type}_lat`).value = lat;
                    document.getElementById(`${type}_lng`).value = lon;

                    statusEl.innerText = `✅ Đã xác định vị trí: ${lat}, ${lon}`;
                    statusEl.className = "text-[10px] mt-1 text-green-500 font-bold";
                } else {
                    statusEl.innerText = "❌ Không tìm thấy địa chỉ trên bản đồ!";
                    statusEl.className = "text-[10px] mt-1 text-red-500";
                }
            })
            .catch(error => {
                statusEl.innerText = "⚠️ Lỗi kết nối bản đồ!";
            });
    }

    // Lắng nghe sự kiện khi sếp nhập xong địa chỉ
    document.getElementById('sender_address').addEventListener('blur', function() {
        geocodeAddress(this.value, 'sender');
    });

    document.getElementById('receiver_address').addEventListener('blur', function() {
        geocodeAddress(this.value, 'receiver');
    });
</script>
</x-app-layout>
