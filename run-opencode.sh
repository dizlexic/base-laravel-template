#!/bin/bash

# Configuration
PROMPT="${PROMPT:-"Continue to work on the project task board. Prioritise implementing / working tasks already in the in progress column. If there are no tasks in the in progress column, pick a task from the backlog and move it to in progress. Accept tasks in first in first out order. If there are no tasks, exit."}"
LOCK_FILE="/tmp/opencode.lock"
LIMIT=$1
COUNT=0

# Function to run opencode
run_opencode() {
    echo "Starting Opencode..."

    # Run opencode
    opencode run "$PROMPT"
}

# The requirement "should launch a new instance on completion" suggests a loop.
while true; do
    if [ -n "$LIMIT" ] && [ "$COUNT" -ge "$LIMIT" ]; then
        echo "Reached iteration limit $LIMIT. Exiting."
        exit 0
    fi
    
    # Run junie with flock to ensure only one instance
    (
        flock -n 200 || { echo "Opencode is already running"; exit 0; }
        
        run_opencode
        
        echo "Opencode finished. Restarting..."
    ) 200>"$LOCK_FILE"
    
    COUNT=$((COUNT+1))
    
    sleep 5
done
