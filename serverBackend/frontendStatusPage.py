import digitalocean
import web
import json
import urllib2

token = "f32d2fa8bbc1199eb4c079a1f70de31155778c1f0aef9e17226ce3869dd51a81"

manager = digitalocean.Manager(token=token)

urls = (
    '/', 'index',
    '/workers', 'workers',
    '/fileserver', 'fileserver',
    '/frontend', 'frontend',
)

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


class index:
    def GET(self):
        servers = []
        for drop in manager.get_all_droplets():
            if "2IN28" in drop.name:
                servers.append(drop)

        responses = []
        for server in servers:
            responses.append(getStatus(server))
        return json.dumps(responses, indent=4, separators=(',', ': '))

class workers:
    def GET(self):
        servers = []
        for drop in manager.get_all_droplets():
            if "worker" in drop.name:
                servers.append(drop)

        responses = []
        for server in servers:
            responses.append(getStatus(server))
        return json.dumps(responses, indent=4, separators=(',', ': '))


class fileserver:
    def GET(self):
        servers = []
        for drop in manager.get_all_droplets():
            if "fileserver" in drop.name:
                servers.append(drop)

        responses = []
        for server in servers:
            responses.append(server)
        return json.dumps(responses, indent=4, separators=(',', ': '))


class frontend:
    def GET(self):
        servers = []
        for drop in manager.get_all_droplets():
            if "frontend" in drop.name:
                servers.append(drop)

        responses = []
        for server in servers:
            responses.append(server)
        return json.dumps(responses, indent=4, separators=(',', ': '))

class addWorker:
    def GET(self):
        workers = []

        workerIMG = manager.get_my_images()[9]
        # new worker droplet
        droplet = digitalocean.Droplet(token=token,
                                       name='2IN28-worker',
                                       region='ams3',
                                       ssh_keys=manager.get_all_sshkeys(),
                                       image=workerIMG.id,
                                       size='512mb',  # 512MB
                                       backups=False,
                                       private_networking=True)
        print droplet
        droplet.create()


if __name__ == "__main__":
    app = web.application(urls, globals())
    app.run()
