document.addEventListener("DOMContentLoaded", function() {
    const chatbot = document.getElementById('ai-chatbot-container');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chatbot-messages');

    // 1. LẤY DATA GỐC VÀ TỰ ĐỘNG PHÂN LOẠI
    const orders = window.phpOrders || [];
    let donChuaXuLy = orders.filter(o => ['Chờ điều phối', 'Chờ in đơn', 'Chờ lấy hàng', 'Đang thẩm định AI...'].includes(o.status));
    let donDaXuLy = orders.filter(o => ['Đang giao hàng', 'Giao thành công', 'Giao thất bại'].includes(o.status));

    // --- LOGIC MỞ/ĐÓNG CHAT ---
    const btnOpen = document.getElementById('btn-open-chatbot');
    const btnClose = document.getElementById('close-chatbot');
    if(btnOpen) btnOpen.addEventListener('click', () => chatbot.classList.remove('chatbot-hidden'));
    if(btnClose) btnClose.addEventListener('click', () => chatbot.classList.add('chatbot-hidden'));

    function appendMsg(sender, text) {
        const msgHtml = `
            <div class="msg-${sender} mb-3">
                <span class="badge ${sender === 'ai' ? 'bg-primary' : 'bg-secondary'}">
                    ${sender === 'ai' ? '🤖 THƯ KÝ AI' : '👤 SẾP THÁI'}
                </span>
                <div class="p-2 ${sender === 'ai' ? 'bg-white border-start border-primary border-4' : 'bg-primary text-white'} rounded mt-1 shadow-sm">
                    ${text}
                </div>
            </div>`;

        // 🔴 CHỖ SỬA QUAN TRỌNG: Dùng insertAdjacentHTML thay vì innerHTML +=
        chatMessages.insertAdjacentHTML('beforeend', msgHtml);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    async function aiThinking(seconds = 0.6) {
        const thinkingId = 'thinking-' + Date.now();
        const thinkingHtml = `<div id="${thinkingId}" class="text-muted small mb-2"><i>Thư ký đang dò sổ sách...</i></div>`;

        // 🔴 Sửa luôn ở đây cho chắc cốp
        chatMessages.insertAdjacentHTML('beforeend', thinkingHtml);

        await new Promise(r => setTimeout(r, seconds * 1000));
        const el = document.getElementById(thinkingId);
        if(el) el.remove();
    }

    const secretarialQuestions = [
        "Dạ? Chỗ này hơi ồn sếp nói lại em nghe với?",
        "Em chưa bắt được ý sếp. Sếp muốn check hàng Tồn kho hay muốn Điều xe đi giao luôn ạ?",
        "Sếp dặn gì thế ạ? Em đang mải phân loại hàng dễ vỡ ở góc kho.",
        "Dạ em đây sếp! Sếp cần báo cáo Tổng quan hay muốn Tối ưu tuyến đường luôn ạ?"
    ];

    // --- BỘ NÃO XỬ LÝ CHAT TỔNG HỢP ---
    window.processUserChat = async function() {
        const rawMsg = chatInput.value.toLowerCase().trim();
        if (!rawMsg) return;

        appendMsg('user', chatInput.value);
        chatInput.value = '';
        await aiThinking(0.5);

        const numberMatch = rawMsg.match(/\d+/);
        let count = numberMatch ? parseInt(numberMatch[0]) : null;

        // KỊCH BẢN 1: BÁO CÁO SỐ LƯỢNG (PHÂN LUỒNG)
        if (rawMsg.includes("bao nhiêu") || rawMsg.includes("check") || rawMsg.includes("tình hình") || rawMsg.includes("tổng")) {
            if (rawMsg.includes("chưa")) {
                appendMsg('ai', `Báo cáo sếp Thai, mình đang tồn <b>${donChuaXuLy.length} đơn CHƯA XỬ LÝ</b> ạ. Sếp muốn bốc lên xe luôn không?`);
            } else if (rawMsg.includes("đã") || rawMsg.includes("xong")) {
                appendMsg('ai', `Dạ, shipper đang đi giao <b>${donDaXuLy.length} đơn ĐÃ XỬ LÝ</b> rồi sếp nhé!`);
            } else {
                appendMsg('ai', `Dạ sếp, tổng quan kho VKU:<br>- 📦 <b>${donChuaXuLy.length} đơn chờ xử lý</b><br>- 🚚 <b>${donDaXuLy.length} đơn đang đi giao</b><br>Sếp muốn em <b>mở danh sách</b> cái nào ạ?`);
            }
        }

        // KỊCH BẢN 2: LIỆT KÊ DANH SÁCH CHI TIẾT
        else if (rawMsg.includes("danh sách") || rawMsg.includes("liệt kê") || rawMsg.includes("mở")) {
            let targetArray = rawMsg.includes("đã") ? donDaXuLy : donChuaXuLy;
            let statusText = rawMsg.includes("đã") ? "ĐÃ ĐI GIAO" : "CHƯA XỬ LÝ";
            let finalCount = count || targetArray.length;

            if (finalCount === 0) {
                appendMsg('ai', `Dạ sếp, hiện không có đơn nào ở trạng thái ${statusText} ạ.`);
                return;
            }

            let listMsg = `Dạ sếp, em gửi danh sách <b>${finalCount} đơn ${statusText}</b>:<br><hr>`;
            targetArray.slice(0, finalCount).forEach((o, i) => {
                listMsg += `<div class="mb-2 p-2 border-bottom"><b>${i+1}. 📦 #${o.id}</b> - ${o.product_name}<br>📍 Tới: ${o.receiver_address || '...'}</div>`;
            });
            appendMsg('ai', listMsg + "<br>Sếp ngó qua nhé, chốt đơn nào thì bảo em <b>Tối ưu</b>!");
        }

        // KỊCH BẢN 3: ĐIỀU XE TỐI ƯU (DÙNG ĐƠN ĐÃ XỬ LÝ NHƯ SẾP YÊU CẦU)
        else if (rawMsg.includes("tối ưu") || rawMsg.includes("chốt") || rawMsg.includes("điều xe")) {
            let num = count;
            let isRecent = rawMsg.includes("gần nhất") || rawMsg.includes("mới nhất");

            // --- CHỖ SỬA: Lấy từ mảng 'orders' (tất cả) để không sót đơn mới tạo ---
            let pool = [...orders];

            if (rawMsg.includes("hết") || rawMsg.includes("tất cả")) num = pool.length;

            if (pool.length === 0) {
                appendMsg('ai', "Sếp ơi, kho đang trống trơn, không có đơn nào để tối ưu ạ!");
                return;
            }

            if (!num) {
                appendMsg('ai', `Đang có <b>${pool.length} đơn hàng</b>. Sếp muốn tối ưu mấy đơn ạ?`);
                return;
            }

            // Sắp xếp đơn hàng: ID lớn nhất (mới nhất) lên đầu
            pool.sort((a, b) => {
                const idA = parseInt(a.id.toString().replace(/\D/g, ''));
                const idB = parseInt(b.id.toString().replace(/\D/g, ''));
                return idB - idA;
            });

            let textResponse = isRecent
                ? `🚀 <b>Rõ!</b> Em bốc <b>${num} đơn vừa mới tạo nhất</b> sang trạm AI n8n.`
                : `🚀 <b>Rõ!</b> Em đang gửi <b>${num} đơn</b> sang trạm AI n8n.`;

            appendMsg('ai', `${textResponse} Sếp chờ em vẽ radar nhé!`);
            await aiThinking(2);

            try {
                // Lấy N đơn hàng từ đầu danh sách đã sắp xếp (luôn là đơn mới nhất)
                const selectedOrders = pool.slice(0, num);

                const payloadOrders = selectedOrders.map(order => ({
                    ...order,
                    // Ưu tiên tọa độ thật từ Database sếp vừa thêm, nếu không có mới dùng rand
                    lat: order.receiver_lat ? parseFloat(order.receiver_lat) : parseFloat((16.05 + Math.random() * 0.05).toFixed(4)),
                    lng: order.receiver_lng ? parseFloat(order.receiver_lng) : parseFloat((108.20 + Math.random() * 0.05).toFixed(4)),
                    s_lat: order.sender_lat ? parseFloat(order.sender_lat) : 16.03,
                    s_lng: order.sender_lng ? parseFloat(order.sender_lng) : 108.18
                }));

                const response = await fetch('http://localhost:5678/webhook-test/toi-uu-logistics', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ orders: payloadOrders })
                });

                const result = await response.json();
                const aiData = result[0]?.json || result;

                if(window.renderChatbotData) window.renderChatbotData(aiData);

                let weatherS = aiData.isRaining
                    ? `<div class="alert alert-info mt-2">🌧️ Đà Nẵng đang mưa, em đã tự cộng phụ phí cho sếp!</div>`
                    : `<div class="alert alert-success mt-2">☀️ Trời đẹp, shipper VKU có thể tăng tốc!</div>`;

                appendMsg('ai', `${aiData.chatbot_message}${weatherS}`);
            } catch (e) {
                appendMsg('ai', "❌ Lỗi kết nối n8n. Sếp check lại container n8n (cổng 5678) nhé!");
            }
        }

        // KỊCH BẢN 4: EASTER EGGS (VỪA KHÔI PHỤC)
        else if (rawMsg.includes("tác giả") || rawMsg.includes("thái") || rawMsg.includes("lê")) {
            appendMsg('ai', "Dạ, em là trợ lý độc quyền được lập trình bởi sếp <b>Thái (Lê)</b> - Trùm cuối Logistics ạ!");
        }
        else if (rawMsg.includes("cô cẩm")) {
            appendMsg('ai', "Em xin kính chào cô ạ! Hệ thống đang chạy rất mượt nhờ sự hướng dẫn của cô đấy ạ!");
        }

        // KỊCH BẢN 5: KHÔNG HIỂU -> HỎI LẠI NGẪU NHIÊN
        else {
            const randomQuestion = secretarialQuestions[Math.floor(Math.random() * secretarialQuestions.length)];
            appendMsg('ai', randomQuestion);
        }
    };

    chatInput.addEventListener("keypress", (e) => { if (e.key === "Enter") processUserChat(); });
});

