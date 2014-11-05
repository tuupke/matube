#!/usr/bin/env python
__author__ = 'max'
import digitalocean
import json
import sys
token = "f32d2fa8bbc1199eb4c079a1f70de31155778c1f0aef9e17226ce3869dd51a81"

manager = digitalocean.Manager(token=token)
filename = 'servers.txt'
fp = open(filename, 'r')
servers = json.load(fp)
fp.close()

def main():
    if sys.argv[1] == "add":
        serverSize = int(sys.argv[2])
        addServer(serverSize)
    elif sys.argv[1] == "remove":
        serverIP = sys.argv[2]
        removeServer(serverIP)

def addServer(serverSize):
    size_param = '512mb'
    if serverSize == 1:
        size_param = '512mb'
    elif serverSize == 2:
        size_param = '1024mb'
    elif serverSize == 3:
        size_param = '2048mb'
    images = manager.get_my_images()
    workerImg = images[0]
    for img in images:
        if 'worker' in str(img):
            workerImg = img
    # new worker droplet
    droplet = digitalocean.Droplet(token=token,
                                   name='2IN28-worker',
                                   region='ams3',
                                   ssh_keys=manager.get_all_sshkeys(),
                                   image=workerImg,
                                   size_slug='512mb',
                                   backups=False,
                                   private_networking=True)
    print "Created new worker droplet: " + size_param
    droplet.create()
    server = {'ip': droplet.ip_address,
                  'name': droplet.name,
                  'memory': droplet.memory,
                  'vcpus': droplet.vcpus,
                  'private_ip': droplet.private_ip_address}
    servers.append(server)
    fp = open(filename,'w')
    json.dump(servers,fp)
    fp.close()

def removeServer(serverIP):
    droplets = []
    temp_drops = manager.get_all_droplets()
    for drop in temp_drops:
        if "2IN28" in drop.name:
            droplets.append(drop)
    for drop in droplets:
        if drop.ip_address == serverIP:
            drop.destroy()
            print 'Removed: ' + serverIP

    for server in servers:
        if server['ip'] == serverIP:
            servers.remove(server)
            fp = open(filename,'w')
            json.dump(servers,fp)
            fp.close()


main()
