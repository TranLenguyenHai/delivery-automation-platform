package com.vku.delivery.repository;

import com.vku.delivery.entity.Order;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface OrderRepository extends JpaRepository<Order, Integer> {
    // Chỉ cần để trống thế này thôi, JpaRepository đã bao thầu sẵn
    // mười mấy hàm thêm, sửa, xóa, tìm kiếm cơ bản rồi!
}