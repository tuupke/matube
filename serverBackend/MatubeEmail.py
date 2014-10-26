__author__ = 'max'
import os
# Import smtplib for the actual sending function
import smtplib

# Import the email modules we'll need
from email.mime.text import MIMEText

class MatubeEmail:
    def __init__(self, email, url):
        server = smtplib.SMTP('mail.gandi.net', 587)

        #Next, log in to the server
        server.login("server@sumrall.nl", "gewiseeeeeeeeeeeeeeeeeeee")
        header  = 'From: %s\n' % "server@sumrall.nl"
        header += 'To: ' + email
        header += 'Subject: %s\n\n' % "Video file ready to download"
        message = header + "\nHello! Your video has been processed and is ready to download at the link below. Thanks " + email + "\n " + url

        #Send the mail
        server.sendmail("server@sumrall.nl", email, message)


