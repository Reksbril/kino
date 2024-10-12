#/bin/bash

INSTANCE_IP=130.61.155.225
KEY_LOCATION="oracle_cloud.key"

HOST="ubuntu@$INSTANCE_IP"
REMOTE_HOME_DIR="/home/ubuntu/"

# Copy php.ini
scp -i $KEY_LOCATION /home/mateusz/projects/kino/php.ini $HOST:$REMOTE_HOME_DIR

# Copy project
scp -r -i $KEY_LOCATION /home/mateusz/projects/kino/src $HOST:$REMOTE_HOME_DIR

# Set up project and requirements
ssh -i $KEY_LOCATION $HOST 'bash -s' < oracle_cloud_init_script.sh