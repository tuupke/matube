__author__ = 'max'
import subprocess

def getLocalIP():
    return subprocess.check_output("/sbin/ifconfig eth1 | grep 'inet addr' | cut -d: -f2 | awk '{print $1}'",shell=True).strip()

def getPublicIP():
    return subprocess.check_output("/sbin/ifconfig eth0 | grep 'inet addr' | cut -d: -f2 | awk '{print $1}'",shell=True).strip()

def getFreeMemory():
    return subprocess.check_output("free -m | awk '{print $4}'", shell=True).strip().split("\n")[-3]

def getOneMinLoad():
    return subprocess.check_output("uptime",shell=True).split(',')[-3].split(":")[-1].strip()

def getFiveMinLoad():
    return subprocess.check_output("uptime",shell=True).split(',')[-2].split(":")[-1].strip()

def getTenMinLoad():
    return subprocess.check_output("uptime",shell=True).split(',')[-1].split(":")[-1].strip()

def getStatus():
    return subprocess.check_output("more /root/status.txt", shell=True)

def getEta():
    return subprocess.check_output("more /root/eta.txt", shell=True)

def getRSAPubKey():
    return subprocess.check_output("more /root/.ssh/.id_rsa.pub", shell=True)
