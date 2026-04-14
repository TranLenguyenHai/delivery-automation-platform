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

    private final String n8nWebhookUrl = "http://localhost:5678/webhook/e99d1f26-3a52-49d9-93e5-ed402977fcb6";

    private final String n8nReportWebhookUrl = "http://localhost:5678/webhook/9102f4a1-7868-44a3-b33c-78114c981656";

    private final String n8nAIWebhookUrl = "http://localhost:5678/webhook-test/ai-logistic-trigger";

    @PostMapping("/create")
    public ResponseEntity<?> createOrder(@RequestBody OrderRequest request) {
        System.out.println("=====================================");
        System.out.println("🎉 NHẬN YÊU CẦU ĐẶT HÀNG TỪ: " + request.getSenderName());

        try {
            // TỰ ĐỘNG TÌM KIẾM ĐỂ GHÉP ĐƠN
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

            // GỌI SANG N8N ĐỂ TÍNH GIÁ SHIP MỚI
            RestTemplate restTemplate = new RestTemplate();
            Map<String, Object> payload = new HashMap<>();
            payload.put("orderId", savedOrder.getId());
            payload.put("weight", savedOrder.getWeight());
            payload.put("distance", savedOrder.getDistanceKm());
            payload.put("note", savedOrder.getNote());

            System.out.println("🚀 Đang gửi tổng khối lượng " + savedOrder.getWeight() + "g sang n8n để tính phí...");
            ResponseEntity<Map> n8nResponse = restTemplate.postForEntity(n8nWebhookUrl, payload, Map.class);
            Map<String, Object> resultBody = n8nResponse.getBody();

            // KIỂM TRA  DỮ LIỆU TỪ N8N
            if (resultBody != null && resultBody.containsKey("shipper") && resultBody.containsKey("fee")) {
                savedOrder.setShipper(resultBody.get("shipper").toString());
                String feeStr = resultBody.get("fee").toString().replaceAll("[^\\d]", "");
                savedOrder.setShippingFee(Integer.valueOf(feeStr));
                savedOrder.setStatus("CALCULATED_SUCCESS");

                orderService.createOrder(savedOrder);
                System.out.println("✅ Đã cập nhật giá từ n8n vào DB!");

                // BẮN DATA SANG N8N ĐỂ LƯU GOOGLE SHEET
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

                // RENDER CÂU THÔNG BÁO
                String successMsg;
                if (isConsolidated) {
                    successMsg = String.format("✨ ĐÃ TỰ ĐỘNG GHÉP ĐƠN TIẾT KIỆM PHÍ!\nĐơn hàng được gộp vào ID: %d\nHàng hóa: %s\nTổng khối lượng: %d gram\nĐơn vị giao tối ưu: %s\nTổng phí ship: %,d VNĐ",
                            savedOrder.getId(), savedOrder.getProductName(), savedOrder.getWeight(), savedOrder.getShipper(), savedOrder.getShippingFee());
                } else {
                    successMsg = String.format("Thành công! ID Đơn: %d\nĐơn vị giao rẻ nhất: %s\nPhí ship: %,d VNĐ",
                            savedOrder.getId(), savedOrder.getShipper(), savedOrder.getShippingFee());
                }

                // [THÊM MỚI] GỌI AI ĐỂ PHÂN TÍCH
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

                // ĐÓNG GÓI TRẢ VỀ CHO WEB
                Map<String, Object> responseData = new HashMap<>();
                responseData.put("summary", successMsg);
                responseData.put("ai_message", thongTinHoaDonAI);

                return ResponseEntity.ok(responseData);

            } else {
                return ResponseEntity.badRequest().body("Lỗi: n8n không trả về 'shipper' hoặc 'fee'.");
            }

        } catch (Exception e) {
            System.err.println("❌ Lỗi hệ thống: " + e.getMessage());
            e.printStackTrace();
            return ResponseEntity.internalServerError().body("Lỗi Server: " + e.getMessage());
        }
    }

    // API NHẬN KẾT QUẢ TỪ N8N (AI VÀ THỜI TIẾT)
    @PutMapping("/{id}/ai-update")
    public ResponseEntity<?> updateOrderFromAi(
            @PathVariable Integer id,
            @RequestBody com.vku.delivery.dto.AiUpdateOrderRequest request) {

        System.out.println("🤖 N8N đang gọi API cập nhật AI cho đơn ID: " + id);

        try {
            Order order = orderService.getOrderById(id)
                    .orElseThrow(() -> new RuntimeException("Không tìm thấy đơn hàng ID: " + id));

            // 1. Cập nhật phụ phí (nếu có)
            if (request.getAdditionalFee() != null && request.getAdditionalFee() > 0) {
                Integer currentFee = order.getShippingFee() != null ? order.getShippingFee() : 0;
                order.setShippingFee(currentFee + request.getAdditionalFee());
                System.out.println("💰 Đã cộng thêm phụ phí: " + request.getAdditionalFee());
            }

            // 2. Cập nhật trạng thái
            if (request.getStatus() != null && !request.getStatus().isEmpty()) {
                order.setStatus(request.getStatus());
            }

            // 3. Nối thêm ghi chú (CONCAT)
            if (request.getNoteAppend() != null && !request.getNoteAppend().isEmpty()) {
                String currentNote = order.getNote() != null ? order.getNote() : "";
                order.setNote(currentNote + request.getNoteAppend());
            }

            // Lưu lại vào DB
            orderService.createOrder(order); // Dùng lại hàm createOrder của ông (bản chất save của JPA là tạo mới hoặc cập nhật)

            System.out.println("✅ Cập nhật AI thành công!");
            return ResponseEntity.ok("Đã cập nhật từ AI");

        } catch (Exception e) {
            System.err.println("❌ Lỗi khi n8n cập nhật AI: " + e.getMessage());
            return ResponseEntity.badRequest().body("Lỗi: " + e.getMessage());
        }
    }

    // API LẤY ĐƠN HÀNG MỚI NHẤT CHO N8N
    @GetMapping("/latest")
    public ResponseEntity<?> getLatestOrder() {
        try {
            Order order = orderService.getLatestOrder();
            if (order == null) {
                return ResponseEntity.notFound().build();
            }
            // Trả về dưới dạng List (mảng) để n8n đọc cấu trúc y hệt như lệnh SQL cũ
            return ResponseEntity.ok(java.util.Collections.singletonList(order));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Lỗi: Không lấy được đơn hàng mới nhất");
        }
    }
    // API LẤY DANH SÁCH ĐƠN HÀNG THEO TRẠNG THÁI
    @GetMapping("/status/{status}")
    public ResponseEntity<?> getOrdersByStatus(@PathVariable String status) {
        try {
            java.util.List<Order> orders = orderService.getOrdersByStatus(status);
            return ResponseEntity.ok(orders);
        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Lỗi: Không lấy được danh sách đơn hàng");
        }
    }
    // API CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG (Thay thế lệnh UPDATE)
    @PutMapping("/{id}/status")
    public ResponseEntity<?> updateOrderStatus(@PathVariable Integer id, @RequestBody Map<String, String> request) {
        try {
            Optional<Order> orderOpt = orderService.getOrderById(id);
            if (!orderOpt.isPresent()) {
                return ResponseEntity.badRequest().body("Không tìm thấy đơn hàng ID: " + id);
            }

            Order order = orderOpt.get();
            if (request.containsKey("status")) {
                order.setStatus(request.get("status"));
                orderService.createOrder(order); // Hàm này của ông bản chất là .save() nên dùng để update luôn
            }

            return ResponseEntity.ok("Cập nhật trạng thái thành công!");
        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Lỗi cập nhật trạng thái: " + e.getMessage());
        }
    }
    // API CẬP NHẬT THÔNG TIN GIAO HÀNG (Thay cho query1)
    @PutMapping("/{id}/assign-delivery")
    public ResponseEntity<?> assignDeliveryInfo(@PathVariable Integer id, @RequestBody java.util.Map<String, Object> request) {
        try {
            Optional<Order> orderOpt = orderService.getOrderById(id);
            if (!orderOpt.isPresent()) {
                return ResponseEntity.badRequest().body("Không tìm thấy đơn hàng ID: " + id);
            }
            Order order = orderOpt.get();

            // Cập nhật trạng thái
            if (request.containsKey("status")) {
                order.setStatus(request.get("status").toString());
            }
            // Cập nhật đơn vị vận chuyển
            if (request.containsKey("shipper")) {
                order.setShipper(request.get("shipper").toString());
            }
            // Cập nhật phí ship
            if (request.containsKey("shippingFee")) {
                order.setShippingFee(Integer.valueOf(request.get("shippingFee").toString()));
            }

            orderService.createOrder(order); // Lưu lại vào DB
            return ResponseEntity.ok("Cập nhật thông tin giao hàng thành công!");
        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Lỗi: " + e.getMessage());
        }
    }

    // API LẤY THỐNG KÊ (Thay cho query3)
    @GetMapping("/stats")
    public ResponseEntity<?> getStatistics() {
        try {
            return ResponseEntity.ok(orderService.getOrderStats());
        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Lỗi lấy thống kê");
        }
    }
}