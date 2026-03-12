package com.vku.delivery.repository;

import com.vku.delivery.entity.Order;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.Optional;

@Repository
public interface OrderRepository extends JpaRepository<Order, Integer> {

    // TÍNH NĂNG GHÉP ĐƠN: Tìm đơn cũ chưa giao của cùng người gửi, người nhận và địa chỉ
    Optional<Order> findFirstBySenderPhoneAndReceiverPhoneAndReceiverAddressAndStatusOrderByIdDesc(
            String senderPhone, String receiverPhone, String receiverAddress, String status);
}