__author__ = 'max'
"""
This resides on the worker servers.
 It is the responsibility of this process to listen for new jobs, process the video,
 send the video back, and send a message back.
"""
import pika
from utilsForStats import *
import json
import subprocess
import sys
from converter import Converter

connection = pika.BlockingConnection(pika.ConnectionParameters(
        host='10.133.234.184'))
channel = connection.channel()

channel.queue_declare(queue='processJobs', durable=True)

remotepath = "/videos/"
filespath = "/usr/share/nginx/html/videos"

print ' [*] Waiting for messages. To exit press CTRL+C'

""" def sendStatusMessage(status,progress):
    channel.basic_publish(exchange='',
                      routing_key='status',
                      body=json.dumps({'type' : 'worker',
                           'local ip' : getLocalIP(),
                           'status' : status,
                           'progress' : progress}))
"""
def sendStatusMessage(status, progress):
    f = file('/root/status.txt','w')
    f.write(status + "," + str(progress))
    f.close()

def retrieve_file(remoteserver, filename):
    subprocess.check_output("wget -P " + filespath + " " + remoteserver + remotepath + filename,shell=True)

def deleteProcessedVideos(file1, file2):
    subprocess.check_output("rm " + filespath + file1 + " " + filespath + file2, shell=True)

def encodeFile(filename):
    encodedfilename = filename.split(".")[0] + ".ogg"

    c = Converter()
    options = {
        'format': 'mkv',
        'audio': {
            'codec': 'mp3',
            'samplerate': 11025,
            'channels': 2
        },
        'video': {
            'codec': 'h264',
            'width': 720,
            'height': 400,
            'fps': 15
        },
        'subtitle': {
            'codec': 'copy'
        },
        'map': 0
    }

    conv = c.convert(filespath + filename, filespath + encodedfilename, options)

    for timecode in conv:
        sys.stdout.write("\r%d%%" % timecode)
        sys.stdout.flush()
        sendStatusMessage('encoding', timecode)
    print "\n Complete"
    return encodedfilename



def callback(ch, method, properties, body):
    print " [x] Received %r" % (body,)
    # Do something with job.

    # get filename, email address
    # add filename,email to DB

    # send job off to queue to be consumed by worker server.

    job = json.loads(body)

    retrieve_file(job['fileserver'], job['filename'])

    encodedfile = encodeFile(job['filename'])

    #deleteProcessedVideos(job['filename'], encodedfile)

    job['workerserver'] = getLocalIP()
    job['filename'] = encodedfile


    # remove old files

    #send a message back
    channel.basic_publish(exchange='',
                          routing_key='completedJobs'+job['fileserver'],
                          body=json.dumps(job))

    #publish work status
    sendStatusMessage('idle', '')



    print " [x] Done"
    ch.basic_ack(delivery_tag = method.delivery_tag)

channel.basic_qos(prefetch_count=1)
channel.basic_consume(callback,
                      queue='processJobs')

channel.start_consuming()