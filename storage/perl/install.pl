#!/usr/bin/perl

use strict;
use warnings;
use DBI;
use String::Random;

my $in_file;
my $storage_dir;

my @lines;
my $line;

my $domain;
my $user;
my $pass;
my $is_wp;
my $site_type;

my $wp_db = '';
my $wp_db_user = '';
my $wp_db_pass = '';

if (@ARGV && $ARGV[0] ne = '') {
    $in_file = $ARGV[0];
    $storage_dir = $ARGV[1];

    open(USER_LIST, '<', $in_file);
    @lines = <USER_LIST>;
    close(USER_LIST);

    # break apart the flat file and then build the directories and users
    foreach $line (@lines)
    {
        # break up the line into components
        ($domain, $user, $pass, $is_wp, $wp_db, $wp_db_user, $wp_db_pass) = split(':', $line);

        if($is_wp == 1 || $is_wp eq '1'){
            $site_type = 'wordpress';
        }else{
            $site_type = 'secure_site';
        }

        system($storage_dir . '/bash/new_site.sh', $domain, $site_type, $pass, $wp_db, $wp_db_pass);
    }
} else {
    die( "\nMissing parameters\ninstall.pl /path/to/website/list" );
}
