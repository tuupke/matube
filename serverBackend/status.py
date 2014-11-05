#!/usr/bin/env python
import digitalocean
import json
import urllib2
import sys

def main():
    if (len(sys.argv) == 1) or ('all' in sys.argv[1]):
        servers = get_servers()
        results = []
        for server in servers:
            results.append(getStatus(server))
        print json.dumps(results)

def get_servers():
    filename = 'servers.txt'
    fp = open(filename, 'r')
    servers = json.load(fp)
    return servers

def getHealth(serverIP):
    try:

        response = urllib2.urlopen("http://" + str(serverIP), timeout=1).read()
    except:
        response = "{}"
    return response

def getStatus(server):
    status = json.loads(getHealth(server['ip']))
    for key in status.keys():
        server[key] = status[key]
    server_tmp = {}
    for key in server.keys():
        server_tmp[str(key)] = str(server[key])
    return server_tmp

main()