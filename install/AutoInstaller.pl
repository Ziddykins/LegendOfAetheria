#!/usr/bin/env perl

use warnings;
use strict;

use Carp;
for (1 .. 10) {
    print "random: ";
    print gen_random(15);
    print "---\n";
}
die;
# CONFIG - Server #
my $FQDN              = 'loa.dankaf.ca';
my ($SUB, $DOM, $TLD) = split /\./, $FQDN;
my $IP_ADDRESS        = '127.0.1.1';
my $LOG_TO_FILE       = '';
my $PHP_BINARY        = $1 if `whereis php` =~ /php: (\/?.*?\/bin\/.*?\/?php\d?\.?\d?)/;
my $GAME_WEB_ROOT     = "/var/www/html/$DOM.$TLD/$SUB";
my $GAME_TEMPLATE_DIR = "$GAME_WEB_ROOT/install/templates";
my $WEB_ADMIN_EMAIL   = "webmaster\@$DOM";
my $CRONTAB_DIRECTORY = '/var/spool/cron/crontabs';

# CONFIG - SQL Tables / Template Replacements #
my $SQL_TBL_CHARACTERS = 'tbl_characters';
my $SQL_TBL_FAMILIARS  = 'tbl_familiars';
my $SQL_TBL_ACCOUNTS   = 'tbl_accounts';
my $SQL_TBL_FRIENDS    = 'tbl_friends';
my $SQL_TBL_GLOBALS    = 'tbl_globals';
my $SQL_TBL_MAIL       = 'tbl_mail';
my $SQL_TBL_CHATS      = 'tbl_chats';

# CONFIG - SQL Credentials / Template Replacements #
my $SQL_USERNAME = 'user_loa';
my $SQL_PASSWORD = gen_random(15);
my $SQL_DATABASE = 'db_loa';
my $SQL_HOST     = '127.0.2.1';
my $SQL_PORT     = 3306;

# CONFIG - SSL Certificate information #
my $SSL_ENABLED = 1;
my $SSL_FULLCER = "/etc/letsencrypt/live/$FQDN/fullchain.pem";
my $SSL_PRIVKEY = "/etc/letsencrypt/live/$FQDN/privkey.pem";

# CONFIG - Apache VirtualHost configuration #
my $VIRTHOST_LOCATION = 2;
my $VIRTHOST_CONF_FILE = "/etc/apache2/sites-available/$FQDN.conf";
my $VIRTHOST_CONF_FILE_SSL = "/etc/apache2/sites-available/ssl-$FQDN.conf";

# CONFIG - XAMPP configuration #
my $XAMPP_INSTALLER_BIN  = 'https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.3.4/xampp-windows-x64-8.3.4-0-VS16-installer.exe';
my $XAMPP_INSTALLER_ARGS = '--mode unattended --enabled-components xampp_server,xampp_apache,xampp_mysql,xampp_program_languages,xampp_php,xampp_perl,xampp_tools';
my $XAMPP_MARIADB_CHPW   = 'mysqladmin.exe -u root password';

# CONFIG - Composer #
my $COMPOSER_RUNAS = 'www-data';
my $APACHE_RUNAS   = 'www-data';

# CONFIG - Template Files #
my $VIRTHOST_SSL_TEMPLATE = "$GAME_TEMPLATE_DIR/virtual_host_ssl.template";
my $VIRTHOST_TEMPLATE     = "$GAME_TEMPLATE_DIR/virtual_host.template";
my $HTACCESS_TEMPLATE     = "$GAME_TEMPLATE_DIR/htaccess.template";
my $CRONTAB_TEMPLATE      = "$GAME_TEMPLATE_DIR/crontab.template";
my $ENV_TEMPLATE          = "$GAME_TEMPLATE_DIR/env.template";
my $SQL_TEMPLATE          = "$GAME_TEMPLATE_DIR/sql.template";

