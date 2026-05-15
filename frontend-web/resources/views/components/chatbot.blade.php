<div id="ai-chatbot-container" class="chatbot-hidden">
    <div class="chatbot-header">
        <div class="d-flex align-items-center gap-2">
            <span class="pulse-icon" style="width:8px; height:8px; background:#10b981; border-radius:50%; display:inline-block;"></span>
            <span>🤖 Trợ Lý Logistics VKU</span>
        </div>
        <button id="close-chatbot" class="close-btn" title="Đóng">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <div class="chatbot-messages" id="chatbot-messages">
        <!-- Tin nhắn khôi phục từ sessionStorage sẽ hiện ở đây -->
    </div>

    <div class="p-3 border-top bg-slate-900">
        <div class="input-group">
            <input type="text" id="chat-input" class="form-control form-control-sm bg-slate-800 border-slate-700 text-white" placeholder="Nhắn: 'Tối ưu 7 đơn'...">
            <button class="btn btn-primary btn-sm px-3" onclick="processUserChat()"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>

    <div id="chatbot-actions" class="hidden">
        <div id="mini-map-container">
            <div id="shipper-dot" class="shipper-dot"></div>
        </div>
        <button id="btn-xem-lo-trinh">Xem Lộ Trình Giao Hàng</button>
    </div>
</div>

<button id="btn-open-chatbot" class="chatbot-fab shadow-lg">
    <span class="fab-icon">💬</span>
</button>
