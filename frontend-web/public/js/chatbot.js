document.addEventListener("DOMContentLoaded", function() {
    const chatbot = document.getElementById('ai-chatbot-container');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chatbot-messages');

    // 1. LẤY DATA GỐC TỪ LARAVEL
    const orders = window.phpOrders || [];

    // 2. BOT TỰ ĐỘNG PHÂN LOẠI ĐƠN HÀNG (GIỐNG HỆT GIAO DIỆN CỦA HẢI)
    // Các trạng thái này phải khớp chính xác với chữ trong file web.php
    let donChuaXuLy = orders.filter(o => ['Chờ điều phối', 'Chờ in đơn', 'Chờ lấy hàng', 'Đang thẩm định AI...'].includes(o.status));
    let donDaXuLy = orders.filter(o => ['Đang giao hàng', 'Giao thành công', 'Giao thất bại'].includes(o.status));

    // --- LOGIC MỞ/ĐÓNG CHAT ---
    const btnOpen = document.getElementById('btn-open-chatbot');
    const btnClose = document.getElementById('close-chatbot');

    if(btnOpen) {
        btnOpen.addEventListener('click', () => {
            chatbot.classList.remove('chatbot-hidden');
            console.log("Đã mở chatbot cho sếp Thai!");
        });
    }

    if(btnClose) {
        btnClose.addEventListener('click', () => {
            chatbot.classList.add('chatbot-hidden');
        });
    }

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
        chatMessages.innerHTML += msgHtml;
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    async function aiThinking(seconds = 0.6) {
        const thinkingId = 'thinking-' + Date.now();
        chatMessages.innerHTML += `<div id="${thinkingId}" class="text-muted small mb-2"><i>Thư ký đang dò sổ sách...</i></div>`;
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

    // --- BỘ NÃO XỬ LÝ CHAT THỰC TẾ ---
    window.processUserChat = async function() {
        const rawMsg = chatInput.value.toLowerCase().trim();
        if (!rawMsg) return;

        appendMsg('user', chatInput.value);
        chatInput.value = '';
        await aiThinking(0.5);

        // Bắt số lượng sếp yêu cầu
        const numberMatch = rawMsg.match(/\d+/);
        let count = numberMatch ? parseInt(numberMatch[0]) : null;

        // KỊCH BẢN 1: BÁO CÁO SỐ LƯỢNG (PHÂN LUỒNG)
        if (rawMsg.includes("bao nhiêu") || rawMsg.includes("check") || rawMsg.includes("tình hình") || rawMsg.includes("tổng")) {
            if (rawMsg.includes("chưa")) {
                appendMsg('ai', `Báo cáo sếp Thai, mình đang tồn <b>${donChuaXuLy.length} đơn CHƯA XỬ LÝ</b> (chờ in đơn/điều phối) ạ. Sếp muốn bốc lên xe luôn không?`);
            } else if (rawMsg.includes("đã") || rawMsg.includes("xong")) {
                appendMsg('ai', `Dạ, shipper đang đi giao <b>${donDaXuLy.length} đơn ĐÃ XỬ LÝ</b> rồi sếp nhé!`);
            } else {
                // Hỏi chung chung thì báo cáo tổng hợp
                appendMsg('ai', `Dạ sếp, tổng quan kho VKU hôm nay:<br>- 📦 <b>${donChuaXuLy.length} đơn chờ xử lý</b><br>- 🚚 <b>${donDaXuLy.length} đơn đang đi giao</b><br>Sếp muốn em <b>mở danh sách</b> cái nào ạ?`);
            }
        }

        // KỊCH BẢN 2: LIỆT KÊ DANH SÁCH CHI TIẾT
        else if (rawMsg.includes("danh sách") || rawMsg.includes("liệt kê") || rawMsg.includes("mở")) {
            let targetArray = donChuaXuLy; // Mặc định lấy đơn chưa xử lý
            let statusText = "CHƯA XỬ LÝ";

            if (rawMsg.includes("đã") || rawMsg.includes("xong")) {
                targetArray = donDaXuLy;
                statusText = "ĐÃ ĐI GIAO";
            }

            let finalCount = count || targetArray.length;

            if (finalCount === 0) {
                appendMsg('ai', `Dạ sếp, hiện không có đơn nào ở trạng thái ${statusText} ạ.`);
                return;
            }

            let listMsg = `Dạ sếp, em gửi danh sách <b>${finalCount} đơn ${statusText}</b>:<br><hr>`;
            targetArray.slice(0, finalCount).forEach((o, i) => {
                listMsg += `
                    <div class="order-item mb-2 p-2 border-bottom">
                        <b>${i+1}. 📦 Đơn #${o.id} - ${o.product_name}</b><br>
                        👤 Nhận: ${o.receiver_name || 'Chưa có tên'}<br>
                        📍 Tới: ${o.receiver_address || 'Đang cập nhật...'}<br>
                        ⚖️ Nặng: <b>${o.weight || '0'} gram</b>
                    </div>`;
            });
            appendMsg('ai', listMsg + "<br>Sếp ngó qua nhé, chốt đơn nào thì bảo em <b>Tối ưu</b>!");
        }

        // KỊCH BẢN 3: ĐIỀU XE TỐI ƯU (CHỈ LẤY ĐƠN CHƯA XỬ LÝ)
        else if (rawMsg.includes("tối ưu") || rawMsg.includes("chốt") || rawMsg.includes("điều xe")) {
            let num = count;
            if (rawMsg.includes("hết") || rawMsg.includes("tất cả")) num = donDaXuLy.length;

            if (donDaXuLy.length === 0) {
                appendMsg('ai', "Sếp ơi, không có đơn ĐÃ XỬ LÝ nào để mang đi tối ưu cả!");
                return;
            }

            if (!num) {
                appendMsg('ai', `Đang có <b>${donDaXuLy.length} đơn đã xử lý</b>. Sếp muốn mang mấy đơn đi tối ưu lộ trình?`);
                return;
            }

            if (num > donDaXuLy.length) num = donDaXuLy.length;

            appendMsg('ai', `🚀 <b>Rõ thưa sếp!</b> Em đang bốc <b>${num} đơn ĐÃ XỬ LÝ</b> sang trạm AI n8n. Sếp đợi em vẽ lộ trình nhé!`);
            await aiThinking(2);

            try {
                // LẤY ĐƠN ĐÃ XỬ LÝ ĐỂ BƠM TỌA ĐỘ ẢO VÀ GỬI ĐI
                const payloadOrders = donDaXuLy.slice(0, num).map(order => ({
                    ...order,
                    lat: order.lat || parseFloat((16.05 + Math.random() * 0.05).toFixed(4)),
                    lng: order.lng || parseFloat((108.20 + Math.random() * 0.05).toFixed(4))
                }));

                const response = await fetch('http://localhost:5678/webhook-test/toi-uu-logistics', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ orders: payloadOrders })
                });

                // Cập nhật lại kho (trừ đi đơn đã gửi)
                donDaXuLy.splice(0, num);

                const result = await response.json();
                const aiData = result[0]?.json || result;

                if(window.renderChatbotData) window.renderChatbotData(aiData);

                let weatherS = aiData.isRaining
                    ? `<div class="alert alert-info mt-2">🌧️ Báo cáo sếp, Đà Nẵng đang mưa, lộ trình có vẻ trơn trượt đấy ạ!</div>`
                    : `<div class="alert alert-success mt-2">☀️ Thời tiết cực kỳ ủng hộ việc đi ship hàng thưa sếp!</div>`;
                appendMsg('ai', `${aiData.chatbot_message}${weatherS}`);
            } catch (e) {
                console.error("LỖI KẾT NỐI N8N:", e);
                appendMsg('ai', "❌ Lỗi đọc kết quả lộ trình, sếp F12 kiểm tra đường truyền n8n nhé!");
            }
        }

        // KỊCH BẢN 4: GIAO TIẾP VỚI KHÁCH VVIP
        else if (rawMsg.includes("tác giả") || rawMsg.includes("thái") || rawMsg.includes("lê")) {
            appendMsg('ai', "Dạ, em là trợ lý độc quyền được lập trình bởi sếp <b>Thái (Lê)</b> - Trùm cuối Logistics VKU ạ!");
        }
        else if (rawMsg.includes("cô cẩm")) {
            appendMsg('ai', "Em xin kính chào cô ạ! Hệ thống đang chạy rất mượt nhờ sự hướng dẫn của cô đấy ạ!");
        }

        // KHÔNG HIỂU -> HỎI LẠI
        else {
            const randomQuestion = secretarialQuestions[Math.floor(Math.random() * secretarialQuestions.length)];
            appendMsg('ai', randomQuestion);
        }
    };

    chatInput.addEventListener("keypress", (e) => { if (e.key === "Enter") processUserChat(); });
});
