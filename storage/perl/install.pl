#!/usr/bin/perl

use strict;
use warnings;

my $in_file;
my $web_dir;
my @lines;
my $line;

my $domain;
my $user;
my $pass;
my $is_wp;

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
        ($domain, $user, $pass, $is_wp) = split(':', $line);

        # create the user
        system('useradd'  . $user . ' -h ' . $web_dir . '-G www-data -p ' . $pass);

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

        # restart NGINX and PHP
        system('service nginx restart');
        system('service php5-fpm restart');
    }
} else {
    die( "\nMissing parameters\n" );
}
