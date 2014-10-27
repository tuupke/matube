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

connection = pika.BlockingConnection(pika.ConnectionParameters(
        host='10.133.234.184'))
channel = connection.channel()

channel.queue_declare(queue='completedJobs'+getLocalIP(), durable=True)
print ' [*] Waiting for messages. To exit press CTRL+C'

def callback(ch, method, properties, body):
    print " [x] Received completed job %r" % (body,)
    # Do something with job.

    # get filename, email address
    # remove filename,email from pending DB, add to completed DB

    # Notify user via email to download their file

    job = json.loads(body)

    MatubeEmail('jmsumrall@gmail.com', getPublicIP() + "/videos/" + job['filename'])



    ch.basic_ack(delivery_tag = method.delivery_tag)

channel.basic_qos(prefetch_count=1)
channel.basic_consume(callback,
                      queue='completedJobs'+getLocalIP())

channel.start_consuming()