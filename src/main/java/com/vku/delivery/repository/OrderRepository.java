package com.vku.delivery.repository;

import com.vku.delivery.entity.Order;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;
import java.util.Optional;

@Repository
public interface OrderRepository extends JpaRepository<Order, Integer> {

    // LẤY ĐƠN HÀNG MỚI NHẤT (THAY CHO LỆNH SQL: ORDER BY id DESC LIMIT 1)
    Order findFirstByOrderByIdDesc();

    // TÍNH NĂNG GHÉP ĐƠN: Tìm đơn cũ chưa giao của cùng người gửi, người nhận và địa chỉ
    Optional<Order> findFirstBySenderPhoneAndReceiverPhoneAndReceiverAddressAndStatusOrderByIdDesc(
            String senderPhone, String receiverPhone, String receiverAddress, String status);
    // Lấy danh sách đơn hàng theo trạng thái
    java.util.List<Order> findByStatus(String status);
    @Query("SELECT COUNT(o.id) FROM Order o")
    long countTotalOrders();
    @Query("SELECT COALESCE(SUM(o.shippingFee), 0) FROM Order o")
    long sumTotalShippingFee();
}