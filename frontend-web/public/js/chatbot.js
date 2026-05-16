document.addEventListener("DOMContentLoaded", function() {
    const chatbot = document.getElementById('ai-chatbot-container');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chatbot-messages');
    const btnOpen = document.getElementById('btn-open-chatbot');
    const btnClose = document.getElementById('close-chatbot');

    // 1. KHÔI PHỤC TRẠNG THÁI & LỊCH SỬ CHAT
    const savedOpen = sessionStorage.getItem('chatbotOpen');
    if (savedOpen === 'true') {
        chatbot.classList.remove('chatbot-hidden');
    }

    const savedHistory = sessionStorage.getItem('chatHistory');
    if (savedHistory) {
        chatMessages.innerHTML = savedHistory;
        chatMessages.scrollTop = chatMessages.scrollHeight;
    } else {
        // Lời chào mặc định nếu lần đầu mở
        setTimeout(() => {
            if (chatMessages.innerHTML.trim() === "") {
                appendMsg('ai', "Chào sếp Thái! Em là trợ lý Logistics VKU. Sếp dặn gì em nghe ạ! 🫡");
            }
        }, 1000);
    }

    function saveChat() {
        sessionStorage.setItem('chatHistory', chatMessages.innerHTML);
    }

    // 2. LẤY DATA GỐC VÀ TỰ ĐỘNG PHÂN LOẠI
    const orders = window.phpOrders || [];
    let donChuaXuLy = orders.filter(o => ['Chờ điều phối', 'Chờ in đơn', 'Chờ lấy hàng', 'Đang thẩm định AI...'].includes(o.status));
    let donDaXuLy = orders.filter(o => ['Đang giao hàng', 'Giao thành công', 'Giao thất bại'].includes(o.status));

    // --- LOGIC MỞ/ĐÓNG CHAT ---
    if(btnOpen) {
        btnOpen.addEventListener('click', () => {
            chatbot.classList.remove('chatbot-hidden');
            sessionStorage.setItem('chatbotOpen', 'true');
        });
    }
    if(btnClose) {
        btnClose.addEventListener('click', () => {
            chatbot.classList.add('chatbot-hidden');
            sessionStorage.setItem('chatbotOpen', 'false');
        });
    }

    function appendMsg(sender, text) {
        const msgHtml = `
            <div class="msg-${sender} mb-3">
                <span class="badge ${sender === 'ai' ? 'bg-primary' : 'bg-secondary'}">
                    ${sender === 'ai' ? '🤖 THƯ KÝ AI' : '👤 SẾP THÁI'}
                </span>
                <div class="p-2 rounded mt-1 shadow-sm" style="${sender === 'ai' ? 'background: #1e293b; color: #38bdf8; border-left: 4px solid #3b82f6;' : 'background: #334155; color: #fbbf24;'}">
                    ${text}
                </div>
            </div>`;

        chatMessages.insertAdjacentHTML('beforeend', msgHtml);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        saveChat(); // Lưu lại ngay
    }

    async function aiThinking(seconds = 0.6) {
        const thinkingId = 'thinking-' + Date.now();
        const thinkingHtml = `<div id="${thinkingId}" class="text-muted small mb-2"><i>Thư ký đang dò sổ sách...</i></div>`;
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

    // --- BỘ NÃO XỬ LÝ CHAT TỔNG HỢP (GIỮ NGUYÊN LOGIC SẾP) ---
    window.processUserChat = async function() {
        const rawMsg = chatInput.value.toLowerCase().trim();
        if (!rawMsg) return;

        const originalMsg = chatInput.value;
        chatInput.value = '';
        appendMsg('user', originalMsg);
        await aiThinking(0.5);

        const numberMatch = rawMsg.match(/\d+/);
        let count = numberMatch ? parseInt(numberMatch[0]) : null;

        // KỊCH BẢN 1: BÁO CÁO SỐ LƯỢNG
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
            } else {
                let listMsg = `Dạ sếp, em gửi danh sách <b>${finalCount} đơn ${statusText}</b>:<br><hr>`;
                targetArray.slice(0, finalCount).forEach((o, i) => {
                    listMsg += `<div class="mb-2 p-2 border-bottom"><b>${i+1}. 📦 #${o.id}</b> - ${o.product_name}<br>📍 Tới: ${o.receiver_address || '...'}</div>`;
                });
                appendMsg('ai', listMsg + "<br>Sếp ngó qua nhé, chốt đơn nào thì bảo em <b>Tối ưu</b>!");
            }
        }

        // KỊCH BẢN 3: ĐIỀU XE TỐI ƯU
        else if (rawMsg.includes("tối ưu") || rawMsg.includes("chốt") || rawMsg.includes("điều xe")) {
            let num = count;
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

            appendMsg('ai', `🚀 <b>Rõ!</b> Em đang gửi <b>${num} đơn</b> sang trạm AI n8n. Sếp chờ em vẽ radar nhé!`);
            await aiThinking(2);

            try {
                const selectedOrders = pool.slice(0, num);
                const response = await fetch('http://103.118.29.21:5678/webhook/toi-uu-logistics', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ orders: selectedOrders })
                });

                const result = await response.json();
                const aiData = result[0]?.json || result;
                if(window.renderChatbotData) window.renderChatbotData(aiData);
                appendMsg('ai', aiData.chatbot_message || "Đã vẽ lộ trình tối ưu cho sếp!");
            } catch (e) {
                appendMsg('ai', "❌ Lỗi kết nối n8n. Sếp check lại container n8n nhé!");
            }
        }

        // KỊCH BẢN 4: EASTER EGGS
        else if (rawMsg.includes("tác giả") || rawMsg.includes("thái") || rawMsg.includes("lê")) {
            appendMsg('ai', "Dạ, em là trợ lý độc quyền được lập trình bởi sếp <b>Thái (Lê)</b> - Trùm cuối Logistics ạ!");
        } else if (rawMsg.includes("cô cẩm")) {
            appendMsg('ai', "Em xin kính chào cô ạ! Hệ thống đang chạy rất mượt nhờ sự hướng dẫn của cô đấy ạ!");
        }

        // KỊCH BẢN 5: KHÔNG HIỂU
        else {
            const randomQuestion = secretarialQuestions[Math.floor(Math.random() * secretarialQuestions.length)];
            appendMsg('ai', randomQuestion);
        }
        
        saveChat(); // Lưu lại sau khi AI trả lời
    };

    chatInput.addEventListener("keypress", (e) => { if (e.key === "Enter") processUserChat(); });
});

