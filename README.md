# Delivery Cost Optimization Automation Platform 🚀

**Môn học:** Chuyên đề 2 (IT) - Low code, No code & Automation

## 📌 Giới thiệu dự án (Project Overview)
Dự án này xây dựng một nền tảng tự động hóa nhằm tối ưu hóa chi phí vận chuyển (Delivery Cost Optimization). Hệ thống tiếp nhận thông tin đơn hàng, tự động truy vấn giá cước từ nhiều đơn vị vận chuyển khác nhau (GHN, GHTK, Viettel Post,...), so sánh và lựa chọn đối tác có chi phí thấp nhất để tiến hành giao hàng.

Điểm nổi bật của dự án là việc ứng dụng **Kiến trúc Lai (Hybrid Architecture)**, kết hợp giữa phát triển phần mềm truyền thống cho Core Backend và tư duy **Low-code/No-code** cho quy trình tự động hóa. Hệ thống được triển khai thực tế trên môi trường máy chủ **Linux**, bám sát tiêu chuẩn vận hành Microservices của doanh nghiệp hiện đại.

## 🛠 Công nghệ sử dụng (Tech Stack)
* **Core Backend & UI:** Java Spring Boot (IDE: IntelliJ IDEA Community).
* **Core Automation Engine:** [n8n](https://n8n.io/) (Nền tảng Workflow Automation).
* **Database:** MySQL.
* **Environment & Deployment:** Hệ điều hành Linux (Ubuntu), quản lý bằng Docker & Containerization.

## 🗺 Lộ trình thực hiện (Implementation Roadmap)
Dự án được chia thành các giai đoạn phối hợp song song giữa Core Backend và Automation:

* **[x] Giai đoạn 1: Chuẩn bị Môi trường (Environment Setup)**
  * Cài đặt Docker engine trên môi trường Linux (Ubuntu).
  * Cài đặt IDE IntelliJ IDEA phục vụ phát triển Backend.
  * Khởi tạo container cho Database MySQL và n8n Engine.
* **[ ] Giai đoạn 2: Phát triển Core Backend (Java Spring Boot)**
  * Xây dựng giao diện Web đặt hàng và quản lý cho Admin/User.
  * Thiết kế Database Schema (`users`, `orders`, `delivery_history`).
  * Viết API nhận đơn hàng và kích hoạt Webhook gửi sang n8n.
* **[ ] Giai đoạn 3: Thiết kế Workflow Tự động hóa (n8n Design)**
  * Nhận Webhook từ Spring Boot (thông tin kiện hàng, địa chỉ).
  * HTTP Request gọi Mock API báo giá của 3 đơn vị vận chuyển.
  * Viết logic (Code Node) để so sánh và lọc ra mức giá vận chuyển rẻ nhất.
  * Tích hợp Node MySQL để tự động cập nhật giá vào Database.
* **[ ] Giai đoạn 4: Tích hợp, Mở rộng & Báo cáo (Integration & Delivery)**
  * Mở rộng luồng n8n: Tự động gửi tin nhắn báo đơn cho khách và bắn đơn cho Shipper (qua Telegram).
  * Test toàn trình (End-to-End) từ lúc đặt hàng đến lúc chốt giá.
  * Viết tài liệu báo cáo và chuẩn bị kịch bản demo.

## 🚀 Hướng dẫn khởi chạy (How to Run)
Hệ thống yêu cầu máy chủ Linux có cài đặt sẵn Docker. Chạy các lệnh sau tại Terminal để khởi động các service ngầm:

**Bước 1: Khởi chạy Database MySQL**
Chạy lệnh sau để khởi động MySQL, mở port 3306 và tạo sẵn database `delivery_db`. Dữ liệu được lưu trữ an toàn qua Docker Volume.
```bash
sudo docker run -d --name mysql-server -p 3306:3306 -v mysql_data:/var/lib/mysql -e MYSQL_ROOT_PASSWORD=123456 -e MYSQL_DATABASE=delivery_db mysql:latest

** Bước 2: Khởi chạy Tự động hóa n8n
Chạy lệnh sau để khởi động n8n core. Dữ liệu workflow được bảo lưu qua Volume.
sudo docker run -d --name n8n -p 5678:5678 -v n8n_data:/home/node/.n8n docker.n8n.io/n8nio/n8n

** Bước 3: Khởi chạy Core Backend
Clone repository này về máy.
Mở thư mục code bằng IntelliJ IDEA.
Cấu hình file application.properties để trỏ vào MySQL ở Bước 1.
Chạy project Spring Boot và truy cập giao diện Web.
