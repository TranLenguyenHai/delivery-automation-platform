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
    out = stdout.read().decode()
    err = stderr.read().decode()
    return out + err

print("Checking Laravel log...")
print(run("tail -n 50 /root/delivery-automation-platform/frontend-web/storage/logs/laravel.log"))

ssh.close()
