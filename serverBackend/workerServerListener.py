__author__ = 'max'
"""
This resides on the worker servers.
 It is the responsibility of this process to listen for new jobs, process the video,
 send the video back, and send a message back.
"""



connection = pika.BlockingConnection(pika.ConnectionParameters(
        host='10.133.235.35'))
channel = connection.channel()

channel.queue_declare(queue='processJobs', durable=True)
print ' [*] Waiting for messages. To exit press CTRL+C'

def callback(ch, method, properties, body):
    print " [x] Received %r" % (body,)
    # Do something with job.

    # get filename, email address
    # add filename,email to DB

    # send job off to queue to be consumed by worker server.
    channel.basic_publish(exchange='',
                          routing_key='completedJobs',
                          body=body.append('--encoded'))




    print " [x] Done"
    ch.basic_ack(delivery_tag = method.delivery_tag)

channel.basic_qos(prefetch_count=1)
channel.basic_consume(callback,
                      queue='processJobs')

channel.start_consuming()