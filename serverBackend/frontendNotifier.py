#!/usr/bin/python
__author__ = 'max'
"""
This process notifies the file server that a new file has been uploaded and that it needs to begin the process of encoding.
This is called when the user uploads a file.
"""
import pika
import json
import sys

connection = pika.BlockingConnection(pika.ConnectionParameters(host='localhost'))

channel = connection.channel()

task = {
    'filename': sys.argv[1],
    'email': sys.argv[2]
}

channel.basic_publish(exchange='',
                      routing_key='newJobs',
                      body=json.dumps(task))
print " [x] Sent 'new job' " + sys.argv[1] + " " + sys.argv[2]
connection.close()