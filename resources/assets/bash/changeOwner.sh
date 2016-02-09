#!/bin/bash

chown -R www-data:www-data /var/www/*
chmod -R 775 /var/www/*
service nginx restart
