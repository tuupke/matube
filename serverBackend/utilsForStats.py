__author__ = 'max'
import subprocess

def getLocalIP():
    return subprocess.check_output("ifconfig eth1 | grep 'inet addr' | cut -d: -f2 | awk '{print $1}'",shell=True).strip()

def getFreeMemory():
    return subprocess.check_output("free -m | grep -n 1 | awk {print $4}'", shell=True).strip()

def getOneMinLoad():
    return subprocess.check_output("uptime | awk '{print $8}'", shell=True).replace(",","").strip()

def getFiveMinLoad():
    return subprocess.check_output("uptime | awk '{print $9}'", shell=True).replace(",","").strip()

def getTenMinLoad():
    return subprocess.check_output("uptime | awk '{print $10}'", shell=True).replace(",","").strip()

def getRSAPubKey():
    return subprocess.check_output("more /root/.ssh/.id_rsa.pub", shell=True)
