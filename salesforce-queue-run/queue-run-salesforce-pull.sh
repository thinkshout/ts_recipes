#!/bin/bash

# Run through the salesforce_pull queue ad infinitum. Ctrl-C to stop.
#
# Usage:
# $ ./queue-run-salesforce-pull.sh @pantheon.site.live >> queue-run-salesforce-pull.log

if [ -z "$1" ]; then
  echo "Missing drush site alias."
  exit
fi

while true
do
  date
  drush $1 queue-run salesforce_pull --time-limit=30
  sleep 10
done

