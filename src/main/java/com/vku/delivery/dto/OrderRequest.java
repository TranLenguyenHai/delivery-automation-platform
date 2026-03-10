package com.vku.delivery.dto;

public class OrderRequest {
    private String customerName;
    private String customerPhone;
    private String deliveryAddress;
    private Integer packageWeight;
    private String note;
    private Double distanceKm;

    // Dưới đây là các hàm Get/Set để Spring Boot tự động đọc được dữ liệu
    public String getCustomerName() { return customerName; }
    public void setCustomerName(String customerName) { this.customerName = customerName; }

    public String getCustomerPhone() { return customerPhone; }
    public void setCustomerPhone(String customerPhone) { this.customerPhone = customerPhone; }

    public String getDeliveryAddress() { return deliveryAddress; }
    public void setDeliveryAddress(String deliveryAddress) { this.deliveryAddress = deliveryAddress; }

    public Integer getPackageWeight() { return packageWeight; }
    public void setPackageWeight(Integer packageWeight) { this.packageWeight = packageWeight; }

    public String getNote() { return note; }
    public void setNote(String note) { this.note = note; }
    public Double getDistanceKm() {
        return distanceKm;
    }
    public void setDistanceKm(Double distanceKm) {
        this.distanceKm = distanceKm;
    }
}