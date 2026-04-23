#!/usr/bin/env perl

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
