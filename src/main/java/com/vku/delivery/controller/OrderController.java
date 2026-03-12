package com.vku.delivery.controller;

import com.vku.delivery.dto.OrderRequest;
import com.vku.delivery.entity.Order;
import com.vku.delivery.service.OrderService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.client.RestTemplate;

import java.util.HashMap;
import java.util.Map;
import java.util.Optional;

@RestController
@RequestMapping("/api/orders")
@CrossOrigin(origins = "*") // Cho phép giao diện Web gọi API không bị lỗi CORS
public class OrderController {

    @Autowired
    private OrderService orderService;

    // Đã cấu hình chạy thẳng link Production URL của n8n
    private final String n8nWebhookUrl = "http://localhost:5678/webhook/e99d1f26-3a52-49d9-93e5-ed402977fcb6";

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
                // TÌM THẤY ĐƠN CŨ -> GỘP ĐƠN
                order = existingOrderOpt.get();
                System.out.println("🔄 PHÁT HIỆN ĐƠN CŨ! Tiến hành gộp đơn ID: " + order.getId());

                // Gộp khối lượng
                order.setWeight(order.getWeight() + request.getWeight());
                // Gộp tên sản phẩm
                order.setProductName(order.getProductName() + " + " + request.getProductName());

                isConsolidated = true;
            } else {
                // KHÔNG TÌM THẤY -> TẠO ĐƠN MỚI
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

            // Lưu trạng thái chờ tính toán để chốt ID lưu DB
            order.setStatus("PENDING_CALCULATION");
            Order savedOrder = orderService.createOrder(order);
            System.out.println("✅ Đã lưu đơn hàng vào DB với ID: " + savedOrder.getId());

            // --- BƯỚC 2: GỌI SANG N8N ĐỂ TÍNH GIÁ SHIP MỚI ---
            RestTemplate restTemplate = new RestTemplate();
            Map<String, Object> payload = new HashMap<>();
            payload.put("orderId", savedOrder.getId());
            payload.put("weight", savedOrder.getWeight());
            payload.put("distance", savedOrder.getDistanceKm());

            System.out.println("🚀 Đang gửi tổng khối lượng " + savedOrder.getWeight() + "g sang n8n...");
            ResponseEntity<Map> n8nResponse = restTemplate.postForEntity(n8nWebhookUrl, payload, Map.class);
            Map<String, Object> resultBody = n8nResponse.getBody();

            // --- BƯỚC 3: CẬP NHẬT KẾT QUẢ VÀ BÁO CÁO RA WEB ---
            if (resultBody != null && resultBody.containsKey("shipper") && resultBody.containsKey("fee")) {
                savedOrder.setShipper(resultBody.get("shipper").toString());

                // Ép kiểu phí ship về Integer cực kỳ thông minh
                String feeStr = resultBody.get("fee").toString().replaceAll("[^\\d]", "");
                savedOrder.setShippingFee(Integer.valueOf(feeStr));

                savedOrder.setStatus("CALCULATED_SUCCESS");

                // Lưu đè để cập nhật thông tin shipper và phí mới nhất
                orderService.createOrder(savedOrder);
                System.out.println("✅ Đã cập nhật giá từ n8n vào DB!");

                // --- BƯỚC 4: RENDER CÂU THÔNG BÁO ---
                String successMsg;
                if (isConsolidated) {
                    successMsg = String.format("✨ ĐÃ TỰ ĐỘNG GHÉP ĐƠN TIẾT KIỆM PHÍ!\nĐơn hàng được gộp vào ID: %d\nHàng hóa: %s\nTổng khối lượng: %d gram\nĐơn vị giao tối ưu: %s\nTổng phí ship: %,d VNĐ",
                            savedOrder.getId(), savedOrder.getProductName(), savedOrder.getWeight(), savedOrder.getShipper(), savedOrder.getShippingFee());
                } else {
                    successMsg = String.format("Thành công! ID Đơn: %d\nĐơn vị giao rẻ nhất: %s\nPhí ship: %,d VNĐ",
                            savedOrder.getId(), savedOrder.getShipper(), savedOrder.getShippingFee());
                }

                return ResponseEntity.ok(successMsg);
            } else {
                return ResponseEntity.badRequest().body("Lỗi: n8n không trả về 'shipper' hoặc 'fee'.");
            }

        } catch (Exception e) {
            System.err.println("❌ Lỗi hệ thống: " + e.getMessage());
            e.printStackTrace();
            return ResponseEntity.internalServerError().body("Lỗi Server: " + e.getMessage());
        }
    }
}