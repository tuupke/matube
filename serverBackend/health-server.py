#!/usr/bin/python
from utilsForStats import *
import threading
import json

def main():
    printit()


def printit():
  threading.Timer(5.0, printit).start()
  writeOut(getHealth())


def getHealth():
        data = {}
        data["freeMemory"] = getFreeMemory()
        data["oneMinLoad"] = getOneMinLoad()
        data["fiveMinLoad"] = getFiveMinLoad()
        data["tenMinLoad"] = getTenMinLoad()
        data["localIP"] = getLocalIP()
        data["jobStatus"] = getStatus()
        return json.dumps(data, indent=4, separators=(',', ': '))

def writeOut(text):
    filename = "/usr/share/nginx/html/index.html"
    f = open(filename, 'w')
    f.write(text)
    f.close()


main()