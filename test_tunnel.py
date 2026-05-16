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

# Stop old processes
run("pkill -f 'ssh -R 80:localhost:5678'")

# Run localhost.run
run("nohup ssh -o StrictHostKeyChecking=no -R 80:localhost:5678 nokey@localhost.run > /root/localtunnel.log 2>&1 &")
run("sleep 5")
run("cat /root/localtunnel.log")

ssh.close()
