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
chown -R 1000:1000 n8n_data/
chmod -R 775 n8n_data/
docker restart n8n
"""
print(run(cmd))

ssh.close()
