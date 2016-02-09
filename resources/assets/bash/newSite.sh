#!/bin/bash

if [ $# -lt 2 ]
    then
        echo "Missing one or more arguments:
		./newSite domainname.com site|wordpress|secure"
	exit 1
fi

# generate a password
PASSWORD=`date | md5sum | head -c 12`
DB_PASSWORD=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | head -c 20)

CLEAN_NAME=$(echo $1 | sed 's@[^a-zA-Z0-9]*@@g')
DB_NAME=`expr substr $CLEAN_NAME 1 8`
DB_NAME+="_wp";

# build directory
mkdir -p /var/www/$1/public_html
cp -rf /var/www/$2/* /var/www/$1/public_html

if [ $2 = "wordpress" ]
	then
		sed -i "s@wp_db_name@$DB_NAME@g" /var/www/$1/public_html/wp-config.php
		sed -i "s@wp_db_user@$DB_NAME@g" /var/www/$1/public_html/wp-config.php
		sed -i "s@wp_db_pass@$DB_PASSWORD@g" /var/www/$1/public_html/wp-config.php
		sed -i "s@wp_ftp_user@$1@g" /var/www/$1/public_html/wp-config.php
		sed -i "s@wp_ftp_pass@$PASSWORD@g" /var/www/$1/public_html/wp-config.php
fi

# copy proper nginx config file, and replace values
cp /etc/nginx/sites-available/$2 /etc/nginx/sites-available/$1
sed -i "s@$2@$1@g" /etc/nginx/sites-available/$1

# create symbolic link
ln -s /etc/nginx/sites-available/$1 /etc/nginx/sites-enabled

# check for secure
if [ $2 = "secure" ]
    then
	cp /etc/nginx/snippets/ssl-snakeoil.conf /etc/nginx/snippets/ssl-$1.conf
	sed -i "s@snakeoil@$1@" /etc/nginx/snippets/ssl-$1.conf
fi

# create ftp user
useradd $1 -d /var/www/$1
usermod -a -G www-data $1

# set ftp user password
echo $1:$PASSWORD | chpasswd

# change ownership and restart nginx
/var/www/scripts/ch

# print FTP info
echo "Host: 52.91.24.198
Username: $1
Password: $PASSWORD
Path: /var/www/$1
"

if [ $2 = "wordpress" ]
    then
 	echo "
Creating database...
"
#Please create database
#
#DB Name: $DB_NAME
#DB User: $DB_NAME
#DB Pass: $DB_PASSWORD
#"

	echo "
CREATE DATABASE ${DB_NAME};
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_NAME}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
" > /tmp/${DB_NAME}.sql

	mysql -u newsite -pcTvPL7Py7A3GzGVPzxc8fLRH < /tmp/${DB_NAME}.sql
fi
echo "Please set DNS records at CloudFlare to point to 52.91.24.198

"

