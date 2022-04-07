#!/bin/bash

SCRIPT_PATH="`dirname \"$0\"`"

LOG_FILE="$SCRIPT_PATH/../application/log/wwsimport/cron_wws_sync.`date +\%Y\%m\%d_\%H\%M\%S`.log"

echo "Start WWS-Import"
echo "Log output to $LOG_FILE"

curl -i http://10.30.2.130:1088/system/wwsdirektimport | tee > "$LOG_FILE" 2>&1 

echo "Finished"

