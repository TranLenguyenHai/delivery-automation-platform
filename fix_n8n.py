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

# 1. Chỉnh quyền thư mục
run("mkdir -p /root/n8n_data")
run("chown -R 1000:1000 /root/n8n_data")

# 2. Xóa container cũ
run("docker rm -f n8n")

# 3. Chạy lại n8n với quyền chính xác
run("""docker run -d \
    --name n8n \
    --restart always \
    -p 5678:5678 \
    -e WEBHOOK_URL=http://103.118.29.21:5678 \
    -e N8N_HOST=103.118.29.21 \
    -e N8N_PORT=5678 \
    -e N8N_PROTOCOL=http \
    -e N8N_CORS_ALLOWED_ORIGINS=* \
    -v /root/n8n_data:/home/node/.n8n \
    docker.n8n.io/n8nio/n8n:1.41.1""")

# 4. Chờ 5s và kiểm tra log
run("sleep 5")
run("docker logs n8n | head -n 20")
run("docker ps | grep n8n")

ssh.close()
