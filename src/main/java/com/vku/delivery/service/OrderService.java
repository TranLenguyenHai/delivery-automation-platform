package com.vku.delivery.service;

import com.vku.delivery.entity.Order;
import com.vku.delivery.repository.OrderRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.util.Optional;

@Service
public class OrderService {

    @Autowired
    private OrderRepository orderRepository;

    public Order createOrder(Order order) {
        if (order.getStatus() == null) {
            order.setStatus("PENDING");
        }
        return orderRepository.save(order);
    }

    // TÍNH NĂNG GHÉP ĐƠN: Tìm đơn cũ để ghép
    public Optional<Order> findOrderToConsolidate(String senderPhone, String receiverPhone, String receiverAddress) {
        // Chỉ tìm những đơn đã tính phí xong nhưng shipper chưa lấy (CALCULATED_SUCCESS)
        return orderRepository.findFirstBySenderPhoneAndReceiverPhoneAndReceiverAddressAndStatusOrderByIdDesc(
                senderPhone, receiverPhone, receiverAddress, "CALCULATED_SUCCESS"
        );
    }

    // Lấy đơn hàng theo ID để AI cập nhật
    public Optional<Order> getOrderById(Integer id) {
        return orderRepository.findById(id);
    }

    // Lấy đơn hàng mới nhất thay cho lệnh SQL LIMIT 1
    public Order getLatestOrder() {
        return orderRepository.findFirstByOrderByIdDesc();
    }
    // Lấy danh sách đơn hàng theo trạng thái
    public java.util.List<Order> getOrdersByStatus(String status) {
        return orderRepository.findByStatus(status);
    }
    public java.util.Map<String, Long> getOrderStats() {
        java.util.Map<String, Long> stats = new java.util.HashMap<>();
        stats.put("tong_don", orderRepository.countTotalOrders());
        stats.put("tong_tien", orderRepository.sumTotalShippingFee());
        return stats;
    }
}