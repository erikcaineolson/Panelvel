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
my $is_wp;
my $site_type;

my $dsn;
my $dbh;
my $drh;


my $wp_db;
my $wp_db_host;
my $wp_db_user;
my $wp_db_pass;
my $wp_db_user_string;

if (@ARGV && $ARGV[0] ne '' && $ARGV[1] ne '') {
    $in_file = $ARGV[0];
    $storage_dir = $ARGV[1];

    open(USER_LIST, '<', $in_file);
    @lines = <USER_LIST>;
    close(USER_LIST);

    # prep and connect to the database
    $drh = DBI->install_driver("mysql");
    $dsn = "DBI:mysql:database=wpaccounts;host=adnomiclps.cscc7jbgnayn.us-east-1.rds.amazonaws.com;port=3306";
    $dbh = DBI->connect($dsn, 'wpbuilder', 'vsCVn9Ue2LPHu6nvLCaU2j7T');

    # break apart the flat file and then build the directories and users
    foreach $line (@lines)
    {
        # break up the line into components
        ($domain, $is_wp, $wp_db, $wp_db_user, $wp_db_pass, $wp_db_host) = split(':', $line);

        if($is_wp == 1 || $is_wp eq '1'){
            $site_type = 'wordpress';
        }else{
            $site_type = 'secure_site';
        }

        #$rc = $dbh->func('createdb', $database, $wp_db_host, $wp_db_user, $wp_db_pass, $wp_db);
        $wp_db_user_string = "'$wp_db_user'\@'%'";
        $dbh->do("CREATE DATABASE $wp_db");
        $dbh->do("GRANT ALL PRIVILEGES ON $wp_db.* TO ? IDENTIFIED BY ?", $wp_db_user_string, $wp_db_pass);

        system($storage_dir . '/bash/new_site.sh', $domain, $site_type, $wp_db, $wp_db_pass);
    }

    $dbh->disconnect();
} else {
    die( "\nMissing parameters\ninstall.pl /path/to/website/list /path/to/Panelvel/storage" );
}
