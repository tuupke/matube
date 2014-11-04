#!/usr/bin/env python
import digitalocean
import json
import urllib2
import sys

def main():
    if (len(sys.argv) == 1) or ('all' in sys.argv[1]):
        servers = get_servers()
        for server in servers:
            print getStatus(server)

def get_servers():
    filename = 'servers.txt'
    fp = open(filename, 'r')
    servers = json.load(fp)
    return servers

def getHealth(serverIP):
    response = urllib2.urlopen("http://" + str(serverIP) + ":8080").read()
    return response

def getStatus(server):
    try:
        status = json.loads(getHealth(server['ip']))
        for key in status.keys():
            server[key] = status[key]

        return server
    except:
        return "{Server not responding}"
main()