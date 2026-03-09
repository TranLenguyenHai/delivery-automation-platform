package com.vku.delivery.entity;

import jakarta.persistence.*;

@Entity
@Table(name = "orders")
public class Order {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Integer id;

    @Column(name = "customer_name")
    private String customerName;

    @Column(name = "customer_phone")
    private String customerPhone;

    private String address;

    @Column(name = "packageWeight")
    private Integer packageWeight;

    private String status = "PENDING";

    private String shipper;

    // Cột mới đẻ thêm để cho Hải lưu kết quả "giá rẻ nhất"
    @Column(name = "shipping_fee")
    private Integer shippingFee;

    // --- GETTER VÀ SETTER ---
    // (Nếu đồ án của bạn có cài thư viện Lombok thì chỉ cần thêm @Data ở trên cùng,
    // còn nếu không dùng Lombok thì bạn nhấn Alt + Insert để Generate tự động Getter/Setter ra nhé)

    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }

    public String getCustomerName() { return customerName; }
    public void setCustomerName(String customerName) { this.customerName = customerName; }

    public String getCustomerPhone() { return customerPhone; }
    public void setCustomerPhone(String customerPhone) { this.customerPhone = customerPhone; }

    public String getAddress() { return address; }
    public void setAddress(String address) { this.address = address; }

    public Integer getPackageWeight() { return packageWeight; }
    public void setPackageWeight(Integer packageWeight) { this.packageWeight = packageWeight; }

    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }

    public String getShipper() { return shipper; }
    public void setShipper(String shipper) { this.shipper = shipper; }

    public Integer getShippingFee() { return shippingFee; }
    public void setShippingFee(Integer shippingFee) { this.shippingFee = shippingFee; }
}