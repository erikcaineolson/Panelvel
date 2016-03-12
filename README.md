# Panelvel

Lightweight (and *very* basic) control panel for NGINX servers. Developed primarily in Laravel 5.2, with a little support from Python, Perl, and bash!

## Requirements

 * PHP 5.5.9+
 * MySQL 5.5+
 * Perl 5+ 
 * Python 2.7+ (untested on Python 3)
 * Bash shell (or feel free to fork and port!)
 * Composer (https://getcomposer.org/download)
 * NGINX web server (otherwise, why?)
 
 1. Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).
 2. Documentation for Composer can be found on the [Composer website](https://getcomposer.org/doc/).
 3. If you need help installing NGINX and PHP on your server, [Digital Ocean](https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-on-debian-7) has a great tutorial (this works on Debian 8 as well).

## Contributing

Thank you for considering contributing to my little project. If it helps you out, please let me know...if I can do something better...please let me know!

## License

Panelvel is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT) (also included in this repository).

## Installing

 1. Install composer...globally, if you can (see above)
 2. Clone this repo into your web directory (I recommend setting a sub-domain or domain aside for this), and run
    `composer install`
 3. Set ownership of all directories under Panelvel to www-data
 4. Create a new database to store the site information, and update the /.env file with your database login information (you may want to use a separate server for WP instances)
 5. Add the following lines to your crontab (replace /YOUR-PATH/ with the path to the repo on your server; this will run every 5 minutes):
    `*/5 * * * * perl /YOUR-PATH/storage/install.pl /YOUR-PATH/storage/sites/list.txt /YOUR-PATH/Panelvel/storage >/dev/null 2>&1`
    (you may edit your crontab with `crontab -e`)
 6. Change the username and password in the /database/seeds/UsersTableSeeder.php file to something you'll remember (or your login will be the _very_ unsafe "admin@admin.adm"/"password")  
 7. Run the migrations
    `php artisan migrate --seed`
 8. Delete your login information from the /database/seeds/UsersTableSeeder.php file
 9. Use it, fork it, improve it!
 
## How to Use
 1. Log into your site (http://yourdomain.tld/login)
 2. Add a domain
 3. Check the box for "WordPress" if you want it
 4. Let the server work its magic...it can take up to 5 minutes to update a site (less if you decrease the value in the crontab)

## How it Works
 1. You enter your information in the admin section
 2. If you marked WordPress, Laravel generates the new database login information (for the as-of-yet-uncreated WordPress database; the server does not have to be the same as Panelvel connects to) 
 3. Eloquent updates the Laravel-connected database
 3. Laravel calls an Artisan command to generate text files from the database contents
 4. The Perl script at `/storage/perl/install.pl` parses the text file
    1. install.pl executes a bash script, `/storage/bash/new_site.sh`, which creates the proper file and directory structures (along with the appropriate config files) for the NGINX server
    2. If the site is flagged WordPress, new_site.sh downloads a fresh copy of WordPress, installs it in the proper directory, writes the database login information into wp-sample-config.php, moves the file to wp-config.php, and appends the "direct" FS_METHOD to WordPress (enables updates without FTP)
    3. new_site.sh copies self-signed certificates into the proper places (if you're not using CloudFlare, you'll want to remove this, and possibly modify the templates in /storage/templates)
    4. new_site.sh changes ownership of all the web files and restarts NGINX
 5. If the WordPress flag is set, install.pl:
    1. calls `/storage/python/create_db.py` to generate the new WordPress database
    2. generates new nonces and edits the wp-config.php file accordingly
