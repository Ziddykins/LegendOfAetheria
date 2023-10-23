#!/usr/bin/env perl

use warnings;
use strict;

use Carp;

# CONFIG #
my $FQDN             = 'loa.dankaf.ca';
my $IP_ADDRESS       = '127.0.2.1';
my $LOG_TO_FILE      = '';

# NO TOUCH #
my $WIN32_HOSTS_FILE = 'c:\windows\system32\drivers\etc\hosts';
my $LINUX_HOSTS_FILE = '/etc/hosts';

my $XAMPP_INSTALLER_BIN  = 'https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.4/xampp-windows-x64-8.2.4-0-VS16-installer.exe';
my $XAMPP_INSTALLER_ARGS = '--mode unattended --enabled-components xampp_server,xampp_apache,xampp_mysql,xampp_program_languages,xampp_php,xampp_perl,xampp_tools
my $XAMPP_MARIADB_CHPW   = 'mysqladmin.exe -u root password
'
my $BLUE   = "\e[34m";
my $YELLOW = "\e[33m";
my $GREEN  = "\e[32m";
my $RED    = "\e[31m";
my $RESET  =  "\e[0m";

my @steps = qw/hosts software hostname apache php sql/;

check_platform();

if (ask_user("Update hosts file?")) {
    update_hosts();
}

if (ask_user("Install required software?")) {
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

sub ask_user {
    my $question = $_[0];
    
    print $question;
    print "Choice[${GREEN}y${RESET}/${RED}n${RESET}]: ";
    
    chomp(my $answer = <STDIN>);
    print "\n";
    
    if ($answer =~ /[Yy]e?s?/) {
        return 1;
    }
    
    return 0;
}

sub tell_user {
    my ($severity, $message, $result) = @_;
    my ($sec, $min, $hour, $day, $mon, $year) = localtime();
    my $date  = "$year-$mon-$day $hour:$min:$sec";
    my prefix = "$date [";
    
    if ($severity eq 'INFO') {
        $prefix .= $BLUE . "=";
    } elsif ($severity eq 'WARN') {
        $prefix .= $YELLOW . "÷";
    } elsif ($severity eq 'ERROR') {
        $prefix .= $RED . "-";
    } elsif ($severity eq 'SUCCESS') {
        $prefix .= $GREEN . "+";
    } else {
        print "$message\n";
    }
    
    $prefix .= "$RESET] ";
    
    print "$prefix -> $message\n";
    
    if ($result) {
        $result =~ s/[\r\n]/\n\t=> /g;
        print $result;
    }
    
    if ($LOG_TO_FILE && -e $LOG_TO_FILE) {
        open my $fh, '>>', $LOG_TO_FILE
            or die "Couldn't open specified log file '$LOG_TO_FILE': $!";
        print $fh $result;
    }
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
    
    print $fh "\n$IP_ADDRESS\t$FQDN $sub.$domain";
    
    close $fh
        or die "Unable to open file for rw: $!\n";
}

sub install_software {
    if (ask_user("Update too?")) {
        tell_user('INFO', 'Updating system packages';
        do_system('apt update');
    }
    my $packages = 'php8.2 php8.2-fpm php8.2-mysql libapache2-mod-php8.2 ';
    
}