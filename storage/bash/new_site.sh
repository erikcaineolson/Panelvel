#!/bin/bash

if [ $# -lt 3 ]
    then
        echo "Missing one or more arguments:
                ./new_site.sh domain-name.com /path/to/site [site|secure_site|wordpress|wp_stub] [db_name] [db_password] [db_host] [subdirectory]"
        exit 1
fi

# store the database and password info
DB_PASSWORD=${5:-0} # set a default zero value if none exists
DB_HOST=${6:-0}

DB_NAME=${4:-0}
DB_NAME+="_wp";

SUB_DIRECTORY=${7:""}

# no matter anything else, we'll need this directory
mkdir -p $2

if [ $3 = "wordpress" or $3 = "wp_stub" ]
    then
        # download a fresh copy of wordpress, gunzip and untar it
        wget https://wordpress.org/latest.tar.gz
        sleep 10
        # clean up the WP install (move files out of /wordpress and get rid of the /wordpress folder)
        tar -xf latest.tar.gz --directory=$2 #$2/latest.tar.gz -C $2/
        mv $2/wordpress/* $2/
        rmdir $2/wordpress
        rm -f latest.tar.gz
        # make replacements in wordpress config file
        sed -i "s@database_name_here@$DB_NAME@g" $2/wp-config-sample.php
        sed -i "s@username_here@$DB_NAME@g" $2/wp-config-sample.php
        sed -i "s@password_here@$DB_PASSWORD@g" $2/wp-config-sample.php
        mv $2/wp-config-sample.php $2/wp-config.php

        # append a direct fix so we can auto-update without FTP access (there's no default FTP setup in fresh config files)
        echo "" >> $2/wp-config.php
        echo "/** Sets up 'direct' method for WordPress, auto-update without FTP **/" >> $2/wp-config.php
        echo "define('FS_METHOD', 'direct');" >> $2/wp-config.php

        # add a blank space for good measure (and ease of reading when cat'ing the file)
        echo "" >> $2/wp-config.php
fi

if [ $3 != "wp_stub" ]
    then
        # copy proper nginx config file, and replace values
        cp ../templates/$3.conf /etc/nginx/sites-available/$1
        sed -i "s@$2@$1@g" /etc/nginx/sites-available/$1

        # create symbolic link
        ln -s /etc/nginx/sites-available/$1 /etc/nginx/sites-enabled
else
    # if the config file already exists, append a copy of the WP-stub file
    if [ -e /etc/nginx/snippets/sites/$3.conf ]
        then
            cat /etc/nginx/snippets/sites/$3.conf ../templates/$3.conf > /etc/nginx/snippets/sites/$3.conf
    else
        cp ../templates/$3 /etc/nginx/snippets/sites/$3.conf
    fi

    # replace the appropriate values in the config file
    sed -i "s@%%SUBDIRECTORY%%@$SUB_DIRECTORY@g" /etc/nginx/snippets/sites/$3.conf
fi

# TODO: use letsencrypt to make all sites secure

# change ownership and restart NGINX
chown www-data:www-data -R $2
service nginx restart
