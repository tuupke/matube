#!/bin/bash
git -C /root/matube/ pull
screen -S root -d -m python /root/matube/serverBackend/health-server.py
screen -S root -d -m python /root/matube/serverBackend/workerServerListener.py
echo " Started health-server and server listener"