// =========================================
// HÀM VẼ RADAR (GIỮ NGUYÊN)
// =========================================
window.renderChatbotData = function(aiData) {
    const mapHtml = `
        <div class="mt-3 p-2 bg-slate-900 rounded-lg border border-slate-700">
            <p class="text-[10px] text-blue-400 font-bold mb-2 uppercase tracking-widest">📡 Lộ trình tối ưu P2P (Shortest Path)</p>
            <div class="mini-map-container" style="position: relative; width: 100%; height: 300px; background: #0f172a; overflow: hidden;">
                <svg class="map-svg" style="position:absolute; width:100%; height:100%; pointer-events:none; z-index:10;"></svg>
                <div class="shipper-dot" style="opacity:0; z-index:30; position:absolute; width:12px; height:12px; background:#00ffcc; border-radius:50%; transform:translate(-50%, -50%); box-shadow:0 0 10px #00ffcc;"></div>
                <div class="map-node node-depot" style="z-index:25; position:absolute; transform:translate(-50%, -50%);">📍</div>
            </div>
            <button class="btn-run-animation w-full mt-2 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 transition-all">
                ▶ KHỞI CHẠY GIAO HÀNG
            </button>
        </div>`;

    const aiMsgs = document.querySelectorAll('.msg-ai');
    const lastAiMsg = aiMsgs[aiMsgs.length - 1].querySelector('.p-2');
    lastAiMsg.insertAdjacentHTML('beforeend', mapHtml);

    const currentMap = lastAiMsg.querySelector('.mt-3:last-child');
    const mapBox = currentMap.querySelector('.mini-map-container');
    const svg = currentMap.querySelector('.map-svg');
    const shipper = currentMap.querySelector('.shipper-dot');
    const btn = currentMap.querySelector('.btn-run-animation');
    let fullRoute = [];

    if (aiData.map_data && aiData.map_data.trips) {
        const startX = (aiData.map_data.depot.x / 500) * 100;
        const startY = (aiData.map_data.depot.y / 500) * 100;
        const depotNode = currentMap.querySelector('.node-depot');

        // (Đã xóa dòng bgColor đi lạc ở đây)

        depotNode.style.left = startX + '%';
        depotNode.style.top = startY + '%';
        fullRoute.push({ x: startX, y: startY });

        aiData.map_data.trips.forEach(trip => {
            trip.route.forEach(point => {
                const nodeX = (point.x / 500) * 100;
                const nodeY = (point.y / 500) * 100;
                const node = document.createElement('div');
                node.className = 'map-node';
                node.innerHTML = point.step;

                // 🔴 1. CHUYỂN DÒNG MÀU VÀO TRONG VÒNG LẶP (Bắt đúng bệnh)
                const bgColor = point.type === "PICK" ? "#f97316" : "#3b82f6";

                node.style.cssText = `position:absolute; left:${nodeX}%; top:${nodeY}%; transform:translate(-50%, -50%); width:20px; height:20px; background:${bgColor}; color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; z-index:20;`;

                // 🔴 2. THÊM LẠI DÒNG NÀY ĐỂ DÁN NÚT LÊN BẢN ĐỒ
                mapBox.appendChild(node);

                fullRoute.push({ x: nodeX, y: nodeY });
            });
        });

        fullRoute.push({ x: startX, y: startY });

        let pathD = `M ${(startX * (mapBox.clientWidth/100))} ${(startY * 3)}`;
        fullRoute.forEach((p, i) => { if(i>0) pathD += ` L ${(p.x * (mapBox.clientWidth/100))} ${(p.y * 3)}`; });
        svg.innerHTML = `<path d="${pathD}" fill="none" stroke="#3b82f6" stroke-width="2" stroke-dasharray="5,5" opacity="0.6" />`;
    }

    btn.addEventListener('click', function() {
        this.innerHTML = "🚚 ĐANG DI CHUYỂN...";
        shipper.style.left = fullRoute[0].x + '%';
        shipper.style.top = fullRoute[0].y + '%';
        shipper.style.opacity = "1";
        let step = 1;
        function moveNext() {
            if (step >= fullRoute.length) { btn.innerHTML = "✅ XONG"; return; }
            shipper.style.left = fullRoute[step].x + '%';
            shipper.style.top = fullRoute[step].y + '%';
            step++;
            setTimeout(moveNext, 1000);
        }
        setTimeout(moveNext, 500);
    });
};
