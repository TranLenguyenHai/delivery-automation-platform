#!/usr/bin/env python3
import paramiko

HOST = "103.118.29.21"
USER = "root"
PASS = "@Lehuuthai2005"

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(HOST, username=USER, password=PASS, timeout=10)

def run(cmd):
    print(f"\n>>> {cmd}")
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode()
    err = stderr.read().decode()
    print(out + err)

# Xóa container cũ
run("docker rm -f n8n")

# Chạy lại n8n với tham số N8N_SECURE_COOKIE=false
run("""docker run -d \
    --name n8n \
    --restart always \
    -p 5678:5678 \
    -e WEBHOOK_URL=http://103.118.29.21:5678 \
    -e N8N_HOST=103.118.29.21 \
    -e N8N_PORT=5678 \
    -e N8N_PROTOCOL=http \
    -e N8N_CORS_ALLOWED_ORIGINS=* \
    -e N8N_SECURE_COOKIE=false \
    -v /root/n8n_data:/home/node/.n8n \
    docker.n8n.io/n8nio/n8n:1.41.1""")

ssh.close()