# NOCONFIG - Hosts files #
my $WIN32_HOSTS_FILE = 'c:\windows\system32\drivers\etc\hosts';
my $LINUX_HOSTS_FILE = '/etc/hosts';

# NOCONFIG - Colors #
my $RED    = "\e[31m";
my $GREEN  = "\e[32m";
my $YELLOW = "\e[33m";
my $BLUE   = "\e[34m";
my $CYAN   = "\e[36m";
my $GREY   = "\e[37m";
my $RESET  =  "\e[0m";

# NOCONFIG - Replacements for Templates
my @replacements = (
    "###REPL_PHP_BINARY###%%%$PHP_BINARY",
	"###REPL_WEB_ROOT###%%%$GAME_WEB_ROOT",
	"###REPL_SQL_DB###%%%$SQL_DATABASE",
	"###REPL_SQL_USER###%%%$SQL_USERNAME",
	"###REPL_SQL_PASS###%%%$SQL_PASSWORD",
	"###REPL_SQL_HOST###%%%$SQL_HOST",
	"###REPL_SQL_PORT###%%%$SQL_PORT",
	"###REPL_SQL_TBL_ACCOUNTS###%%%$SQL_TBL_ACCOUNTS",
	"###REPL_SQL_TBL_CHARACTERS###%%%$SQL_TBL_CHARACTERS",
	"###REPL_SQL_TBL_FAMILIARS###%%%$SQL_TBL_FAMILIARS",
	"###REPL_SQL_TBL_FRIENDS###%%%$SQL_TBL_FRIENDS",
	"###REPL_SQL_TBL_GLOBALS###%%%$SQL_TBL_GLOBALS",
	"###REPL_SQL_TBL_MAIL###%%%$SQL_TBL_MAIL",
	"###REPL_SQL_USER###%%%$SQL_USERNAME"
);

## NO MORE CONFIGURATION BEYOND THIS POINT ##

my %completed;
my @steps = qw/hosts software hostname apache apache-enables
              composer templates php sqlimport crons certificate/;

foreach my $step (@steps) {
    -e ".loa.step.$step" 
        ? ($completed{$step} = 1) 
        : ($completed{$step} = 0);
}

foreach my $key (keys %completed) {
    next if !$completed{$key};
    print "It looks like you have ran this script before; do you want to\n";
    print "[c]ontinue from step '$key' where you left off, or [r]estart from\n";
    print "the beginning?\n[r]estart/[c]ontinue: ";
    chomp(my $answer = <STDIN>);

    if ($answer =~ /[rR](estart)?/) {
        clean_up();
        foreach my $key (keys %completed) {
            $completed{$key} = 0;
        }
    }
}

check_platform();

if (!-e '/etc/debian_version' && check_platform() eq "linux") {
    croak "sry no :')";
}

if (ask_user("Update hosts file?")) {
    update_hosts() if !$completed{hosts};
    `touch .loa-step-hosts`;
}

if (ask_user("Install required software?")) {
    install_software() if !$completed{software};
    `touch .loa-step-software`;
}

if (ask_user("Update system hostname to match FQDN?")) {
    update_hostname() if !$completed{hostname};
    `touch .loa.step.hostname`;
}

if (ask_user("Perform necessary apache updates?")) {
    apache_config() if !$completed{apache};
    `touch .loa.step.apache`;
}

if (ask_user("Enable the required Apache conf/mods/sites?")) {
    apache_enables() if !$completed{apache_enables};
    `touch .loa.step.apache_enables`;
}

if (ask_user("Update PHP configurations? (security, performance)")) {
   update_php_confs() if !$completed{php};
    `touch .loa.step.php`;
}

if (ask_user("Run composer to download required dependencies?")) {
    composer_pull() if !$completed{composer};
    `touch .loa.step.composer`;
}