// ============================================================
// HÀM VẼ BẢN ĐỒ RADAR - ĐÃ VIẾT LẠI HOÀN TOÀN
// ============================================================
window.renderChatbotData = function(aiData) {
    const mapData = aiData.map_data;
    if (!mapData) return;

    const trips = mapData.trips || [];
    const depot = mapData.depot || { x: 50, y: 50 };

    // --- Thu thập tất cả các điểm giao hàng từ mọi chuyến ---
    let allPoints = []; // { x, y, label, color, tripIdx }
    trips.forEach((trip, ti) => {
        (trip.route || []).forEach(pt => {
            allPoints.push({
                x: pt.x, y: pt.y,
                label: pt.step || (pt.id ? String(pt.id).slice(-3) : '?'),
                color: trip.color || '#3b82f6',
                tripIdx: ti,
                step: pt.step
            });
        });
    });

    // --- Nếu KHÔNG có tọa độ thực, tạo bố cục vòng tròn ---
    const MAP_W = 280, MAP_H = 220;
    const DEPOT_X = MAP_W / 2, DEPOT_Y = MAP_H / 2;
    const needsLayout = allPoints.every(p => !p.x && !p.y);

    if (needsLayout) {
        const total = allPoints.length;
        const radius = Math.min(MAP_W, MAP_H) * 0.36;
        allPoints.forEach((pt, i) => {
            // Trải đều theo vòng tròn, bắt đầu từ góc trên (270 độ)
            const angle = (270 + (360 / total) * i) * (Math.PI / 180);
            pt.x = Math.round(DEPOT_X + radius * Math.cos(angle));
            pt.y = Math.round(DEPOT_Y + radius * Math.sin(angle));
        });
    } else {
        // Có tọa độ thực (0-500 range) → scale về MAP_W x MAP_H
        const xs = allPoints.map(p => p.x).filter(Boolean);
        const ys = allPoints.map(p => p.y).filter(Boolean);
        const minX = Math.min(...xs, depot.x || 250);
        const maxX = Math.max(...xs, depot.x || 250);
        const minY = Math.min(...ys, depot.y || 250);
        const maxY = Math.max(...ys, depot.y || 250);
        const padX = (maxX - minX) || 1;
        const padY = (maxY - minY) || 1;
        const scale = (v, min, pad, out) => Math.round(20 + ((v - min) / pad) * (out - 40));
        allPoints.forEach(pt => {
            pt.x = scale(pt.x, minX, padX, MAP_W);
            pt.y = scale(pt.y, minY, padY, MAP_H);
        });
        depot.x = scale(depot.x || 250, minX, padX, MAP_W);
        depot.y = scale(depot.y || 250, minY, padY, MAP_H);
    }

    // --- Nhóm lại điểm theo từng chuyến để vẽ đường ---
    let tripRoutes = trips.map((trip, ti) => {
        const pts = allPoints.filter(p => p.tripIdx === ti).sort((a, b) => a.step - b.step);
        return { color: trip.color || '#3b82f6', name: trip.name, pts };
    });

    // --- Xây HTML bản đồ ---
    const depX = needsLayout ? DEPOT_X : (depot.x || DEPOT_X);
    const depY = needsLayout ? DEPOT_Y : (depot.y || DEPOT_Y);

    // Tạo SVG path cho từng chuyến: KHO → điểm1 → điểm2 → ... → KHO
    let svgLines = '';
    tripRoutes.forEach(tr => {
        if (tr.pts.length === 0) return;
        const pathPts = [[depX, depY], ...tr.pts.map(p => [p.x, p.y]), [depX, depY]];
        const d = pathPts.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p[0]} ${p[1]}`).join(' ');
        svgLines += `<path d="${d}" stroke="${tr.color}" stroke-width="1.5" stroke-dasharray="4 3" fill="none" opacity="0.7"/>`;
    });

    // Tạo HTML các node điểm giao
    let nodesHtml = '';
    allPoints.forEach(pt => {
        nodesHtml += `
            <div style="position:absolute; left:${pt.x}px; top:${pt.y}px; transform:translate(-50%,-50%); z-index:20; text-align:center;">
                <div style="width:22px;height:22px;border-radius:50%;background:${pt.color};border:2px solid #fff;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:bold;color:#fff;box-shadow:0 0 6px ${pt.color}88;">${pt.label}</div>
            </div>`;
    });

    const mapHtml = `
        <div class="chatbot-map-wrap mt-3 p-2 rounded-lg" style="background:#0f172a; border:1px solid #1e3a5f;">
            <p style="font-size:10px;color:#60a5fa;font-weight:bold;margin-bottom:6px;letter-spacing:1px;">📡 LỘ TRÌNH TỐI ƯU AI (${allPoints.length} điểm giao)</p>
            <div class="mini-map-container" style="position:relative; width:${MAP_W}px; height:${MAP_H}px; background:#0a1628; overflow:hidden; border-radius:8px; border:1px solid #1e3a5f; margin:0 auto;">
                <!-- SVG lines -->
                <svg style="position:absolute;width:100%;height:100%;top:0;left:0;z-index:5;" viewBox="0 0 ${MAP_W} ${MAP_H}">
                    ${svgLines}
                </svg>
                <!-- Shipper dot (animated) -->
                <div class="chatbot-shipper-dot" style="opacity:0;position:absolute;left:${depX}px;top:${depY}px;width:14px;height:14px;background:#00ffcc;border-radius:50%;transform:translate(-50%,-50%);z-index:30;box-shadow:0 0 12px #00ffcc, 0 0 4px #fff;transition:left 0.6s ease, top 0.6s ease;"></div>
                <!-- Depot node -->
                <div style="position:absolute;left:${depX}px;top:${depY}px;transform:translate(-50%,-50%);z-index:25;font-size:16px;" title="KHO TỔNG">🏭</div>
                <!-- Delivery nodes -->
                ${nodesHtml}
            </div>
            <!-- Legend -->
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px;">
                ${tripRoutes.map(tr => `<span style="font-size:9px;color:${tr.color};background:${tr.color}22;padding:2px 6px;border-radius:10px;border:1px solid ${tr.color}55;">${tr.name}</span>`).join('')}
            </div>
            <button class="chatbot-run-btn" style="width:100%;margin-top:8px;padding:5px;background:#1d4ed8;color:#fff;font-size:11px;font-weight:bold;border:none;border-radius:6px;cursor:pointer;">▶ KHỞI CHẠY ANIMATION</button>
        </div>`;

    const aiMsgs = document.querySelectorAll('.msg-ai');
    const lastAiMsg = aiMsgs[aiMsgs.length - 1].querySelector('.p-2');
    lastAiMsg.insertAdjacentHTML('beforeend', mapHtml);

    // --- Gắn animation cho nút KHỞI CHẠY vừa tạo ---
    const mapWrap = lastAiMsg.querySelector('.chatbot-map-wrap');
    const runBtn = mapWrap.querySelector('.chatbot-run-btn');
    const shipperDot = mapWrap.querySelector('.chatbot-shipper-dot');

    // Xây chuỗi waypoints: KHO → tất cả điểm theo thứ tự chuyến → KHO
    const waypoints = [{ x: depX, y: depY }];
    tripRoutes.forEach(tr => {
        tr.pts.forEach(pt => waypoints.push({ x: pt.x, y: pt.y }));
        waypoints.push({ x: depX, y: depY }); // Về kho sau mỗi chuyến
    });

    runBtn.addEventListener('click', function() {
        runBtn.disabled = true;
        runBtn.textContent = '⏳ Đang chạy...';
        shipperDot.style.opacity = '1';

        let step = 0;
        function moveNext() {
            if (step >= waypoints.length) {
                runBtn.disabled = false;
                runBtn.textContent = '🔁 CHẠY LẠI';
                return;
            }
            const wp = waypoints[step];
            shipperDot.style.left = wp.x + 'px';
            shipperDot.style.top = wp.y + 'px';
            step++;
            setTimeout(moveNext, 700);
        }
        // Reset về kho trước rồi chạy
        shipperDot.style.left = depX + 'px';
        shipperDot.style.top = depY + 'px';
        setTimeout(moveNext, 300);
    });

    sessionStorage.setItem('chatHistory', document.getElementById('chatbot-messages').innerHTML);
};
