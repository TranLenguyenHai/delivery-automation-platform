package com.vku.delivery.dto;

public class AiUpdateOrderRequest {
    private Integer additionalFee; // Phụ phí (dùng Integer vì code cũ của ông fee đang là Integer)
    private String status;
    private String noteAppend;

    // Getters and Setters
    public Integer getAdditionalFee() { return additionalFee; }
    public void setAdditionalFee(Integer additionalFee) { this.additionalFee = additionalFee; }

    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }

    public String getNoteAppend() { return noteAppend; }
    public void setNoteAppend(String noteAppend) { this.noteAppend = noteAppend; }
}