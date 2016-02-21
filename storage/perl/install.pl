#!/usr/bin/perl

use strict;
use warnings;
use String::Random;

my $in_file;
my $storage_dir;

my @lines;
my $line;

my @config_lines;
my $config_line;
my $random_string;

my $domain;
my $is_wp;
my $site_type;

my $wp_db;
my $wp_db_host;
my $wp_db_user;
my $wp_db_pass;

if (@ARGV && $ARGV[0] ne '' && $ARGV[1] ne '') {
    $in_file = $ARGV[0];
    $storage_dir = $ARGV[1];

    open(USER_LIST, '<', $in_file);
    @lines = <USER_LIST>;
    close(USER_LIST);

    # break apart the flat file and then build the directories and users
    foreach $line (@lines)
    {
        # break up the line into components
        ($domain, $is_wp, $wp_db, $wp_db_user, $wp_db_pass, $wp_db_host) = split(':', $line);

        if($domain ne ''){
            if($is_wp == 1 || $is_wp eq '1'){
                $site_type = 'wordpress';
            }else{
                $site_type = 'secure_site';
            }

            # build the database
            system('touch', $storage_dir . '/sql/' . $wp_db . '.sql');
            system($storage_dir . '/bash/new_site.sh', $domain, $site_type, $wp_db, $wp_db_pass, $wp_db_host);

            # change the keys and the localhost in the wordpress config file, since bash isn't playing nice with that
            if($site_type eq 'wordpress'){
                system('python', $storage_dir . '/python/create_db.py', $wp_db, $wp_db_user, $wp_db_pass, $wp_db_host);

                open(WP_CONFIG_IN, '<', "/var/www/$domain/public_html/wp-config.php");
                @config_lines = <WP_CONFIG_IN>;
                close(WP_CONFIG_IN);

                open(WP_CONFIG, '>', "/var/www/$domain/public_html/wp-config.php");
                foreach $config_line (@config_lines)
                {
                    $random_string = new String::Random();
                    my $temp_rand_str = $random_string->randpattern("ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss");
                    my $temp_db_host = s/\n/$wp_db_host/;

                    $config_line =~ s/localhost/$temp_db_host/g;
                    $config_line =~ s/put your unique phrase here/$temp_rand_str/;

                    print WP_CONFIG $config_line;
                }
                close(WP_CONFIG);
            }
        }
        system('rm', $in_file);
    }
} else {
    die( "\nMissing parameters\ninstall.pl /path/to/website/list /path/to/Panelvel/storage" );
}
