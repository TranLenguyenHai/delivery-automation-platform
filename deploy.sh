#!/bin/bash
# ============================================================
# DEPLOY SCRIPT – Delivery Automation Platform VPS
# VPS: 103.118.29.21 | User: root | Port: 22
# ============================================================

set -e

VPS_IP="103.118.29.21"
VPS_USER="root"
VPS_PASS="@Lehuuthai2005"
VPS_PORT="22"
REMOTE_DIR="/var/www/delivery-platform"
LARAVEL_PORT="8000"
N8N_PORT="5678"

echo ""
echo "╔══════════════════════════════════════════════════════╗"
echo "║   DEPLOY: Delivery Automation Platform → VPS         ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""

# -------------------------------------------------------------------
# BƯỚC 1: Upload code lên VPS
# -------------------------------------------------------------------
echo "📦 [1/6] Uploading code to VPS..."
rsync -avz --exclude='vendor' --exclude='node_modules' --exclude='.git' \
  --exclude='frontend-web/storage/logs' --exclude='*.sqlite' \
  -e "ssh -p $VPS_PORT" \
  . $VPS_USER@$VPS_IP:$REMOTE_DIR

# -------------------------------------------------------------------
# BƯỚC 2: Cài đặt dependencies và cấu hình Laravel trên VPS
# -------------------------------------------------------------------
echo ""
echo "🔧 [2/6] Installing dependencies on VPS..."
ssh -p $VPS_PORT $VPS_USER@$VPS_IP << 'ENDSSH'
  cd /var/www/delivery-platform/frontend-web

  # Cài Composer nếu chưa có
  if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
  fi

  # Cài PHP packages
  composer install --no-dev --optimize-autoloader

  # Copy .env
  cp .env.production .env

  # Generate key nếu chưa có
  php artisan key:generate --force

  # Optimize
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache

  echo "Laravel setup done."
ENDSSH

# -------------------------------------------------------------------
# BƯỚC 3: Khởi động n8n bằng Docker
# -------------------------------------------------------------------
echo ""
echo "🐳 [3/6] Starting n8n with Docker..."
ssh -p $VPS_PORT $VPS_USER@$VPS_IP << 'ENDSSH'
  cd /var/www/delivery-platform

  # Cài Docker nếu chưa có
  if ! command -v docker &> /dev/null; then
    curl -fsSL https://get.docker.com | sh
    systemctl enable docker && systemctl start docker
  fi

  # Cài Docker Compose nếu chưa có
  if ! command -v docker-compose &> /dev/null; then
    curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
  fi

  # Chạy n8n
  docker-compose up -d --build
  echo "n8n started on port 5678"
ENDSSH

# -------------------------------------------------------------------
# BƯỚC 4: Khởi động Laravel server
# -------------------------------------------------------------------
echo ""
echo "🚀 [4/6] Starting Laravel server..."
ssh -p $VPS_PORT $VPS_USER@$VPS_IP << 'ENDSSH'
  cd /var/www/delivery-platform/frontend-web

  # Dừng process cũ nếu có
  pkill -f "php artisan serve" 2>/dev/null || true

  # Cài PHP nếu chưa có
  if ! command -v php &> /dev/null; then
    apt-get update -q
    apt-get install -y php8.2-cli php8.2-pgsql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip
  fi

  # Chạy Laravel trong background
  nohup php artisan serve --host=0.0.0.0 --port=8000 > /var/log/laravel.log 2>&1 &
  echo "Laravel started on port 8000"
  sleep 2
  cat /var/log/laravel.log | tail -5
ENDSSH

# -------------------------------------------------------------------
# BƯỚC 5: Mở firewall (nếu có UFW)
# -------------------------------------------------------------------
echo ""
echo "🔓 [5/6] Opening firewall ports..."
ssh -p $VPS_PORT $VPS_USER@$VPS_IP << 'ENDSSH'
  if command -v ufw &> /dev/null; then
    ufw allow 8000/tcp comment 'Laravel'
    ufw allow 5678/tcp comment 'n8n'
    echo "Firewall ports opened."
  else
    echo "No UFW firewall detected, skipping."
  fi
ENDSSH

# -------------------------------------------------------------------
# BƯỚC 6: Kiểm tra health
# -------------------------------------------------------------------
echo ""
echo "✅ [6/6] Health check..."
sleep 3
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://$VPS_IP:$LARAVEL_PORT" 2>/dev/null || echo "failed")
N8N_CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://$VPS_IP:$N8N_PORT" 2>/dev/null || echo "failed")

echo ""
echo "╔══════════════════════════════════════════════════════╗"
echo "║              DEPLOY HOÀN THÀNH!                      ║"
echo "╠══════════════════════════════════════════════════════╣"
echo "║  Laravel:  http://$VPS_IP:$LARAVEL_PORT  [HTTP: $HTTP_CODE]"
echo "║  n8n:      http://$VPS_IP:$N8N_PORT       [HTTP: $N8N_CODE]"
echo "╠══════════════════════════════════════════════════════╣"
echo "║  Bước tiếp theo:                                     ║"
echo "║  1. Vào http://$VPS_IP:$N8N_PORT để setup n8n"
echo "║  2. Import 2 file workflow từ n8n-workflows/"
echo "║  3. Kết nối lại Groq API credentials trong n8n"
echo "║  4. Publish cả 2 workflow"
echo "╚══════════════════════════════════════════════════════╝"
