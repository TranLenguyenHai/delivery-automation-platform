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
    return out

print("Checking Laravel log error message...")
print(run("grep -E 'ERROR:|Exception' /root/delivery-automation-platform/frontend-web/storage/logs/laravel.log | tail -n 5"))

ssh.close()
