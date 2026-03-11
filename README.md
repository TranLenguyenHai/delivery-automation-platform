# Delivery Cost Optimization Automation Platform 

**Môn học:** Chuyên đề 2 (IT) - Low code, No code & Automation

## Giới thiệu dự án (Project Overview)
Dự án này xây dựng một nền tảng tự động hóa nhằm tối ưu hóa chi phí vận chuyển (Delivery Cost Optimization). Hệ thống tiếp nhận thông tin đơn hàng, tự động truy vấn giá cước từ nhiều đơn vị vận chuyển khác nhau (GHN, GHTK, Viettel Post,...), so sánh và lựa chọn đối tác có chi phí thấp nhất để tiến hành giao hàng, báo cáo tổng đơn hàng, gửi mail báo cáo tiến độ.

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

## Hướng dẫn khởi chạy (How to Run)
Hệ thống yêu cầu máy chủ Linux có cài đặt sẵn Docker. Chạy các lệnh sau tại Terminal để khởi động các service ngầm:

Bước 1: Khởi động hệ thống nền (Database & Automation Engine)
Trường hợp 1: Chạy lần đầu tiên (Dành cho máy mới clone code về)
Mở Terminal tại thư mục gốc của dự án (nơi chứa file docker-compose.yml) và chạy lệnh sau để Docker tự động tải và xây dựng hệ thống:

sudo docker compose up -d

Trường hợp 2: Chạy các lần sau (Tắt máy mở lại)
Vì các container đã được tạo sẵn từ lần chạy đầu tiên, bạn chỉ cần dùng lệnh start để đánh thức hệ thống dậy cho nhanh:

sudo docker start mysql-server n8n
sudo docker ps

Bước 2: Khởi chạy Core Backend
Mở thư mục dự án bằng IntelliJ IDEA.
Kiểm tra file src/main/resources/application.properties, đảm bảo kết nối Database đang trỏ về:
spring.datasource.url=jdbc:mysql://localhost:3306/delivery_db
Nhấn nút Run để khởi động Spring Boot Application.

Bước 3: Truy cập và Sử dụng
Sau khi tất cả các dịch vụ đã chạy thành công, truy cập các đường dẫn sau:
Giao diện đặt hàng (Web): http://localhost:8080
Hệ thống tự động hóa (n8n): http://localhost:5678

Lệnh hữu ích cho Database (Dành cho Developer)
Để kiểm tra trực tiếp dữ liệu đơn hàng đã được luân chuyển hay chưa, chạy lệnh sau để truy cập vào MySQL trong Docker:
Bash

sudo docker exec -it mysql-server mysql -u root -p
