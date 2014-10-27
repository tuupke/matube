__author__ = 'max'


import pika
from utilsForStats import *
import json
import threading
import web

msg = 'hello world'

def statusUpdater():
    connection = pika.BlockingConnection(pika.ConnectionParameters(
            host='localhost'))
    channel = connection.channel()

    channel.queue_declare(queue='status', durable=True)


    print ' [*] Waiting for messages. To exit press CTRL+C'

    def callback(ch, method, properties, body):
        print " [x] Received %r" % (body,)
        super.msg = body

        print " [x] Done"
        ch.basic_ack(delivery_tag = method.delivery_tag)

    channel.basic_qos(prefetch_count=1)
    channel.basic_consume(callback,
                          queue='status')

    channel.start_consuming()


t_msg = threading.Thread(target=statusUpdater)
t_msg.start()

urls = (
    '/', 'index')

class index:
    def GET(self):
        return msg

if __name__ == "__main__":
    app = web.application(urls, globals())
    app.run()
