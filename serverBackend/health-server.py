#!/usr/bin/python
import web
import subprocess
import json

urls = (
    '/', 'index'
)

class index:

    def getHealth(self):
	data = {}
	data["freeMemory"] = subprocess.check_output("free -m | grep -n 1 | awk {print $4}'", shell=True).strip()
	data["oneMinLoad"] = subprocess.check_output("uptime | awk '{print $8}'", shell=True).replace(",","").strip()
	data["fiveMinLoad"] = subprocess.check_output("uptime | awk '{print $9}'", shell=True).replace(",","").strip()
	data["tenMinLoad"] = subprocess.check_output("uptime | awk '{print $10}'", shell=True).replace(",","").strip()
	return json.dumps(data, indent=4, separators=(',', ': '))

    def GET(self):
        return self.getHealth()


if __name__ == "__main__":
    app = web.application(urls, globals())
    app.run()
