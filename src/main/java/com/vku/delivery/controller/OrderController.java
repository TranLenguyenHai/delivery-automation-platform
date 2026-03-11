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

@RestController
@RequestMapping("/api/orders")
@CrossOrigin(origins = "*") // Cho phép giao diện Web gọi API không bị lỗi CORS
public class OrderController {

    @Autowired
    private OrderService orderService;

    // Dán link TEST URL từ n8n vào đây (có chữ /webhook-test/)
    private final String n8nWebhookUrl = "http://localhost:5678/webhook/e99d1f26-3a52-49d9-93e5-ed402977fcb6";

    @PostMapping("/create")
    public ResponseEntity<?> createOrder(@RequestBody OrderRequest request) {
        System.out.println("=====================================");
        System.out.println("🎉 NHẬN ĐƠN HÀNG MỚI TỪ: " + request.getSenderName());

        try {
            // --- BƯỚC 1: LƯU VÀO DATABASE (TRẠNG THÁI PENDING) ---
            Order order = new Order();
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
            order.setStatus("PENDING");

            Order savedOrder = orderService.createOrder(order);
            System.out.println("✅ Đã lưu đơn hàng vào DB với ID: " + savedOrder.getId());

            // --- BƯỚC 2: GỬI SANG N8N ĐỂ TÍNH PHÍ VẬN CHUYỂN ---
            RestTemplate restTemplate = new RestTemplate();
            Map<String, Object> payload = new HashMap<>();
            payload.put("orderId", savedOrder.getId());
            payload.put("weight", request.getWeight());
            payload.put("distance", request.getDistance());

            System.out.println("🚀 Đang gửi dữ liệu sang n8n: " + payload);

            ResponseEntity<Map> n8nResponse = restTemplate.postForEntity(n8nWebhookUrl, payload, Map.class);
            Map<String, Object> resultBody = n8nResponse.getBody();

            // --- BƯỚC 3: CẬP NHẬT LẠI DATABASE TỪ KẾT QUẢ N8N ---
            if (resultBody != null && resultBody.containsKey("shipper") && resultBody.containsKey("fee")) {
                savedOrder.setShipper(resultBody.get("shipper").toString());

                // Ép kiểu phí ship về Integer
                String feeStr = resultBody.get("fee").toString().replaceAll("[^\\d]", "");
                savedOrder.setShippingFee(Integer.valueOf(feeStr));

                savedOrder.setStatus("CALCULATED_SUCCESS");

                // Lưu đè để cập nhật thông tin shipper và phí
                orderService.createOrder(savedOrder);
                System.out.println("✅ Đã cập nhật giá từ n8n vào DB!");

                // --- BƯỚC 4: TRẢ KẾT QUẢ CHO WEB ---
                String successMsg = String.format("Thành công! ID Đơn: %d\nĐơn vị: %s\nPhí ship: %,d VNĐ",
                        savedOrder.getId(), savedOrder.getShipper(), savedOrder.getShippingFee());
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