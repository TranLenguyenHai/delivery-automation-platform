#!/usr/bin/env python3
import paramiko
import time
import sys

HOST = "103.118.29.21"
USER = "root"
PASS = "@Lehuuthai2005"

def run(ssh, cmd, timeout=120):
    print(f"\n\033[94m>>> {cmd[:80]}\033[0m")
    stdin, stdout, stderr = ssh.exec_command(cmd, timeout=timeout, get_pty=True)
    out = ""
    while True:
        line = stdout.readline()
        if not line:
            break
        print(line, end="", flush=True)
        out += line
    err = stderr.read().decode()
    if err and "WARNING" not in err and "Do not run" not in err:
        print(f"\033[93m{err}\033[0m")
    return out

print("🔌 Connecting to VPS 103.118.29.21...")
ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(HOST, username=USER, password=PASS, timeout=10)
print("✅ Connected!\n")

STEPS = [
    # Bước 1: Kiểm tra tình trạng
    ("📊 Checking VPS status...", [
        "netstat -tunlp | grep -E '8000|5678' || echo 'No services running on 8000/5678'",
        "ls ~/delivery-automation-platform/frontend-web/ 2>/dev/null || echo 'CODE NOT FOUND'",
        "php --version 2>/dev/null || echo 'PHP NOT INSTALLED'",
    ]),

    # Bước 2: Pull code mới nhất
    ("📥 Pulling latest code...", [
        "cd ~/delivery-automation-platform && git pull origin main 2>&1 || git clone https://github.com/TranLenguyenHai/delivery-automation-platform.git ~/delivery-automation-platform && echo 'Code OK'",
    ]),

    # Bước 3: Cài PHP 8.3
    ("🔧 Installing PHP 8.3...", [
        "apt-get install -y software-properties-common 2>/dev/null",
        "add-apt-repository ppa:ondrej/php -y 2>/dev/null",
        "apt-get update -qq",
        "apt-get install -y php8.3-cli php8.3-curl php8.3-xml php8.3-mbstring php8.3-pgsql php8.3-zip php8.3-bcmath 2>/dev/null",
        "update-alternatives --set php /usr/bin/php8.3 2>/dev/null || true",
        "php --version",
    ]),

    # Bước 4: Cài Composer
    ("📦 Installing Composer...", [
        "command -v composer || (curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer)",
        "composer --version",
    ]),

    # Bước 5: Setup Laravel
    ("⚙️  Setting up Laravel...", [
        "cd ~/delivery-automation-platform/frontend-web && COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -5",
        "cd ~/delivery-automation-platform/frontend-web && cp .env.production .env && echo '.env copied'",
        "cd ~/delivery-automation-platform/frontend-web && php artisan key:generate --force",
        "cd ~/delivery-automation-platform/frontend-web && php artisan config:clear && php artisan view:clear",
        "cd ~/delivery-automation-platform/frontend-web && php artisan migrate --force 2>&1 | tail -5 || echo 'Migration done or skipped'",
    ]),

    # Bước 6: Fix port 5678 và chạy n8n
    ("🐳 Fixing n8n port issue...", [
        "lsof -t -i:5678 | xargs kill -9 2>/dev/null || true",
        "docker rm -f n8n 2>/dev/null || true",
        "sleep 2",
        "systemctl restart docker",
        "sleep 3",
        "cd ~/delivery-automation-platform && docker-compose up -d && echo 'n8n started!' || echo 'n8n failed, checking...'",
        "docker ps | grep n8n || echo 'n8n container not running'",
    ]),

    # Bước 7: Chạy Laravel server
    ("🚀 Starting Laravel server...", [
        "pkill -f 'php artisan serve' 2>/dev/null || true",
        "sleep 1",
        "ufw allow 8000/tcp 2>/dev/null || true",
        "ufw allow 5678/tcp 2>/dev/null || true",
        "cd ~/delivery-automation-platform/frontend-web && nohup php artisan serve --host=0.0.0.0 --port=8000 > /var/log/laravel.log 2>&1 &",
        "sleep 3",
        "tail -10 /var/log/laravel.log",
        "netstat -tunlp | grep 8000 && echo '✅ Laravel is RUNNING on port 8000' || echo '❌ Laravel NOT running'",
    ]),
]

success = True
for label, cmds in STEPS:
    print(f"\n{'='*60}")
    print(f"  {label}")
    print('='*60)
    for cmd in cmds:
        try:
            run(ssh, cmd)
        except Exception as e:
            print(f"⚠️  Error: {e}")

print("\n" + "="*60)
print("  FINAL STATUS CHECK")
print("="*60)
run(ssh, "netstat -tunlp | grep -E '8000|5678'")
run(ssh, "docker ps")

print(f"""
╔════════════════════════════════════════════════════╗
║            🎉 DEPLOY HOÀN THÀNH!                  ║
╠════════════════════════════════════════════════════╣
║  🌐 Laravel:  http://103.118.29.21:8000           ║
║  🤖 n8n:      http://103.118.29.21:5678           ║
║  📋 Log:      tail -f /var/log/laravel.log        ║
╚════════════════════════════════════════════════════╝
""")
ssh.close()
