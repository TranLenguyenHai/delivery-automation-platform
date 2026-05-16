#!/usr/bin/env python3
import paramiko
import time

HOST = "103.118.29.21"
USER = "root"
PASS = "@Lehuuthai2005"

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(HOST, username=USER, password=PASS, timeout=10)

def run(cmd):
    stdin, stdout, stderr = ssh.exec_command(cmd)
    return stdout.read().decode() + stderr.read().decode()

run("pkill -f 'ssh -R hai-n8n'")
time.sleep(1)
run("nohup ssh -o StrictHostKeyChecking=no -R hai-n8n:80:localhost:5678 serveo.net > /root/serveo.log 2>&1 &")
time.sleep(5)
print(run("cat /root/serveo.log"))

ssh.close()
