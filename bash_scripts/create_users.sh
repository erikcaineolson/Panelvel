#!/bin/bash

echo "Creating user ${1} with home directory ${2}!"
useradd $1 -d $2 -G www-data -p $3
