#!/usr/bin/env perl
#===============================================================================
#
#         FILE: table_explore.pl
#
#        USAGE: ./table_explore.pl  
#
#  DESCRIPTION: 
#
#      OPTIONS: ---
# REQUIREMENTS: ---
#         BUGS: ---
#        NOTES: ---
#       AUTHOR: YOUR NAME (), 
# ORGANIZATION: 
#      VERSION: 1.0
#      CREATED: 12/01/23 06:50:50
#     REVISION: ---
#===============================================================================

use warnings;
use strict;
use utf8;

my @tables = `mysql -e "use db_loa; show tables;" | grep -v Tables`;

my $html = "";
foreach my $table (@tables) {
    chomp($table);
    $html .= "<a href=\"$table.html\">$table</a> - ";
    `echo '<pre>' > $table.html`;
    `mysql -t -e 'use db_loa; DESCRIBE $table;' >> $table.html`;
}
$html =~ s/...$/<br>/;

print $html;
