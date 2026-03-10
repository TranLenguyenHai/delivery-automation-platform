package com.vku.delivery.controller;

import com.vku.delivery.dto.OrderRequest;
import com.vku.delivery.entity.Order;
import com.vku.delivery.service.OrderService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.client.RestTemplate;
import java.util.HashMap;
import java.util.Map;

@RestController
@RequestMapping("/api/orders")
public class OrderController {

    @Autowired
    private OrderService orderService;

    @PostMapping("/create")
    public String createOrder(@RequestBody OrderRequest request) {
        System.out.println("=====================================");
        System.out.println("🎉 NHẬN ĐƠN HÀNG MỚI: " + request.getCustomerName());

        // --- BƯỚC 1: LƯU VÀO DATABASE TRƯỚC ---
        Order newOrder = new Order();
        newOrder.setCustomerName(request.getCustomerName());
        newOrder.setCustomerPhone(request.getCustomerPhone());
        newOrder.setAddress(request.getDeliveryAddress());
        newOrder.setPackageWeight(request.getPackageWeight());

        // Mày nhớ bảo thằng Thái thêm trường này vào OrderRequest/Entity nếu chưa có nhé
        // newOrder.setDistanceKm(request.getDistanceKm());

        // Lưu đơn hàng để lấy được cái ID tự tăng từ MySQL
        Order savedOrder = orderService.createOrder(newOrder);
        System.out.println("💾 Đã lưu DB, ID đơn hàng là: " + savedOrder.getId());

        // --- BƯỚC 2: BẮN WEBHOOK CHO N8N (LINK PRODUCTION) ---
        try {
            RestTemplate restTemplate = new RestTemplate();

            // ĐÃ ĐỔI SANG LINK PRODUCTION (Bỏ chữ -test)
            String n8nWebhookUrl = "http://localhost:5678/webhook/2322cc2e-b5ee-4be9-a79a-dfd29a0a3b93";

            // Tạo cục JSON chuẩn để n8n đọc được
            Map<String, Object> payload = new HashMap<>();
            payload.put("order_id", savedOrder.getId()); // Quan trọng để n8n biết đơn nào mà Update
            payload.put("packageWeight", savedOrder.getPackageWeight());
            payload.put("distanceKm", request.getDistanceKm()); // Giả sử request có trường này
            payload.put("customerName", savedOrder.getCustomerName());

            restTemplate.postForEntity(n8nWebhookUrl, payload, String.class);

            System.out.println("✅ ĐÃ GỬI SANG N8N ĐỂ TÍNH GIÁ TỰ ĐỘNG!");
            return "Đã nhận đơn ID: " + savedOrder.getId() + ". Đang tính toán giá cước...";

        } catch (Exception e) {
            System.err.println("❌ Lỗi gọi n8n: " + e.getMessage());
            return "Lưu DB ok nhưng lỗi gọi n8n: " + e.getMessage();
        }
    }
}