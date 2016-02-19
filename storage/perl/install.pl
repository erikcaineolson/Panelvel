#!/usr/bin/perl

use strict;
use warnings;
use DBI;
use String::Random;

my $in_file;
my $web_dir;
my @lines;
my $line;
my @nginx_lines;
my $nginx_line;
my @php_lines;
my $php_line;
my @wp_lines;
my $wp_line;

my $domain;
my $user;
my $pass;
my $is_wp;

my $random_pattern;
my $random_string;

my $wp_db = '';
my $wp_db_user = '';
my $wp_db_pass = '';

my $wp_source = 'https://wordpress.org/latest.tar.gz';

my $nginx_config_file;
my $php_config_file;

if (@ARGV && $ARGV[0] ne '' && $ARGV[1] ne '') {
    $in_file = $ARGV[0];
    $web_dir = $ARGV[1];

    open(USER_LIST, '<', $in_file);
    @lines = <USER_LIST>;
    close(USER_LIST);

    # break apart the flat file and then build the directories and users
    foreach $line (@lines)
    {
        # break up the line into components
        ($domain, $user, $pass, $is_wp, $wp_db, $wp_db_user, $wp_db_pass) = split(':', $line);

        # create the user
        system('useradd'  . $user . ' -h ' . $web_dir . '/' . $user . ' -G www-data -p ' . $pass);

        # create the user's SFTP directory
        system('mkdir -p /srv/sftp/' . $user . '/web');

        # mount the user's home directory to the SFTP/user/web directory
        system('mount --bind /srv/sftp/' . $user . '/web ' . $web_dir);

        # edit the config files
        open(PHP_CONFIG, '+<', '../sites/php/' . $domain . '.conf');
        @php_lines = <PHP_CONFIG>;
        foreach $php_line (@php_lines)
        {
            $php_line =~ s/SITE_NAME/\$domain/g;
        }
        print PHP_CONFIG @php_lines;
        close(PHP_CONFIG);

        open(NGINX_CONFIG, '+<', '../sites/nginx/' . $domain);
        @nginx_lines = <NGINX_CONFIG>;
        foreach $nginx_line (@nginx_lines)
        {
            $nginx_line =~ s/SITE_NAME/\$domain/g;
        }
        print NGINX_CONFIG @nginx_lines;
        close(NGINX_CONFIG);

        # move the config files
        system('mv ../sites/nginx/' . $domain . ' /etc/nginx/sites-available/');
        system('mv ../sites/php/' . $domain . '.conf /etc/php5/fpm/pool.d/');

        # "enable" the site
        system('ln -s /etc/nginx/sites-available/' . $domain . ' /etc/nginx/sites-enabled/');

        # create the domain directories
        system('mkdir', '-p', $web_dir . '/' . $domain . '/public_html');
        system('mkdir', '-p', $web_dir . '/' . $domain . '/logs');
        system('touch', $web_dir . '/' . $domain . '/logs/access.log');
        system('touch', $web_dir . '/' . $domain . '/logs/error.log');

        # if it's wordpress, install wordpress
        if($is_wp == 1){
            system('wget ' . $wp_source . ' -O ' . $web_dir . '/' . $user . '/public_html/latest.tar.gz');
            system('tar -xf ' . $web_dir . '/' . $user . '/public_html/latest.tar.gz');
            system('mv ' . $web_dir . '/' . $user . '/public_html/latest.tar.gz/* ../');
            system('rmdir ' . $web_dir . '/' . $user . '/public_html/wordpress');

            open(WP_CONFIG_SAMPLE, '<', $web_dir . '/' . $user . '/public_html/wordpress/wp-config-sample.php');
            open(WP_CONFIG, '>', $web_dir . '/' . $user . '/public_html/wordpress/wp-config.php');
            @wp_lines = <WP_CONFIG_SAMPLE>;

            foreach $wp_line (@wp_lines)
            {
                $random_pattern = new String::Random();
                $random_string = $random_pattern->randpattern("................................................................");

                $wp_line =~ s/database_name_here/\$wp_db/;
                $wp_line =~ s/username_here/\$wp_db_user/;
                $wp_line =~ s/password_here/\$wp_db_pass/;
                $wp_line =~ s/put your unique phrase here/\$random_string/;

                print WP_CONFIG $wp_line;
            }

            close(WP_CONFIG);
            close(WP_CONFIG_SAMPLE);
        }

        # change ownership
        system('chown www-data:www-data ' . $web_dir . '/' . $user);

        # restart NGINX and PHP
        system('service nginx restart');
        system('service php5-fpm restart');
    }
} else {
    die( "\nMissing parameters\n" );
}
