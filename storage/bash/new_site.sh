#!/bin/bash

if [ $# -lt 3 ]
    then
        echo "Missing one or more arguments:
                ./new_site.sh domainname.com site|wordpress password [db_name] [db_password]"
        exit 1
fi

# store the database and password info
PASSWORD=$3
DB_PASSWORD=${5:-0} # set a default zero value if none exists

DB_NAME=${4:-0}
DB_NAME+="_wp";

if [ $2 = "wordpress" ]
    then
        # download a fresh copy of wordpress, gunzip and untar it
        wget https://wordpress.org/latest.tar.gz
        sleep 10
        # clean up the WP install (move files out of /wordpress and get rid of the /wordpress folder)
        tar -xf latest.tar.gz #/var/www/$1/public_html/latest.tar.gz -C /var/www/$1/public_html/
        rm -f latest.tar.gz
        mv wordpress/* /var/www/$1/public_html/
        rmdir wordpress

        # make replacements in wordpress config file
        sed -i "s@wp_db_name@$DB_NAME@g" /var/www/$1/public_html/wp-config-sample.php
        sed -i "s@wp_db_user@$DB_NAME@g" /var/www/$1/public_html/wp-config-sample.php
        sed -i "s@wp_db_pass@$DB_PASSWORD@g" /var/www/$1/public_html/wp-config-sample.php
        sed -i "s@wp_ftp_user@$1@g" /var/www/$1/public_html/wp-config-sample.php
        sed -i "s@wp_ftp_pass@$PASSWORD@g" /var/www/$1/public_html/wp-config-sample.php
        mv /var/www/$1/public_html/wp-config-sample.php /var/www/$1/public_html/wp-config.php
fi

# copy proper php config file and replace values
cp /etc/nginx/templates/php /etc/php5/fpm/pool.d/$1.conf
sed -i "s@SITE_NAME@$1@g" /etc/php5/fpm/pool.d/$1.conf

# copy proper nginx config file, and replace values
cp /etc/nginx/templates/$2 /etc/nginx/sites-available/$1
sed -i "s@$2@$1@g" /etc/nginx/sites-available/$1

# create symbolic link
ln -s /etc/nginx/sites-available/$1 /etc/nginx/sites-enabled

# make all sites secure
cp /etc/nginx/snippets/ssl-snakeoil.conf /etc/nginx/snippets/ssl-$1.conf
sed -i "s@snakeoil@$1@" /etc/nginx/snippets/ssl-$1.conf

# change ownership and restart nginx and php
/var/www/ch
service nginx restart
service php5-fpm restart