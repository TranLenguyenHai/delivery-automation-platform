package com.vku.delivery.service;

import com.vku.delivery.entity.Order;
import com.vku.delivery.repository.OrderRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

@Service
public class OrderService {

    @Autowired
    private OrderRepository orderRepository;

    // Hàm nhận đơn hàng mới và lưu vào kho
    public Order createOrder(Order order) {
        // Đảm bảo đơn hàng mới luôn ở trạng thái PENDING để n8n còn quét
        order.setStatus("PENDING");

        // Gọi băng chuyền (Repository) để cất vào Database
        return orderRepository.save(order);
    }
}