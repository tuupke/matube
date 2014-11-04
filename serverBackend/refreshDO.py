#!/usr/bin/env python
__author__ = 'max'
import digitalocean
import json
token = "f32d2fa8bbc1199eb4c079a1f70de31155778c1f0aef9e17226ce3869dd51a81"

manager = digitalocean.Manager(token=token)
filename = 'servers.txt'
fp = open(filename,'w')
servers = []
drops = manager.get_all_droplets()
for drop in drops:
    if "2IN28" in drop.name:
        server = {'ip': drop.ip_address,
                  'name': drop.name,
                  'memory': drop.memory,
                  'vcpus': drop.vcpus,
                  'private_ip': drop.private_ip_address}
        servers.append(server)

filename = 'servers.txt'
fp = open(filename,'w')
json.dump(servers, fp)
fp.close()