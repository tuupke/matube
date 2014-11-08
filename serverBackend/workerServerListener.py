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
import time
from converter import Converter

connection = pika.BlockingConnection(pika.ConnectionParameters(
        host='10.133.234.184'))
channel = connection.channel()

channel.queue_declare(queue='processJobs', durable=True)

remotepath = "/videos/"
filespath = "/usr/share/nginx/html/videos/"

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

def update_eta(running_time, progress):
    f = file('/root/eta.txt','w')
    val = 4294967296
    if progress != 0
        val = ((running_time / progress) * 100) - running_time
    f.write(str(int(val)))
    f.close()

def reset_eta_status():
    f = file('/root/eta.txt','w')
    f.write(str(-1))
    f.close()
    f = file('/root/status.txt','w')
    f.write('idle')
    f.close()


def retrieve_file(remoteserver, filename):
    subprocess.check_output("wget -P " + filespath + " " + remoteserver + remotepath + filename,shell=True)

def deleteProcessedVideos(file1, file2):
    subprocess.check_output("rm " + filespath + file1 + " " + filespath + file2, shell=True)

def encodeFile(filename, startTime):
    encodedfilename = filename.split(".")[0] + ".mp4"

    c = Converter()
    # thumbnail 196x110
    options = {
        'format': 'mp4',
        'audio': {
            'codec': 'aac',
            'samplerate': 11025,
            'channels': 2
        },
        'video': {
            'codec': 'h264',
            'width': 720,
            'height': 400,
            'fps': 15
        },
        'map': 0
    }

    conv = c.convert(filespath + filename, filespath + encodedfilename, options)
    c.thumbnail(filespath + filename, 10, filespath + (encodedfilename.split(".")[0] + ".jpg"), '196x110')

    for timecode in conv:
        sys.stdout.write("\r%d%%" % timecode)
        sys.stdout.flush()
        sendStatusMessage('encoding', timecode)
        runningTime = time.time() - startTime
        update_eta(runningTime, timecode)
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


    job['startTime'] = time.time()
    encodedfile = encodeFile(job['filename'], job['startTime'])

    #deleteProcessedVideos(job['filename'], encodedfile)

    job['workerserver'] = getLocalIP()
    job['worker_ip'] = getPublicIP()
    job['oldFilename'] = job['filename']
    job['filename'] = encodedfile


    # remove old files

    #send a message back
    channel.basic_publish(exchange='',
                          routing_key='completedJobs'+job['fileserver'],
                          body=json.dumps(job))

    #publish work status
    reset_eta_status()




    print " [x] Done"
    ch.basic_ack(delivery_tag = method.delivery_tag)

channel.basic_qos(prefetch_count=1)
channel.basic_consume(callback,
                      queue='processJobs')

reset_eta_status()
channel.start_consuming()
