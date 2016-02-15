#!/bin/bash

cd $1
wget https://wordpress.org/latest.tar.gz
tar -xf latest.tar.gz
cd wordpress
mv * ../
cd ../
rmdir wordpress

exit 0
