__author__ = 'max'
"""
Listens for notifications about completed jobs. When notification comes in, the file should have been rsynced to
this servers file directory. It is this processes responsibility to remove this entry from the PendingJob table in the database,
add it to the Completed job table, and notify the user to download the file.
"""
import pika
import json
from MatubeEmail import *
from utilsForStats import *
import subprocess
import requests

connection = pika.BlockingConnection(pika.ConnectionParameters(
        host='10.133.234.184'))
channel = connection.channel()

remotepath = "/videos/"
filespath = "/usr/share/nginx/html/videos"


def retrieve_file(remoteserver, filename):
    subprocess.check_output("wget -P " + filespath + " " + remoteserver + remotepath + filename,shell=True)



channel.queue_declare(queue='completedJobs'+getLocalIP(), durable=True)
print ' [*] Waiting for messages. To exit press CTRL+C'

def callback(ch, method, properties, body):
    print " [x] Received completed job %r" % (body,)
    # Do something with job.

    # get filename, email address
    # remove filename,email from pending DB, add to completed DB

    # Notify user via email to download their file

    job = json.loads(body)

    retrieve_file(job['workerserver'], job['filename'])
    retrieve_file(job['workerserver'], job['filename'].split(".")[0] + ".jpg")
    try:
        MatubeEmail(job['email'], getPublicIP() + "/videos/" + job['filename'])
    except:
        pass
    r = requests.get('http://178.62.239.233/conv_done.php?newName=' + job['filename'] + '&oldName=' + job['oldFilename'] + '&hash=41dc8c4ced0a3ec02593499f3f58fec306dc58903c054abaff5045ee9f189a96')



    ch.basic_ack(delivery_tag = method.delivery_tag)

channel.basic_qos(prefetch_count=1)
channel.basic_consume(callback,
                      queue='completedJobs'+getLocalIP())

channel.start_consuming()