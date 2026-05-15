#!/bin/bash
# ===========================================
# FULL DEPLOY SCRIPT - Chạy ngay trên VPS
# ===========================================
set -e

VPS_IP="103.118.29.21"
REPO="https://github.com/TranLenguyenHai/delivery-automation-platform.git"
APP_DIR="/root/delivery-automation-platform"
LARAVEL_DIR="$APP_DIR/frontend-web"

echo ""
echo "╔═══════════════════════════════════════════╗"
echo "║   DEPLOYING Delivery Automation Platform  ║"
echo "╚═══════════════════════════════════════════╝"

# ---- BƯỚC 1: Lấy/cập nhật code ----
echo ""
echo "📥 [1/6] Pulling latest code from GitHub..."
if [ -d "$APP_DIR" ]; then
    cd "$APP_DIR"
    git pull origin main
else
    git clone "$REPO" "$APP_DIR"
    cd "$APP_DIR"
fi

# ---- BƯỚC 2: Cài PHP và extensions ----
echo ""
echo "🔧 [2/6] Installing PHP 8.2..."
apt-get update -qq
apt-get install -y software-properties-common curl
add-apt-repository ppa:ondrej/php -y
apt-get update -qq
apt-get install -y php8.2-cli php8.2-curl php8.2-xml php8.2-mbstring php8.2-pgsql php8.2-zip php8.2-bcmath unzip
php -v

# ---- BƯỚC 3: Cài Composer ----
echo ""
echo "📦 [3/6] Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi
composer --version

# ---- BƯỚC 4: Setup Laravel ----
echo ""
echo "⚙️  [4/6] Setting up Laravel..."
cd "$LARAVEL_DIR"
composer install --no-dev --optimize-autoloader --no-interaction

# Tạo .env từ .env.production
if [ -f ".env.production" ]; then
    cp .env.production .env
else
    cp .env.example .env
fi

php artisan key:generate --force
php artisan config:clear
php artisan config:cache
php artisan view:cache

echo "Laravel setup complete."

# ---- BƯỚC 5: Fix cổng 5678 và khởi động n8n ----
echo ""
echo "🐳 [5/6] Fixing port 5678 and starting n8n..."
cd "$APP_DIR"

# Tìm và tắt tiến trình chiếm cổng 5678
PORT_PID=$(lsof -t -i:5678 2>/dev/null || true)
if [ -n "$PORT_PID" ]; then
    echo "Killing process $PORT_PID on port 5678..."
    kill -9 $PORT_PID
    sleep 2
fi

# Dọn sạch container cũ
docker rm -f n8n 2>/dev/null || true
docker-compose down 2>/dev/null || true

# Chờ cổng giải phóng hoàn toàn
sleep 3

# Thử bật n8n với IP binding cụ thể
docker run -d \
    --name n8n \
    --restart always \
    -p 5678:5678 \
    -e WEBHOOK_URL=http://103.118.29.21:5678 \
    -e N8N_HOST=103.118.29.21 \
    -e N8N_PORT=5678 \
    -e N8N_PROTOCOL=http \
    -e N8N_CORS_ALLOWED_ORIGINS=* \
    -e N8N_RESPONSE_MODE=responseNode \
    -v /root/n8n_data:/home/node/.n8n \
    n8nio/n8n:latest || {
        echo "Port 5678 still busy, trying port 5679..."
        docker run -d \
            --name n8n \
            --restart always \
            -p 5679:5678 \
            -e WEBHOOK_URL=http://103.118.29.21:5679 \
            -e N8N_HOST=103.118.29.21 \
            -e N8N_PORT=5678 \
            -e N8N_PROTOCOL=http \
            -e N8N_CORS_ALLOWED_ORIGINS=* \
            -v /root/n8n_data:/home/node/.n8n \
            n8nio/n8n:latest
        echo "n8n started on port 5679!"
    }

# ---- BƯỚC 6: Chạy Laravel ----
echo ""
echo "🚀 [6/6] Starting Laravel server..."
cd "$LARAVEL_DIR"

# Tắt Laravel cũ nếu đang chạy
pkill -f "php artisan serve" 2>/dev/null || true
sleep 1

# Mở cổng firewall
ufw allow 8000/tcp 2>/dev/null || true
ufw allow 5678/tcp 2>/dev/null || true
ufw allow 5679/tcp 2>/dev/null || true

# Chạy Laravel trong background
nohup php artisan serve --host=0.0.0.0 --port=8000 > /var/log/laravel.log 2>&1 &
LARAVEL_PID=$!
sleep 3

echo ""
echo "╔═══════════════════════════════════════════════════════╗"
echo "║              ✅ DEPLOY HOÀN THÀNH!                    ║"
echo "╠═══════════════════════════════════════════════════════╣"
echo "║  🌐 Laravel:  http://103.118.29.21:8000              ║"
echo "║  🤖 n8n:      http://103.118.29.21:5678              ║"
echo "║  📋 Laravel Log: tail -f /var/log/laravel.log        ║"
echo "║  📋 n8n Log:     docker logs -f n8n                  ║"
echo "╠═══════════════════════════════════════════════════════╣"
echo "║  Bước tiếp theo trong n8n:                           ║"
echo "║  1. Vào http://103.118.29.21:5678                    ║"
echo "║  2. Workflows → Import from file                      ║"
echo "║  3. Import 2 file trong n8n-workflows/               ║"
echo "║  4. Kết nối Groq credentials                         ║"
echo "║  5. Publish cả 2 workflow                            ║"
echo "╚═══════════════════════════════════════════════════════╝"
