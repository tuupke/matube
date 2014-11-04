git -C /root/matube/ pull
screen -d -m python /root/matube/serverBackend/fileserverWebListener.py
screen -d -m python /root/matube/serverBackend/fileserverCompletedJobListener.py
