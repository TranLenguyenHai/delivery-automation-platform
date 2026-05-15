# 🚀 Hướng dẫn Deploy lên VPS

## Thông tin VPS

| Thông tin | Giá trị |
|---|---|
| IP | `103.118.29.21` |
| User | `root` |
| SSH Port | `22` |
| Laravel Port | `8000` |
| n8n Port | `5678` |

---

## Cách 1: Deploy tự động bằng script (Khuyến nghị)

```bash
# Trên máy local, chạy:
cd /home/hai/Project/delivery-automation-platform
chmod +x deploy.sh
./deploy.sh
```

Script sẽ tự động:
1. Upload toàn bộ code lên VPS
2. Cài Composer + PHP dependencies
3. Cấu hình Laravel (`.env`, key, cache)
4. Khởi động n8n bằng Docker
5. Khởi động Laravel server ở port 8000
6. Mở firewall và kiểm tra health

---

## Cách 2: Deploy thủ công từng bước

### Bước 1: SSH vào VPS

```bash
ssh root@103.118.29.21
```

### Bước 2: Clone/Upload code

```bash
mkdir -p /var/www/delivery-platform
# Upload bằng rsync hoặc git clone
```

### Bước 3: Cài PHP & Composer

```bash
apt-get update && apt-get install -y php8.2-cli php8.2-pgsql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

### Bước 4: Setup Laravel

```bash
cd /var/www/delivery-platform/frontend-web
composer install --no-dev --optimize-autoloader
cp .env.production .env
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Chạy server
nohup php artisan serve --host=0.0.0.0 --port=8000 > /var/log/laravel.log 2>&1 &
```

### Bước 5: Khởi động n8n

```bash
cd /var/www/delivery-platform
apt-get install -y docker.io
curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose
docker-compose up -d
```

### Bước 6: Mở firewall

```bash
ufw allow 8000
ufw allow 5678
```

---

## Bước 7: Setup n8n Workflows

1. Vào **http://103.118.29.21:5678** trên trình duyệt
2. Tạo tài khoản n8n lần đầu
3. Vào menu → **Workflows** → **Import from file**
4. Import lần lượt 2 file:
   - `n8n-workflows/Điều phối kho Giao Hàng .json`
   - `n8n-workflows/AI Logistics.json`
5. Kết nối lại **Groq API** credentials (vào Settings → Credentials → Add Groq)
6. Kết nối lại **SMTP** credentials nếu cần email
7. **Publish** cả 2 workflow (toggle Active)

---

## URLs sau khi deploy

| Service | URL |
|---|---|
| Laravel App | http://103.118.29.21:8000 |
| n8n Dashboard | http://103.118.29.21:5678 |
| API Orders | http://103.118.29.21:8000/api/orders |

---

## Kiểm tra nhanh

```bash
# Kiểm tra Laravel
curl http://103.118.29.21:8000

# Kiểm tra n8n  
curl http://103.118.29.21:5678

# Xem log Laravel
ssh root@103.118.29.21 "tail -f /var/log/laravel.log"

# Xem log n8n
ssh root@103.118.29.21 "docker logs -f n8n"
```
