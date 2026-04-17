document.addEventListener("DOMContentLoaded", function() {
    const chatbot = document.getElementById('ai-chatbot-container');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chatbot-messages');
    const orders = window.phpOrders || []; // Dữ liệu thật từ Laravel

    // 1. LOGIC MỞ/ĐÓNG CHAT (Cái này phải chạy đầu tiên)
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
        chatMessages.innerHTML += `<div id="${thinkingId}" class="text-muted small mb-2"><i>Thư ký đang ghi chép...</i></div>`;
        await new Promise(r => setTimeout(r, seconds * 1000));
        const el = document.getElementById(thinkingId);
        if(el) el.remove();
    }

    // --- KHO ỨNG BIẾN KHI KHÔNG HIỂU ---
    const secretarialQuestions = [
        "Dạ? Sếp nói gì em chưa kịp load, hay là sếp đang mải ngắm con Razer nên nhắn nhầm?",
        "Câu này khó quá, sếp có muốn em gọi cứu viện không?",
        "Sếp dặn lại đi, em đang bận đếm đơn ở VKU nên hơi xao nhãng. Sếp muốn check đơn hay tối ưu?",
        "Sếp nhắn gì lạ thế? Có liên quan đến đồ án SQA của anh em mình không sếp?"
    ];

    // --- 2. BỘ NÃO XỬ LÝ CHAT (Gộp làm 1 hàm duy nhất) ---
    window.processUserChat = async function() {
        const rawMsg = chatInput.value.toLowerCase().trim();
        if (!rawMsg) return;

        appendMsg('user', chatInput.value);
        chatInput.value = '';
        await aiThinking(0.5);

        // BẮT MỌI CON SỐ SẾP NHẮN (7, 12, 100...)
        const numberMatch = rawMsg.match(/\d+/);
        let count = numberMatch ? parseInt(numberMatch[0]) : null;

        // A. Kịch bản Check kho

        if (rawMsg.includes("bao nhiêu") || rawMsg.includes("check") || rawMsg.includes("tình hình")) {
                    appendMsg('ai', `Báo cáo sếp Thai, tổng kho VKU hiện có <b>${orders.length} đơn</b>. Sếp muốn em <b>mở danh sách</b> chi tiết không?`);
                }

                // B. MỚI: Kịch bản LIỆT KÊ DANH SÁCH (Cái sếp đang thiếu nè!)
                else if (rawMsg.includes("danh sách") || rawMsg.includes("liệt kê") || rawMsg.includes("mở")) {
                            let finalCount = count || orders.length;
                            let listMsg = `Dạ sếp, đây là <b>${finalCount} đơn hàng</b> chi tiết tại VKU:<br><hr>`;

                            orders.slice(0, finalCount).forEach((o, i) => {
                                // Em đã thêm icon và xuống dòng cho sếp dễ nhìn trên màn hình MSI
                                listMsg += `
                                    <div class="order-item mb-2 p-2 border-bottom">
                                        <b>${i+1}. 📦 Đơn #${o.id}</b><br>
                                        👤 Khách: ${o.receiver_name || 'Chưa có tên'}<br>
                                        📞 SĐT: <span class="text-primary">${o.phone || o.receiver_phone || 'Trống'}</span><br>
                                        📍 Địa chỉ: ${o.address || 'Đang cập nhật...'}<br>
                                        📏 Khoảng cách: <b>${o.distanceKm || '0'} km</b>
                                    </div>`;
                            });

                            appendMsg('ai', listMsg + "<br>Sếp check kỹ nhé, cần <b>Tối ưu</b> đơn nào sếp hú em!");
                        }


        // B. Kịch bản Tối ưu (Linh hoạt số lượng theo ý sếp)
        else if (rawMsg.includes("tối ưu") || rawMsg.includes("chốt")) {
            let num = count; // Lấy đúng con số sếp nhắn
            if (rawMsg.includes("hết") || rawMsg.includes("tất cả")) num = orders.length;

            if (!num) {
                appendMsg('ai', "Sếp ơi, sếp muốn tối ưu bao nhiêu đơn cụ thể? Hay làm <b>hết luôn</b> cho máu?");
                return;
            }

            appendMsg('ai', `🚀 <b>Rõ!</b> Em đang gửi đúng <b>${num} đơn</b> sang n8n. Sếp ngồi vắt vẻo trên ghế MSI đợi em tí nhé!`);
            await aiThinking(2);

            try {
                const response = await fetch('https://c78448b64e7062.lhr.life/webhook/toi-uu-logistics', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ orders: orders.slice(0, num) })
                });
                const result = await response.json();
                const aiData = result[0].json;

                if(window.renderChatbotData) window.renderChatbotData(aiData);

                let weatherS = aiData.isRaining
                    ? `<div class="alert alert-info mt-2">🌧️ Đà Nẵng đang mưa, em đã tự cộng phụ phí cho sếp!</div>`
                    : `<div class="alert alert-success mt-2">☀️ Trời đẹp, shipper VKU có thể tăng tốc!</div>`;
                appendMsg('ai', `${aiData.chatbot_message}${weatherS}`);
            } catch (e) {
                appendMsg('ai', "❌ Lỗi rồi! Ngrok máy MSI bị ngắt, sếp check lại giúp em!");
            }
        }

        // C. Kịch bản nịnh sếp & cô Cẩm
        else if (rawMsg.includes("tác giả") || rawMsg.includes("thái") || rawMsg.includes("lê")) {
            appendMsg('ai', "Dạ, em là trợ lý của sếp <b>Thai (Le)</b>, sinh viên ưu tú của VKU ạ!");
        }
        else if (rawMsg.includes("cô cẩm")) {
            appendMsg('ai', "Chào cô ạ! Em đang được sếp Thai huấn luyện để trở thành AI Logistics giỏi nhất VKU!");
        }

        // D. Xử lý khi không hiểu (Hỏi ngược lại sếp)
        else {
            const randomQuestion = secretarialQuestions[Math.floor(Math.random() * secretarialQuestions.length)];
            appendMsg('ai', randomQuestion);
        }
    };

    chatInput.addEventListener("keypress", (e) => { if (e.key === "Enter") processUserChat(); });
});
