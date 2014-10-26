__author__ = 'max'
"""
This process notifies the file server that a new file has been uploaded and that it needs to begin the process of encoding.
This is called when the user uploads a file.
"""
import pika
import time

connection = pika.BlockingConnection(pika.ConnectionParameters(
        host='10.133.235.35'))

channel = connection.channel()

#channel.queue_declare(queue='newJobs')

channel.basic_publish(exchange='',
                      routing_key='newJobs',
                      body='new job' + str(time.time()))
print " [x] Sent 'new job'"
connection.close()