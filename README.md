# delivery-automation-platform
# Delivery Cost Optimization Automation Platform 

**Môn học:** Chuyên đề 2 (IT) - Low code, No code & Automation
## Giới thiệu dự án (Project Overview)
Dự án này xây dựng một nền tảng tự động hóa nhằm tối ưu hóa chi phí vận chuyển (Delivery Cost Optimization). Hệ thống tiếp nhận thông tin đơn hàng, tự động truy vấn giá cước từ nhiều đơn vị vận chuyển khác nhau (GHN, GHTK, Viettel Post,...), so sánh và lựa chọn đối tác có chi phí thấp nhất để tiến hành giao hàng.

Điểm nổi bật của dự án là việc ứng dụng tư duy **Low-code/No-code** kết hợp với triển khai thực tế trên môi trường máy chủ **Linux**, bám sát yêu cầu vận hành của doanh nghiệp hiện đại.

## 🛠 Công nghệ sử dụng (Tech Stack)
* **Core Automation Engine:** [n8n](https://n8n.io/) (Nền tảng Workflow Automation mã nguồn mở).
* **Environment:** Hệ điều hành Linux (Ubuntu).
* **Deployment:** Docker & Containerization.
* **IDE: ** Intelij.
* **DATABASE: ** MySQL And DOCKER.
## 🗺 Lộ trình thực hiện (Implementation Roadmap)
Để hoàn thiện hệ thống, dự án được chia thành các giai đoạn cụ thể:

* **[x] Giai đoạn 1: Chuẩn bị Môi trường (Environment Setup)**
  * Thiết lập môi trường Linux (Ubuntu).
  * Cài đặt Docker engine để quản lý container.
* **[ ] Giai đoạn 2: Triển khai n8n (n8n Deployment)**
  * Pull image n8n từ Docker Hub.
  * Cấu hình port và volume để lưu trữ dữ liệu (persistent data) trên Linux.
* **[ ] Giai đoạn 3: Thiết kế Workflow (Workflow Design)**
  * Xây dựng Webhook tiếp nhận dữ liệu đơn hàng đầu vào (JSON).
  * Tích hợp HTTP Request gọi Mock API báo giá của 3 đơn vị vận chuyển.
  * Viết logic (Code Node) để so sánh và lọc ra mức giá rẻ nhất.
* **[ ] Giai đoạn 4: Hoàn thiện & Báo cáo (Testing & Delivery)**
  * Thiết lập node đầu ra (gửi thông báo Telegram/Slack hoặc ghi vào Google Sheets).
  * Viết tài liệu báo cáo và chuẩn bị kịch bản demo trực tiếp luồng chạy.

## Hướng dẫn khởi chạy (How to Run)
Dự án được đóng gói để chạy trực tiếp trên môi trường Linux có cài đặt sẵn Docker. 

**Chạy lệnh sau tại Terminal để khởi động hệ thống n8n:**
```bash
sudo docker run -it --rm --name n8n -p 5678:5678 -v n8n_data:/home/node/.n8n docker.n8n.io/n8nio/n8n
