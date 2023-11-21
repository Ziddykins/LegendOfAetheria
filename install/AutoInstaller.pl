#!/usr/bin/env perl

use warnings;
use strict;

use Carp;
# CONFIG - Server #
my $FQDN             = 'loa.dankaf.ca';
my ($SUB, $DOM)      = split '.', $FQDN;
my $IP_ADDRESS       = '127.0.2.1';
my $LOG_TO_FILE      = '';
my $PHP_BINARY       = $1 if `whereis php` =~ /php: (\/?.*?\/bin\/.*?\/?php\d?\.?\d?)/;
my $GAME_WEB_ROOT    = "/var/www/html/$DOM/$SUB";
my $WEB_ADMIN_EMAIL  = "webmaster\@$DOM";

# CONFIG - SQL Tables #
my $TBL_CHARACTERS = 'tbl_characters';
my $TBL_FAMILIARS  = 'tbl_familiars';
my $TBL_ACCOUNTS   = 'tbl_accounts';
my $TBL_FRIENDS    = 'tbl_friends';
my $TBL_GLOBALS    = 'tbl_globals';
my $TBL_MAIL       = 'tbl_mail';
my $TBL_CHATS      = 'tbl_chats';

# CONFIG - Apache / XAMPP #
my $SSL_ENABLED = 1;
my $SSL_FULLCER = '/etc/letsencrypt/live/loa.dankaf.ca/fullchain.pem';
my $SSL_PRIVKEY = '/etc/letsencrypt/live/loa.dankaf.ca/privkey.pem';

# 1 - All hosts in apache2.conf [ Not Recommended ]
# 2 - Separate in its own .conf file, under sites-available
my $VIRTHOST_LOCATION = 2;
my $VIRTHOST_CONF_FILE = "/etc/apache2/sites-available/$FQDN.conf";

my $XAMPP_INSTALLER_BIN  = 'https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.4/xampp-windows-x64-8.2.4-0-VS16-installer.exe';
my $XAMPP_INSTALLER_ARGS = '--mode unattended --enabled-components xampp_server,xampp_apache,xampp_mysql,xampp_program_languages,xampp_php,xampp_perl,xampp_tools';
my $XAMPP_MARIADB_CHPW   = 'mysqladmin.exe -u root password';

# NOCONFIG - Hosts files
my $WIN32_HOSTS_FILE = 'c:\windows\system32\drivers\etc\hosts';
my $LINUX_HOSTS_FILE = '/etc/hosts';

# NOCONFIG - Colors
my $RED    = "\e[31m";
my $GREEN  = "\e[32m";
my $YELLOW = "\e[33m";
my $BLUE   = "\e[34m";
my $CYAN   = "\e[36m";
my $GREY   = "\e[37m";
my $RESET  =  "\e[0m";

my @steps = qw/hosts software hostname apache certificate php composer env sqlgen sqlimport crons/;

check_platform();

if (ask_user("Update hosts file?")) {
    update_hosts();
}

if (ask_user("Install required software?")) {
    install_software();
}

if (ask_user("Update system hostname to match FQDN?")) {
    update_hostname();
}

if (!-e '/etc/debian_version' && check_platform() eq "linux") {
    croak "sry no :')";
}

sub check_platform {
    my $platform = $^O;

    if ($platform eq "MSwin32") {
        return "windows";
    } elsif ($platform eq "linux") {
        return "linux";
    }
    die "Unsupported OS!\n";
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
    my $prefix = "$date [";
    
    if ($severity eq 'INFO') {
        $prefix .= $BLUE . '?';
    } elsif ($severity eq 'WARN') {
        $prefix .= $YELLOW . '!';
    } elsif ($severity eq 'ERROR') {
        $prefix .= $RED . '-';
    } elsif ($severity eq 'SUCCESS') {
        $prefix .= $GREEN . '+';
    } elsif ($severity eq 'SYSTEM') {
        $message =~ s/[\r\n]/\n\t\t/g;
        $prefix .= $GREY . '*';
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
            or die "Couldn't open specified log file for append '$LOG_TO_FILE': $!";
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
    
    if ($contents =~ /$FQDN/) {
        tell_user('WARN', 'Looks like the hosts file entry is already there');
    
        if (!ask_user('Add it anyway?')) {
            close $fh
                or die "Unable to open file for rw: $!\n";
            return;
        }
    }

    print $fh "\n$IP_ADDRESS\t$FQDN $sub.$domain";
    tell_user('SUCCESS', 'Hosts entry added');
 
    close $fh
        or die "Unable to open file for rw: $!\n";
}

sub install_software {
    my $apt_output;
    
    if (ask_user("Update too?")) {
        tell_user('INFO', 'Updating system packages');
        $apt_output = `apt update 2>&1`;
        tell_user('SYSTEM', $apt_output);
    }
    my $packages   = 'php8.2 php8.2-fpm php8.2-mysql libapache2-mod-php8.2 composer';
    
    $apt_output = `apt install -y $packages 2>&1`;
    tell_user('SYSTEM', $apt_output);
}

sub update_hostname {
    my $output = `hostnamectl set-hostname $FQDN --transient 2>&1 | grep -v Hint`;
    chomp(my $return_code = `\$?`);
    
    if ($return_code == 0) {
        tell_user('SUCCESS', "Hostname for the system has been successfully set - Please reboot after\n\tOutput if any: $output");
    } else {
        tell_user('ERROR', "Something went wrong setting the hostname for the system - Output below:\n\t$output");

        if (!ask_user('Continue anyway?')) {
            die "Errors occured during hostname configuration - halting";
        }
    }
}

sub apache_config {

    my $conf_file = qq#
        <VirtualHost $FQDN:80>
    ServerName $FQDN
    ServerAdmin $WEB_ADMIN_EMAIL
    DocumentRoot $GAME_WEB_ROOT
    ErrorLog \${APACHE_LOG_DIR}/$FQDN.error.log
    CustomLog \${APACHE_LOG_DIR}/$FQDN.access.log combined
    LimitInternalRecursion 15;

    if (ask_user('Do you want to add the SSL-enabled configuration as well?')) {


    if (ask_user("Do you also want to redirect traffic from http:80 to https:443? A valid certificate will need to have been provided in the script configuration! (Currently set: $SSL_FULLCER with key: $SSL_PRIVKEY)")) {
        tell_user('INFO', 'Enabling mod_rewrite if it isn\'t already');
    }
    <IfModule mod_rewrite>
        RewriteEngine on
        RewriteCond %{SERVER_NAME} =loa.dankaf.ca
        RewriteRule ^ https://%{SERVER_NAME}%{REQUEST_URI} [END,NE,R=permanent]
    </IfModule>
</VirtualHost>#;
}
