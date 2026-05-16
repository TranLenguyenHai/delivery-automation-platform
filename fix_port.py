#!/usr/bin/env python3
import paramiko

HOST = "103.118.29.21"
USER = "root"
PASS = "@Lehuuthai2005"

SYSTEMD_SERVICE = """[Unit]
Description=Laravel Delivery Platform
After=network.target

[Service]
Type=simple
User=root
WorkingDirectory=/root/delivery-automation-platform/frontend-web
ExecStart=/usr/bin/php artisan serve --host=0.0.0.0 --port=8080
Restart=always
RestartSec=5
StandardOutput=append:/var/log/laravel.log
StandardError=append:/var/log/laravel.log

[Install]
WantedBy=multi-user.target
"""

def run(ssh, cmd, timeout=30):
    print(f"\n>>> {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd, timeout=timeout, get_pty=True)
    out = ""
    while True:
        line = stdout.readline()
        if not line:
            break
        print(line, end="", flush=True)
        out += line
    return out

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(HOST, username=USER, password=PASS, timeout=10)
print("✅ Connected!")

# Upload systemd service file
sftp = ssh.open_sftp()
with sftp.open('/etc/systemd/system/laravel.service', 'w') as f:
    f.write(SYSTEMD_SERVICE)
sftp.close()
print("✅ Systemd service file created!")

# Enable và start service
run(ssh, "systemctl daemon-reload")
run(ssh, "systemctl stop laravel 2>/dev/null || true")
run(ssh, "systemctl enable laravel")
run(ssh, "systemctl start laravel")
run(ssh, "sleep 3")
run(ssh, "systemctl status laravel --no-pager")
run(ssh, "netstat -tunlp | grep 8080")
out = run(ssh, "curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8080/")

if any(code in out for code in ["200", "302", "301", "404"]):
    print(f"\n{'='*50}")
    print("✅ LARAVEL IS LIVE!")
    print("🌐 Truy cập: http://103.118.29.21:8080")
    print("🤖 n8n:      http://103.118.29.21:5678")
else:
    run(ssh, "journalctl -u laravel -n 20 --no-pager")

ssh.close()
