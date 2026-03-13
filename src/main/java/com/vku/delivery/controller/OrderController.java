package com.vku.delivery.controller;

import com.vku.delivery.dto.OrderRequest;
import com.vku.delivery.entity.Order;
import com.vku.delivery.service.OrderService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.client.RestTemplate;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.HashMap;
import java.util.Map;
import java.util.Optional;

@RestController
@RequestMapping("/api/orders")
@CrossOrigin(origins = "*")
public class OrderController {

    @Autowired
    private OrderService orderService;

    // Webhook 1: Dùng để tính giá ship (Cái cũ cậu đang xài)
    private final String n8nWebhookUrl = "http://localhost:5678/webhook/e99d1f26-3a52-49d9-93e5-ed402977fcb6";

    // Webhook 2: Dùng để bắn data sang Google Sheet (CÁI MỚI)
    private final String n8nReportWebhookUrl = "http://localhost:5678/webhook/9102f4a1-7868-44a3-b33c-78114c981656";

    // Webhook 3: Workflow AI
    private final String n8nAIWebhookUrl = "http://localhost:5678/webhook-test/ai-logistic-trigger";

    @PostMapping("/create")
    public ResponseEntity<?> createOrder(@RequestBody OrderRequest request) {
        System.out.println("=====================================");
        System.out.println("🎉 NHẬN YÊU CẦU ĐẶT HÀNG TỪ: " + request.getSenderName());

        try {
            // --- BƯỚC 1: TỰ ĐỘNG TÌM KIẾM ĐỂ GHÉP ĐƠN ---
            Optional<Order> existingOrderOpt = orderService.findOrderToConsolidate(
                    request.getSenderPhone(),
                    request.getReceiverPhone(),
                    request.getReceiverAddress()
            );

            Order order;
            boolean isConsolidated = false;

            if (existingOrderOpt.isPresent()) {
                order = existingOrderOpt.get();
                System.out.println("🔄 PHÁT HIỆN ĐƠN CŨ! Tiến hành gộp đơn ID: " + order.getId());
                order.setWeight(order.getWeight() + request.getWeight());
                order.setProductName(order.getProductName() + " + " + request.getProductName());
                isConsolidated = true;
            } else {
                System.out.println("🆕 Không có đơn trùng, tạo đơn mới!");
                order = new Order();
                order.setSenderName(request.getSenderName());
                order.setSenderPhone(request.getSenderPhone());
                order.setSenderAddress(request.getSenderAddress());
                order.setReceiverName(request.getReceiverName());
                order.setReceiverPhone(request.getReceiverPhone());
                order.setReceiverAddress(request.getReceiverAddress());
                order.setProductName(request.getProductName());
                order.setWeight(request.getWeight());
                order.setDistanceKm(request.getDistance());
                order.setNote(request.getNote());
            }

            order.setStatus("PENDING_CALCULATION");
            Order savedOrder = orderService.createOrder(order);
            System.out.println("✅ Đã lưu đơn hàng vào DB với ID: " + savedOrder.getId());

            // --- BƯỚC 2: GỌI SANG N8N ĐỂ TÍNH GIÁ SHIP MỚI ---
            RestTemplate restTemplate = new RestTemplate();
            Map<String, Object> payload = new HashMap<>();
            payload.put("orderId", savedOrder.getId());
            payload.put("weight", savedOrder.getWeight());
            payload.put("distance", savedOrder.getDistanceKm());
            payload.put("note", savedOrder.getNote()); // Thêm note để dùng chung cho cả AI

            System.out.println("🚀 Đang gửi tổng khối lượng " + savedOrder.getWeight() + "g sang n8n để tính phí...");
            ResponseEntity<Map> n8nResponse = restTemplate.postForEntity(n8nWebhookUrl, payload, Map.class);
            Map<String, Object> resultBody = n8nResponse.getBody();

            // --- BƯỚC 3: KIỂM TRA CHẶT CHẼ DỮ LIỆU TỪ N8N (Khôi phục logic cũ) ---
            if (resultBody != null && resultBody.containsKey("shipper") && resultBody.containsKey("fee")) {
                savedOrder.setShipper(resultBody.get("shipper").toString());
                String feeStr = resultBody.get("fee").toString().replaceAll("[^\\d]", "");
                savedOrder.setShippingFee(Integer.valueOf(feeStr));
                savedOrder.setStatus("CALCULATED_SUCCESS");

                orderService.createOrder(savedOrder);
                System.out.println("✅ Đã cập nhật giá từ n8n vào DB!");

                // --- BƯỚC 4: BẮN DATA SANG N8N ĐỂ LƯU GOOGLE SHEET ---
                try {
                    Map<String, Object> reportData = new HashMap<>();
                    reportData.put("id_don", "DH" + savedOrder.getId());
                    reportData.put("thoi_gian", LocalDateTime.now().format(DateTimeFormatter.ofPattern("dd/MM/yyyy HH:mm:ss")));
                    reportData.put("ten_hang", savedOrder.getProductName());
                    reportData.put("khoi_luong", savedOrder.getWeight());
                    reportData.put("don_vi_giao", savedOrder.getShipper());
                    reportData.put("phi_ship", savedOrder.getShippingFee());

                    restTemplate.postForEntity(n8nReportWebhookUrl, reportData, String.class);
                    System.out.println("📊 Đã gửi báo cáo sang Google Sheet thành công!");
                } catch (Exception e) {
                    System.err.println("⚠️ Lỗi gửi báo cáo Google Sheet: " + e.getMessage());
                }

                // --- BƯỚC 5: RENDER CÂU THÔNG BÁO (Khôi phục câu đầy đủ cũ) ---
                String successMsg;
                if (isConsolidated) {
                    successMsg = String.format("✨ ĐÃ TỰ ĐỘNG GHÉP ĐƠN TIẾT KIỆM PHÍ!\nĐơn hàng được gộp vào ID: %d\nHàng hóa: %s\nTổng khối lượng: %d gram\nĐơn vị giao tối ưu: %s\nTổng phí ship: %,d VNĐ",
                            savedOrder.getId(), savedOrder.getProductName(), savedOrder.getWeight(), savedOrder.getShipper(), savedOrder.getShippingFee());
                } else {
                    successMsg = String.format("Thành công! ID Đơn: %d\nĐơn vị giao rẻ nhất: %s\nPhí ship: %,d VNĐ",
                            savedOrder.getId(), savedOrder.getShipper(), savedOrder.getShippingFee());
                }

                // --- BƯỚC 6: [THÊM MỚI] GỌI AI ĐỂ PHÂN TÍCH ---
                Object thongTinHoaDonAI = "Hệ thống AI đã ghi nhận đơn hàng.";
                try {
                    System.out.println("🤖 Đang nhờ AI phân tích phụ phí...");
                    ResponseEntity<Map> aiResponse = restTemplate.postForEntity(n8nAIWebhookUrl, payload, Map.class);
                    if (aiResponse.getBody() != null) {
                        thongTinHoaDonAI = aiResponse.getBody();
                        System.out.println("✅ AI phân tích xong!");
                    }
                } catch (Exception e) {
                    System.err.println("⚠️ AI chưa phản hồi: " + e.getMessage());
                }

                // --- BƯỚC 7: ĐÓNG GÓI TRẢ VỀ CHO WEB ---
                Map<String, Object> responseData = new HashMap<>();
                responseData.put("summary", successMsg);
                responseData.put("ai_message", thongTinHoaDonAI);

                return ResponseEntity.ok(responseData);

            } else {
                // Nếu n8n cũ lỗi, chặn đứng luôn tại đây (Logic cũ)
                return ResponseEntity.badRequest().body("Lỗi: n8n không trả về 'shipper' hoặc 'fee'.");
            }

        } catch (Exception e) {
            System.err.println("❌ Lỗi hệ thống: " + e.getMessage());
            e.printStackTrace();
            return ResponseEntity.internalServerError().body("Lỗi Server: " + e.getMessage());
        }
    }
}