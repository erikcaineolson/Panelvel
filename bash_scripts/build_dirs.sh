#!/bin/bash

echo "Building directories..."

# pass the directories to create as the parameters
for var in "$@"
do
    mkdir -p "${var//\;}"
done
