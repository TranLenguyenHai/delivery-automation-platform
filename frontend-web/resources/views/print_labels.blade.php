<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>In vận đơn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* A6 Size styling for normal printing if needed */
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .no-print { display: none; }
            .shipping-label { page-break-after: always; box-shadow: none !important; border: none !important; margin: 0 !important; }
            #labels-container { padding: 0; }
        }
    </style>
</head>
<body class="bg-gray-200 p-8 flex flex-col items-center min-h-screen">

    <div class="no-print max-w-4xl w-full flex justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Hệ Thống In Vận Đơn</h1>
            <p class="text-sm text-gray-500 mt-1">Hỗ trợ in nhãn dán khổ A6. Mỗi đơn hàng sẽ lưu thành 1 file PDF riêng biệt.</p>
        </div>
        <button id="btn-download" onclick="downloadAllPDFs()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg shadow-blue-200 font-bold transition-all flex items-center gap-2 active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            <span id="btn-text">Lưu thành {{ count($orders) }} file PDF</span>
        </button>
    </div>

    <div id="labels-container" class="space-y-8 flex flex-col items-center pb-10">
        @foreach($orders as $order)
        <!-- Khổ A6: 105 x 148 mm -->
        <div class="shipping-label bg-white border border-gray-300 w-[105mm] h-[148mm] p-4 relative shadow-lg overflow-hidden flex flex-col" id="label-{{ $order->id }}">
            
            <!-- Header: Logo and Barcode -->
            <div class="flex justify-between items-start border-b-2 border-black pb-3 mb-3 shrink-0">
                <div class="w-1/2">
                    @php
                        $shipper = mb_strtoupper($order->shipper ?? '');
                    @endphp
                    @if(str_contains($shipper, 'GHTK') || str_contains($shipper, 'GIAO HÀNG TIẾT KIỆM'))
                        <h2 class="text-4xl font-black text-[#006E43] tracking-tighter">GHTK</h2>
                    @elseif(str_contains($shipper, 'GHN') || str_contains($shipper, 'GIAO HÀNG NHANH'))
                        <h2 class="text-4xl font-black text-[#F26522] tracking-tighter">GHN</h2>
                    @elseif(str_contains($shipper, 'VIETTEL'))
                        <h2 class="text-3xl font-black text-[#EE0033] tracking-tighter leading-tight">VIETTEL<br>POST</h2>
                    @elseif(str_contains($shipper, 'J&T'))
                        <h2 class="text-4xl font-black text-[#E31837] tracking-tighter">J&T</h2>
                    @elseif(str_contains($shipper, 'AHAMOVE'))
                        <h2 class="text-3xl font-black text-[#F57120] tracking-tighter">Ahamove</h2>
                    @else
                        <h2 class="text-3xl font-black text-gray-800 tracking-tighter">{{ $shipper ?: 'ĐVVC' }}</h2>
                    @endif
                </div>
                <div class="w-1/2 text-right">
                    <div class="text-[10px] uppercase font-bold text-gray-500 tracking-widest mb-1">MÃ ĐƠN HÀNG</div>
                    <div class="font-mono font-black text-xl leading-none">REQ-{{ $order->id }}</div>
                    <!-- Fake barcode -->
                    <div class="h-10 bg-black w-full mt-2" style="background: repeating-linear-gradient(90deg, #000, #000 2px, #fff 2px, #fff 4px, #000 4px, #000 6px, #fff 6px, #fff 9px, #000 9px, #000 12px, #fff 12px, #fff 14px);"></div>
                    <div class="text-[8px] tracking-[0.2em] mt-1 font-mono text-center">REQ{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
            
            <!-- Sender / Receiver -->
            <div class="flex border-b-2 border-black pb-3 mb-3 gap-3 shrink-0">
                <div class="w-1/2 border-r-2 border-black pr-3">
                    <div class="font-black uppercase text-[10px] mb-1 bg-black text-white inline-block px-1">Từ (Người gửi):</div>
                    <div class="font-bold text-sm leading-tight mt-1">{{ $order->sender_name ?? 'Cửa hàng mặc định' }}</div>
                    <div class="text-xs font-mono mt-0.5">{{ $order->sender_phone ?? '09xx.xxx.xxx' }}</div>
                    <div class="text-xs mt-1 leading-snug line-clamp-3">{{ $order->sender_address ?? 'Địa chỉ cửa hàng' }}</div>
                </div>
                <div class="w-1/2 pl-1">
                    <div class="font-black uppercase text-[10px] mb-1 bg-black text-white inline-block px-1">Đến (Người nhận):</div>
                    <div class="font-bold text-sm leading-tight mt-1">{{ $order->receiver_name }}</div>
                    <div class="text-xs font-mono mt-0.5 font-bold">{{ $order->receiver_phone }}</div>
                    <div class="text-xs mt-1 leading-snug line-clamp-3">{{ $order->receiver_address }}</div>
                </div>
            </div>
            
            <!-- Details -->
            <div class="border-b-2 border-black pb-3 mb-3 shrink-0 flex-grow">
                <div class="font-black uppercase text-[10px] text-gray-500 mb-1">Nội dung hàng hóa:</div>
                <div class="font-bold text-sm leading-tight">{{ $order->product_name }}</div>
                
                <div class="grid grid-cols-2 gap-2 mt-3">
                    <div class="border border-black p-1 text-center">
                        <div class="text-[9px] uppercase text-gray-500 font-bold">Khối lượng</div>
                        <div class="font-black">{{ $order->weight ?? 0 }} g</div>
                    </div>
                    <div class="border border-black p-1 text-center">
                        <div class="text-[9px] uppercase text-gray-500 font-bold">Số kiện</div>
                        <div class="font-black">1</div>
                    </div>
                </div>
            </div>
            
            <!-- COD & Note -->
            <div class="flex gap-3 mb-4 shrink-0">
                <div class="w-1/2 border-2 border-black p-2 text-center bg-gray-50 flex flex-col justify-center">
                    <div class="font-black uppercase text-[10px] mb-1">Tiền thu người nhận (COD)</div>
                    <div class="text-xl font-black mt-1">{{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }} đ</div>
                </div>
                <div class="w-1/2 p-1">
                    <div class="font-black uppercase text-[10px] text-gray-500 mb-1">Chỉ dẫn giao hàng:</div>
                    <div class="text-xs font-bold leading-tight">{{ $order->note ?: 'Không có ghi chú' }}</div>
                </div>
            </div>
            
            <!-- Footer Signature -->
            <div class="absolute bottom-4 left-4 right-4 flex justify-between text-[10px] text-center border-t-2 border-black pt-2">
                <div class="w-1/2 border-r-2 border-black font-bold">
                    Chữ ký người gửi<br>
                    <span class="text-gray-400 font-normal italic">(Ký và ghi rõ họ tên)</span>
                </div>
                <div class="w-1/2 font-bold">
                    Chữ ký người nhận<br>
                    <span class="text-gray-400 font-normal italic">(Ký và ghi rõ họ tên)</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <script>
        async function downloadAllPDFs() {
            const btn = document.getElementById('btn-download');
            const btnText = document.getElementById('btn-text');
            const labels = document.querySelectorAll('.shipping-label');
            
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            
            for(let i = 0; i < labels.length; i++) {
                const label = labels[i];
                const id = label.id.split('-')[1];
                
                btnText.textContent = `Đang tạo PDF (${i+1}/${labels.length})...`;

                const opt = {
                    margin:       0,
                    filename:     'VanDon_REQ-' + id + '.pdf',
                    image:        { type: 'jpeg', quality: 1 },
                    html2canvas:  { scale: 3, useCORS: true },
                    jsPDF:        { unit: 'mm', format: 'a6', orientation: 'portrait' }
                };
                
                await html2pdf().set(opt).from(label).save();
                
                // Dừng một chút để trình duyệt không chặn tải xuống nhiều file
                await new Promise(r => setTimeout(r, 800));
            }
            
            btnText.textContent = `Hoàn tất! Đã tải ${labels.length} file.`;
            btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            btn.classList.add('bg-green-600', 'hover:bg-green-700');
            
            setTimeout(() => {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                btnText.textContent = `Lưu thành ${labels.length} file PDF`;
            }, 3000);
        }
    </script>
</body>
</html>
