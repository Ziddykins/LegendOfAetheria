#!/usr/bin/env perl

use warnings;
use strict;

use Carp;

# CONFIG #
my $FQDN             = 'loa.dankaf.ca';
my $IP_ADDRESS       = '127.0.2.1';
my $LOG_TO_FILE      = '';

# Verbosity level:
#   - 0: Script errors/system call errors
#   - 1: Script output (default)
#   - 2: Script and system call output
#   - 3: All + errors to output file
#   - 4: All + all to output file
my $VERBOSITY = 1;

# NO TOUCH #
my $WIN32_HOSTS_FILE = 'c:\windows\system32\drivers\etc\hosts';
my $LINUX_HOSTS_FILE = '/etc/hosts';
my $GREEN = "\e[32m";
my $RED   = "\e[31m";
my $RESET = "\e[0m";
my $REDIR;

# lmao
#$REDIR = $VERBOSITY == 1
#            ? ''
#            : $VERBOSITY == 2
#                ? ''
#                : $VERBOSITY == 3
#                    ? ''
#                    : $VERBOSITY == 4
#                = ">/dev/null 2>&1";


check_platform();

if (get_answer("Update hosts file?")) {
    update_hosts();
}

if (get_answer("Install required software?")) {
    install_software();
}

if (!-e '/etc/debian_version' && check_platform() eq "linux") {
    croak "sry no :')";
}

sub check_platform {
    my $platform = $^O;

    if ($platform eq "MSwin32") {
        return "windows";
    } else {
        die "Unsupported OS!\n";
    }
    
    return "linux";
}

sub get_answer {
    my $question = $_[0];
    
    print $question;
    print "Choice[y/n]: ";
    
    chomp(my $answer = <STDIN>);
    print "\n";
    
    if ($answer =~ /[Yy]e?s?/) {
        return 1;
    }
    
    return 0;
}

sub update_hosts {
    my $fh;
    my ($sub, $domain) = split '.', $FQDN;
    
    if (check_platform() eq "linux") {
        open $fh, '+<', $LINUX_HOSTS_FILE
            or die "Unable to open file for rw: $!\n";
    } else {
        open $fh, '+<', $WIN32_HOSTS_FILE
            or die "Unable to open file for rw: $!\n";
    }
    
    my $contents = <$fh>;
    print $fh "$IP_ADDRESS\t$FQDN $sub.$domain";
    
    close $fh
        or die "Unable to open file for rw: $!\n";
}

sub install_software {
    if (get_answer("Update too?")) {
        print "Updating system packages\n";
        do_system('apt update');
    }
    my $packages = 'php8.2 php8.2-fpm php8.2-mysql libapache2-mod-php8.2 ';
    
}

sub do_system {
    my $command = $_[0];
    
    my $output = `$command`;
    
    if ($LOG_TO_FILE) {
        
    }
}