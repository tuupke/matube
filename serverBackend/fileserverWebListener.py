__author__ = 'max'
"""
Listens for communication from the website/frontend for notification about a new job.
Adds the job to the Database in the PendingJob table, then sends the job data off to the worker.
"""

import pika
from utilsForStats import *
import json
import subprocess
import time

connection = pika.BlockingConnection(pika.ConnectionParameters(
        host='10.133.234.184'))
channel = connection.channel()

channel.queue_declare(queue='newJobs', durable=True)

frontend = "10.133.234.184"
remotepath = "/videos/"
filespath = "/usr/share/nginx/html/videos"

def retrieve_file(filename):
    subprocess.check_output("wget -P " + filespath + " " + frontend + remotepath + filename,shell=True)

def give_file_unique_name(filename):
    splitname = filename.split(".")
    newFilename = splitname[0] + time.time() + "." + splitname[1]
    subprocess.check_output("mv " + filespath + filename + " " + filespath + newFilename ,shell=True)
    return newFilename

print ' [*] Waiting for messages. To exit press CTRL+C'

def callback(ch, method, properties, body):
    print " [x] Received %r" % (body,)
    # Do something with job.

    # get filename, email address
    # add filename,email to DB

    # send job off to queue to be consumed by worker server.
    incomingJob = json.loads(body)


    task = {
        'fileserver' : getLocalIP(),
        'filename' : give_file_unique_name(incomingJob['filename'])
    }

    retrieve_file(incomingJob['filename'])


    channel.basic_publish(exchange='',
                          routing_key='processJobs',
                          body=json.dumps(task))




    print " [x] Done"
    ch.basic_ack(delivery_tag = method.delivery_tag)

channel.basic_qos(prefetch_count=1)
channel.basic_consume(callback,
                      queue='newJobs')

channel.start_consuming()