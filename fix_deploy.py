#!/usr/bin/env python3
import paramiko

HOST = "103.118.29.21"
USER = "root"
PASS = "@Lehuuthai2005"

ENV_CONTENT = """APP_NAME="AI Logistics"
APP_ENV=production
APP_KEY=base64:sMWyu3JmAobt6teMgNfkgD3EzTO2AkUrOgQWuVJ9xjY=
APP_DEBUG=false
APP_URL=http://103.118.29.21:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.dxrzessjpdhyyfmlgauv
DB_PASSWORD=Password1234512345******1

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

N8N_BASE_URL=http://103.118.29.21:5678
N8N_WEBHOOK_AI_TRIGGER=ai-logistic-trigger
N8N_WEBHOOK_TELEGRAM=627dd940-97f9-472c-b88b-93f953d7520a
N8N_WEBHOOK_OPTIMIZE=e99d1f26-3a52-49d9-93e5-ed402977fcb6

VITE_APP_NAME="${APP_NAME}"
"""

def run(ssh, cmd, timeout=60):
    print(f"\n>>> {cmd[:80]}")
    stdin, stdout, stderr = ssh.exec_command(cmd, timeout=timeout, get_pty=True)
    out = ""
    while True:
        line = stdout.readline()
        if not line:
            break
        print(line, end="", flush=True)
        out += line
    return out

print("🔌 Connecting...")
ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(HOST, username=USER, password=PASS, timeout=10)
print("✅ Connected!\n")

# Fix 1: Tạo .env trực tiếp trên VPS
print("="*50)
print("📝 Creating .env file on VPS...")
# Upload .env via SFTP
sftp = ssh.open_sftp()
with sftp.open('/root/delivery-automation-platform/frontend-web/.env', 'w') as f:
    f.write(ENV_CONTENT)
sftp.close()
print("✅ .env file created!")

# Fix 2: Cài php8.3-pdo + PDO PostgreSQL extension
print("\n📦 Installing missing PHP extensions...")
run(ssh, "apt-get install -y php8.3-pgsql php8.3-common 2>/dev/null | tail -3")
run(ssh, "php -m | grep -i pdo")

# Fix 3: Tắt process đang dùng port 8000
print("\n🔧 Freeing port 8000...")
run(ssh, "fuser -k 8000/tcp 2>/dev/null || true")
run(ssh, "pkill -f 'php artisan serve' 2>/dev/null || true")
run(ssh, "sleep 2")
run(ssh, "lsof -i:8000 || echo 'Port 8000 is free'")

# Fix 4: Generate key và chạy artisan
print("\n⚙️  Finalizing Laravel setup...")
run(ssh, "cd ~/delivery-automation-platform/frontend-web && php artisan key:generate --force")
run(ssh, "cd ~/delivery-automation-platform/frontend-web && php artisan config:clear")
run(ssh, "cd ~/delivery-automation-platform/frontend-web && php artisan config:cache")
run(ssh, "cd ~/delivery-automation-platform/frontend-web && php artisan view:clear")
run(ssh, "cd ~/delivery-automation-platform/frontend-web && php artisan migrate --force 2>&1 | tail -5")

# Fix 5: Chạy Laravel
print("\n🚀 Starting Laravel server...")
run(ssh, "cd ~/delivery-automation-platform/frontend-web && nohup php artisan serve --host=0.0.0.0 --port=8000 > /var/log/laravel.log 2>&1 &")
run(ssh, "sleep 3")
run(ssh, "tail -5 /var/log/laravel.log")
result = run(ssh, "netstat -tunlp | grep 8000 && echo '✅ LARAVEL RUNNING!' || echo '❌ FAILED'")

print("""
╔══════════════════════════════════════════╗
║        ✅ FIX HOÀN THÀNH!               ║
╠══════════════════════════════════════════╣
║  🌐 http://103.118.29.21:8000           ║
║  🤖 http://103.118.29.21:5678           ║
╚══════════════════════════════════════════╝
""")
ssh.close()
