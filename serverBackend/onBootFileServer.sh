git -C /usr/share/nginx/matube/ pull
screen -d -m python /usr/share/nginx/matube/serverBackend/fileserverWebListener.py
screen -d -m python /usr/share/nginx/matube/serverBackend/fileserverCompletedJobListener.py
screen -d -m python /usr/share/nginx/matube/serverBackend/health-server.py
