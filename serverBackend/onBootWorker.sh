git -C /usr/share/nginx/matube/ pull
screen -d -m python /usr/share/nginx/matube/serverBackend/health-server.py
screen -d -m python /usr/share/nginx/matube/serverBackend/workerServerListener.py
echo " Started health-server and server listener"
