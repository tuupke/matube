__author__ = 'max'
import peewee
from peewee import *  #MySQL ORM
from MatubeEmail import *
import subprocess

#Connect to MYSQL
db = MySQLDatabase("videos",
                 user="root",
                  passwd="fileserver")
IP = subprocess.check_output("curl ifconfig.me/ip", shell=True)
PATH_TO_FILES = "/videos/"


class FileServer:
    def __init__(self):
        pass

    class PendingVideo(peewee.Model):

        filename = peewee.TextField()
        email = peewee.TextField()

        class Meta:
            database = db

    class CompletedVideo(peewee.Model):
        filename = peewee.TextField()
        email = peewee.TextField()

        class Meta:
            database = db

    def newJob(self, xFilename, xEmail):
        """Put a new (filename, email) pair in the database and spawn a new job to the MQ.
        Assumes that the filename has been sanitized and made unique.
        Assumes that the email has been sanitized and is valid.
        """

        job = self.PendingVideo(filename= xFilename, email=xEmail)
        job.save()  # the data is saved in the db

        self.sendJobToMQ(xFilename)

    def sendJobToMQ(self, xFilename):
        """
        New jobs are sent to the Message Queue to be given to a worker server to complete.
        :param newFilename:
        :return:
        """
        channel.basic_publish(exchange='',
                              routing_key='newJob',
                              body=xFilename)

    def completedJob(self, xFilename):
        """
        The message from the MQ has been taken.
        The objective here it remove the job from the
        pending DB Table, put it in the completed DB table
        and send an email to the user to retrieve the file.
        :param xFilename:
        :return:
        """
        pendingRecord = self.PendingVideo.select().where(self.PendingVideo.filename == xFilename).get()

        downloadURL = self.getURLToFile(pendingRecord.filename)

        MatubeEmail(pendingRecord.email, downloadURL)

        pendingRecord.delete_instance()

    def getURLToFile(self, filename):
        """
        construct the link to the file
        :param filename:
        :return:
        """
        return IP + PATH_TO_FILES + filename