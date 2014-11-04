#!/usr/bin/env python
import digitalocean
import json
import urllib2
import sys

token = "f32d2fa8bbc1199eb4c079a1f70de31155778c1f0aef9e17226ce3869dd51a81"

manager = digitalocean.Manager(token=token)

def main():
    if (len(sys.argv) == 1) or ('all' in sys.argv[1]):
        print get_all()
    elif 'worker' in sys.argv[1]:
        print get_workers()
    elif 'fileserver' in sys.argv[1]:
        print get_fileserver()



def getHealth(serverIP):
    response = urllib2.urlopen("http://" + str(serverIP) + ":8080").read()
    return response

def getStatus(server):
    try:
        status = json.loads(getHealth(str(server.ip_address)))
        status['name'] = server.name
        status['memory'] = server.memory
        status['vcpus'] = server.vcpus
        status['disk'] = server.disk
        status['region'] = server.region
        status['status'] = server.status
        status['id'] = server.id
        status['actions'] = []
        actions = server.get_actions()
        for action in actions:
            action.load()
            status['actions'].append(action.status)
        return status
    except:
        return "Server Not responding"

def get_all():
    servers = []
    for drop in manager.get_all_droplets():
        if "2IN28" in drop.name:
            servers.append(drop)

    responses = []
    for server in servers:
        responses.append(getStatus(server))
    return json.dumps(responses, indent=4, separators=(',', ': '))

def get_workers():
    servers = []
    for drop in manager.get_all_droplets():
        if "worker" in drop.name:
            servers.append(drop)

    responses = []
    for server in servers:
        responses.append(getStatus(server))
    return json.dumps(responses, indent=4, separators=(',', ': '))


def get_fileserver():
    servers = []
    for drop in manager.get_all_droplets():
        if "fileserver" in drop.name:
            servers.append(drop)

    responses = []
    for server in servers:
        responses.append(server)
    return json.dumps(responses, indent=4, separators=(',', ': '))

main()