package com.vku.delivery.controller;

import com.vku.delivery.dto.OrderRequest;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.client.RestTemplate; // Công cụ dùng để bắn API

@RestController
@RequestMapping("/api/orders")
public class OrderController {

    @PostMapping("/create")
    public String createOrder(@RequestBody OrderRequest request) {
        System.out.println("=====================================");
        System.out.println("🎉 ĐÃ NHẬN ĐƯỢC ĐƠN HÀNG MỚI TỪ WEB!");
        System.out.println("Khách hàng: " + request.getCustomerName());
        System.out.println("=====================================");

        // --- ĐOẠN CODE MỚI: BẮN DỮ LIỆU SANG N8N ---
        try {
            RestTemplate restTemplate = new RestTemplate();
            // Đây chính là link Webhook n8n của bạn
            String n8nWebhookUrl = "http://localhost:5678/webhook-test/2322cc2e-b5ee-4be9-a79a-dfd29a0a3b93";

            // Lệnh khai hỏa: Gói toàn bộ 'request' ném sang URL kia
            restTemplate.postForEntity(n8nWebhookUrl, request, String.class);

            System.out.println("✅ ĐÃ BẮN THÀNH CÔNG SANG N8N!");
            return "Tuyệt vời! Đơn hàng đã được Backend chuyển thành công sang hệ thống n8n!";
        } catch (Exception e) {
            System.out.println("❌ Lỗi khi bắn sang n8n: " + e.getMessage());
            return "Lỗi: Backend đã nhận, nhưng n8n đang tắt hoặc lỗi mạng.";
        }
    }
}