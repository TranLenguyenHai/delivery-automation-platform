#!/usr/bin/env python3
import paramiko

HOST = "103.118.29.21"
USER = "root"
PASS = "@Lehuuthai2005"

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(HOST, username=USER, password=PASS, timeout=10)

def run(cmd):
    stdin, stdout, stderr = ssh.exec_command(cmd)
    return stdout.read().decode() + stderr.read().decode()

cmd = """
cd ~/delivery-automation-platform
git fetch origin main
git reset --hard origin/main
cd frontend-web
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
cd ~/delivery-automation-platform
docker-compose up -d
cd frontend-web
pkill -f "php artisan serve" || true
nohup php artisan serve --host=0.0.0.0 --port=8000 > /var/log/laravel.log 2>&1 &
"""
print(run(cmd))

ssh.close()
