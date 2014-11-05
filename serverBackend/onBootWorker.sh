git -C /root/matube/ pull
screen -d -m python /root/matube/serverBackend/health-server.py
screen -d -m python /root/matube/serverBackend/workerServerListener.py
echo " Started health-server and server listener"
