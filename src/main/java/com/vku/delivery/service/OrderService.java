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

    // Hàm nhận đơn hàng mới và lưu vào kho (Đã bỏ ghim cứng trạng thái để cập nhật được DB)
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
}