if (ask_user("There are multiple template files which need to be processed; make " .
             "sure that all of the sections marked with 'Template Replacements' " .
             "are filled out properly - Continue?")) {
    process_templates();
    `touch .loa.step.templates`;
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


# Step: hosts
sub update_hosts {
    my $fh;
    
    if (check_platform() eq "linux") {
        open $fh, '<', $LINUX_HOSTS_FILE
            or die "Unable to open file for rw: $!\n";
    } else {
        open $fh, '<', $WIN32_HOSTS_FILE
            or die "Unable to open file for rw: $!\n";
    }
    
    my @tmp_contents = <$fh>;
    close $fh;

    open $fh, '>', $LINUX_HOSTS_FILE;
    my $contents = join '', @tmp_contents;
    
    if ($contents =~ /$IP_ADDRESS/) {
        if (ask_user('Looks like the entry is already there, overwrite?')) {
            $contents =~ 
                s/.*?$IP_ADDRESS.*?/\n\n# Added by LoA\n$IP_ADDRESS\t$FQDN\t$SUB/;
            print $fh $contents;
        }
    } else {
        print $fh $contents;
        print $fh "\n\n# Added by LoA\n$IP_ADDRESS\t$FQDN $SUB\n";
        tell_user('SUCCESS', 'Hosts entry added');
    }
    
    close $fh or die "Unable to close file: $!\n";
}

#Step: software
sub install_software {
    my $apt_output;
    
    if (ask_user("Update too?")) {
        tell_user('INFO', 'Updating system packages');
        $apt_output = `apt update 2>&1`;
        tell_user('SYSTEM', $apt_output);
    }
    my $packages = 'php8.3 php8.3-cli php8.3-common php8.3-curl php8.3-dev php8.3-fpm php8.3-mbstring php8.3-mysql php8.3-xml php8.3-xdebug mariadb-server apache2 libapache2-mod-log-sql-mysql libapache2-mod-log-sql-ssl libapache2-mod-php libapache2-mod-php8.3 composer';
    $apt_output = `apt install -y $packages 2>&1`;
    tell_user('SYSTEM', $apt_output);
}

# Step: hostname
sub update_hostname {
    my $output = `hostnamectl set-hostname $FQDN 2>&1 | grep -v Hint`;
    $output   .= `hostnamectl set-hostname $FQDN --pretty 2>&1 | grep -v Hint`;
    chomp(my $hostname = `hostname -f`);
    
    if ($hostname eq $FQDN) {
        tell_user(
            'SUCCESS',
            "Hostname for the system has been successfully set\n" .
                "Please reboot after\n\tOutput if any: $output\n"
        );
    } else {
        tell_user(
            'ERROR',
            "Something went wrong setting the hostname\n" .
                "Output below:\n\t$output"
        );

        if (!ask_user('Continue anyway?')) {
            die "Errors occured during hostname configuration - halting";
        }
    }
}
# Step: apache
sub apache_config {
    my $conf_file_data = qq#
        <VirtualHost $FQDN:80>
            ServerName $FQDN
            ServerAdmin $WEB_ADMIN_EMAIL
            DocumentRoot $GAME_WEB_ROOT
            ErrorLog \${APACHE_LOG_DIR}/$FQDN.error.log
            CustomLog \${APACHE_LOG_DIR}/$FQDN.access.log combined
            LimitInternalRecursion 15;
        </VirtualHost>
    #;

    my $ssl_conf_file_data = qq#
        <VirtualHost $FQDN:443>
    	    ServerName $FQDN
            ServerAdmin $WEB_ADMIN_EMAIL
    	    DocumentRoot $GAME_WEB_ROOT
    	    LogLevel info ssl:warn
    	    ErrorLog \${APACHE_LOG_DIR}/$FQDN-error.log
    	    CustomLog \${APACHE_LOG_DIR}/$FQDN-access.log combined
    	    SSLEngine on
    	    SSLCertificateFile "$SSL_FULLCER"
    	    SSLCertificateKeyFile "$SSL_PRIVKEY"

	        <FilesMatch "\.(?:cgi|shtml|phtml|php)$">
              	SSLOptions +StdEnvVars
	        </FilesMatch>
	        <Directory /usr/lib/cgi-bin>
            	SSLOptions +StdEnvVars
	        </Directory>	
        </VirtualHost>
    #;

    if (ask_user("Do you want to add the default virtual host " .
                 "configuration for $FQDN to $VIRTHOST_CONF_FILE?")) {
        if (-e $VIRTHOST_CONF_FILE) {
            file_exists($VIRTHOST_CONF_FILE, $conf_file_data);
        } else {
            open my $fh, '>', $VIRTHOST_CONF_FILE;
            print $fh $conf_file_data;
            close $fh;
        }
    }

    if (ask_user('Do you want to add the SSL-enabled configuration as well?')) {
        if (-e $VIRTHOST_CONF_FILE_SSL) {
            file_exists($VIRTHOST_CONF_FILE_SSL, $ssl_conf_file_data);
        } else {
            open my $fh, '>', $VIRTHOST_CONF_FILE;
            print $fh $ssl_conf_file_data;
            close $fh;
        }
    }

    if (ask_user("Do you also want to redirect traffic from http:80 to https:443? A valid certificate will need to have been provided in the script configuration! (Currently set: $SSL_FULLCER with key: $SSL_PRIVKEY)")) {
        tell_user('INFO', 'Enabling mod_rewrite if it isn\'t already');
        my $output = `a2enmod rewrite 2>&1`;
        tell_user('SYSTEM', $output);

        open my $fh, '<', $VIRTHOST_CONF_FILE;
        my @lines = <$fh>;
        close $fh;
        
        my $vhost_output;
        for (my $i=0; $i<scalar @lines - 1; $i++) {
            $output .= $lines[$i] . "\n";
            if ($lines[$i+1] =~ /<\/VirtualHost>/) {
                $vhost_output .= qq#
                    <IfModule mod_rewrite>
                        RewriteEngine on
                        RewriteCond %{SERVER_NAME} =$FQDN
                        RewriteRule ^ https://%{SERVER_NAME}%{REQUEST_URI} [END,NE,R=permanent]
                        </IfModule>
                    </VirtualHost>
                #;
                last;
            }
        }
    }        
}

# Step: apache_enables
sub apache_enables {
    my $success = 0;

    tell_user('INFO', 'Enabling required Apache configurations, sites and modules');
    my $conf_output      = `a2enconf php8.3-fpm 2>&1`;
    $success = $? == 0 ? 1 : 0;

    my $mods_output      = `a2enmod php8.3 rewrite setenvif 2>&1`;
    $success = $? == 0 ? 1 : 0;
    
    my $sites_output     = `a2ensite $VIRTHOST_CONF_FILE 2>&1`;
    $success = $? == 0 ? 1 : 0;
    
    my $sites_ssl_output = `a2ensite $VIRTHOST_CONF_FILE_SSL 2>&1`;
    $success = $? == 0 ? 1 : 0;

    tell_user('SYSTEM', "          conf result: $conf_output");
    tell_user('SYSTEM', "          mods result: $mods_output");
    tell_user('SYSTEM', "site (non-ssl) result: $sites_output");
    tell_user('SYSTEM', "     site (ssl) resul: $sites_ssl_output");

    if ($success) {
        tell_user('SUCCESS', "Apache configuration completed");
    } else {
        tell_user('ERROR', "There were errors - See above output\n");
        if (!ask_user('Continue?')) {
            die "Quitting at user request\n";
        }
    }
}

# Step: composer
sub composer_pull {
    if (!ask_user("Composer is going to download/install these as $COMPOSER_RUNAS\n" .
                 "Do you want to change this? It should be the same user which\n"  .
                 "Apache runs under")) {
        my $cmd = "sudo -u $COMPOSER_RUNAS " .
                  "composer --working-dir \"$GAME_WEB_ROOT\" install --force";
        my $cmd_output = `$cmd 2>&1`;
        tell_user('SYSTEM', $cmd_output);
    }
}

sub process_templates {
    open my $fh_env,  '<', $ENV_TEMPLATE;
    open my $fh_sql,  '<', $SQL_TEMPLATE;
    open my $fh_cron, '<', $CRONTAB_TEMPLATE;

    my $env_contents  = <$fh_env>;
    my $sql_contents  = <$fh_sql>;
    my $cron_contents = <$fh_cron>;

    close $fh_env;
    close $fh_sql;
    close $fh_cron;

    foreach my $replacement (@replacements) {
    #   my ($search, $replace) = split /%%%/, $replacement;
    #    $env_contents  =~ s=$search=$replace=g
    #    $cron_contents =~ s=$search=$replace=g;
    #    $sql_contents  =~ s=$search=$replace=g;
    }

    tell_user('SUCCESS', "Replacements have been made in all template files\n");
    tell_user('INFO', "Copying template files to the proper spots now\n");

    my $copy_output;
    $copy_output = `cp $GAME_TEMPLATE_DIR/env.template $GAME_WEB_ROOT/.env`;
    tell_user('ERROR', "Copy env.template result: $copy_output\n") if $? != 0;

    $copy_output = `cp $GAME_TEMPLATE_DIR/crontab.template $CRONTAB_DIRECTORY/$APACHE_RUNAS`;
    tell_user('ERROR', "Move crontab.template result: $!") if $? != 0;

    $copy_output = `cp $GAME_TEMPLATE_DIR/htaccess.template $GAME_WEB_ROOT/.htaccess`;
    tell_user('ERROR', "Move htaccess.template result: $!") if $? != 0;
}

## INTERNAL SCRIPT FUNCTIONS ##

sub clean_up {
    foreach my $step (@steps) {
        my $file = "$GAME_WEB_ROOT/.loa.step-$step";
        unlink($file)
            or tell_user('ERROR', "Couldn't remove progress file ($file): $!\n");
        tell_user('SUCCESS', "Removed progress file $file\n");
    }
    tell_user('SUCCESS', 'Cleaned up all of our temp files!');
}

sub gen_random {
    my $length = $_[0];
    my $password;

    for (1 .. $length) { 
        $password .= chr(int(rand(94)+32)); 
    }
    
    return $password;
}

sub file_exists {
    my ($file, $data) = @_;
    print "$file exists already...\n";
    print '[o]verwrite, [a]ppend, [s]kip -> ';
    chomp(my $answer = <STDIN>);
    
    my $fh;
    if ($answer =~ /[Oo](verwrite)?/) {
        open $fh, '>', $file;
        tell_user('INFO', "Preparing to overwrite existing file $file");
        print $fh $file;
    } elsif ($answer =~ /[Aa](ppend)?/) {
        open $fh, '>>', $file;
        tell_user('INFO', "Prepairing to append to existing file $file");
        print $fh $file;
    } elsif ($answer =~ /[Ss](kip)?/) {
        tell_user('WARN', "Skipping write-to-file operation for $file");
    }
    close $fh;
}

sub ask_user {
    my $question = $_[0];
    my $date = get_date();

    print $date . ' -> ' . $question;
    print "\n$date  ->Choice[${GREEN}y${RESET}/${RED}n${RESET}]: ";
    
    chomp(my $answer = <STDIN>);
    print "\n";
    
    if ($answer =~ /[Yy]e?s?/) {
        return 1;
    }
    
    return 0;
}

sub get_date {
    my ($sec, $min, $hour, $day, $mon, $year) = localtime();
    my $date  = sprintf("[%02d-%02d %02d:%02d:%02d] -> ",$mon, $day, $hour, $min, $sec);
    return $date;
}

sub tell_user {
    my ($severity, $message, $result) = @_;
    my $date = get_date();
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
        close $fh or die "Couldn't close file: $!\n";
    }
}
