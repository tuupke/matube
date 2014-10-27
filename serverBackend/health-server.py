#!/usr/bin/python
import web
import subprocess
import json
from utilsForStats import *

urls = (
    '/', 'index',
    '/syncPK', 'syncPK'
)

class index:

    def getHealth(self):
        data = {}
        data["freeMemory"] = getFreeMemory()
        data["oneMinLoad"] = getOneMinLoad()
        data["fiveMinLoad"] = getFiveMinLoad()
        data["tenMinLoad"] = getTenMinLoad()
        data["localIP"] = getLocalIP()
        return json.dumps(data, indent=4, separators=(',', ': '))

    def GET(self):
        return self.getHealth()



if __name__ == "__main__":
    app = web.application(urls, globals())
    app.run()
