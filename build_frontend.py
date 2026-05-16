#!/usr/bin/env python3
import paramiko

HOST = "103.118.29.21"
USER = "root"
PASS = "@Lehuuthai2005"

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(HOST, username=USER, password=PASS, timeout=10)

def run(cmd, timeout=300):
    print(f"\n>>> {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd, timeout=timeout, get_pty=True)
    while True:
        line = stdout.readline()
        if not line:
            break
        print(line, end="", flush=True)

print("🔌 Connected to VPS! Fixing frontend build...")

# 1. Install Node.js (v20)
run("curl -fsSL https://deb.nodesource.com/setup_20.x | bash -")
run("apt-get install -y nodejs")
run("node -v && npm -v")

# 2. Build Vite assets
run("cd /root/delivery-automation-platform/frontend-web && npm install")
run("cd /root/delivery-automation-platform/frontend-web && npm run build")

print("\n✅ Vite build complete! The login page should now work.")
ssh.close()
