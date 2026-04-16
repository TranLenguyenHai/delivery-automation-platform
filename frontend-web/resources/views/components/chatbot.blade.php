<div id="ai-chatbot-container" class="chatbot-hidden">
    <div class="chatbot-header">
        <span>🤖 Trợ Lý Logistics VKU</span>
        <button id="close-chatbot"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="chatbot-messages" id="chatbot-messages">
        </div>

    <div class="p-2 border-top bg-dark">
        <div class="input-group">
            <input type="text" id="chat-input" class="form-control form-control-sm" placeholder="Nhắn: 'Tối ưu 7 đơn'...">
            <button class="btn btn-primary btn-sm" onclick="processUserChat()"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>

    <div id="chatbot-actions" class="hidden">
        <div id="mini-map-container">
            <div id="shipper-dot" class="shipper-dot"></div>
        </div>
        <button id="btn-xem-lo-trinh">Xem Lộ Trình Giao Hàng</button>
    </div>
</div>

<button id="btn-open-chatbot" class="chatbot-fab shadow-lg">💬</button>
