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
# Fix permissions
chown -R 1000:1000 n8n_data/
chmod -R 775 n8n_data/

# Add env var if missing
if ! grep -q "N8N_ENFORCE_SETTINGS_FILE_PERMISSIONS" docker-compose.yml; then
    sed -i '/N8N_SECURE_COOKIE/a\      - N8N_ENFORCE_SETTINGS_FILE_PERMISSIONS=false' docker-compose.yml
fi

docker restart n8n
"""
print(run(cmd))

ssh.close()
