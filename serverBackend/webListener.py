__author__ = 'max'
"""
Listens for communication from the website/frontend for notification about a new job.
Adds the job to the Database in the PendingJob table, then sends the job data off to the worker.
"""

import pika
import FileServer


connection = pika.BlockingConnection(pika.ConnectionParameters(
        host='localhost'))
channel = connection.channel()

channel.queue_declare(queue='completedJobs', durable=True)
print ' [*] Waiting for messages. To exit press CTRL+C'

def callback(ch, method, properties, body):
    print " [x] Received %r" % (body,)
    # Do something when a job is completed.


    print " [x] Done"
    ch.basic_ack(delivery_tag = method.delivery_tag)

channel.basic_qos(prefetch_count=1)
channel.basic_consume(callback,
                      queue='completedJobs')

channel.start_consuming()