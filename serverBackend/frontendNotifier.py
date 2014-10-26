__author__ = 'max'
"""
This process notifies the file server that a new file has been uploaded and that it needs to begin the process of encoding.
This is called when the user uploads a file.
"""
import pika
import time
import json

connection = pika.BlockingConnection(pika.ConnectionParameters(
        host='10.133.235.35'))

channel = connection.channel()

#channel.queue_declare(queue='newJobs')

task = {
    'filename' : 'example1.mp4',
    'email' : 'jmsumrall@gmail.com'
}

channel.basic_publish(exchange='',
                      routing_key='newJobs',
                      body=json.dumps(task))
print " [x] Sent 'new job'"
connection.close()