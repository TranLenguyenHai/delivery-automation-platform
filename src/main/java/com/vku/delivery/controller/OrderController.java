package com.vku.delivery.controller;

import com.vku.delivery.dto.OrderRequest;
import com.vku.delivery.entity.Order;
import com.vku.delivery.service.OrderService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.client.RestTemplate;

@RestController
@RequestMapping("/api/orders")
public class OrderController {

    @Autowired
    private OrderService orderService; // Kêu gọi Anh Quản Lý ra làm việc

    @PostMapping("/create")
    public String createOrder(@RequestBody OrderRequest request) {
        System.out.println("=====================================");
        System.out.println("🎉 ĐÃ NHẬN ĐƯỢC ĐƠN HÀNG MỚI TỪ WEB!");
        System.out.println("Khách hàng: " + request.getCustomerName());
        System.out.println("SĐT: " + request.getCustomerPhone());
        System.out.println("Cân nặng: " + request.getPackageWeight() + " gram");
        System.out.println("=====================================");

        // --- BƯỚC 1: LƯU VÀO DATABASE TRƯỚC ---
        Order newOrder = new Order();
        newOrder.setCustomerName(request.getCustomerName());
        newOrder.setCustomerPhone(request.getCustomerPhone());
        newOrder.setAddress(request.getDeliveryAddress()); // Phiên dịch tên biến cho khớp
        newOrder.setPackageWeight(request.getPackageWeight());

        // Gọi Anh Quản Lý cất vào kho (Nó sẽ tự gán trạng thái PENDING)
        orderService.createOrder(newOrder);
        System.out.println("💾 Đã lưu đơn hàng thành công vào Database MySQL!");

        // --- BƯỚC 2: BẮN WEBHOOK CHO N8N ---
        try {
            RestTemplate restTemplate = new RestTemplate();
            // Link Webhook n8n của bạn
            String n8nWebhookUrl = "http://localhost:5678/webhook-test/2322cc2e-b5ee-4be9-a79a-dfd29a0a3b93";

            restTemplate.postForEntity(n8nWebhookUrl, request, String.class);

            System.out.println("✅ ĐÃ BẮN TÍN HIỆU THÀNH CÔNG SANG N8N!");
            return "Tuyệt vời! Đơn hàng đã được lưu DB và bắn thành công sang n8n!";
        } catch (Exception e) {
            System.out.println("❌ Lỗi khi bắn sang n8n: " + e.getMessage());
            return "Cảnh báo: Đã lưu Database thành công, nhưng n8n đang tắt hoặc lỗi mạng.";
        }
    }
}