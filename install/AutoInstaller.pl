#!/usr/bin/env perl

use warnings;
use strict;
use autodie;

# CONFIG - Server #
my $FQDN = 'loa.didney.whorl';
my ($SUB, $DOM, $TLD) = split /\./, $FQDN;
my $IP_ADDRESS  = '127.0.2.1';
my $LOG_TO_FILE = 'setup.log';
my $PHP_BINARY  = $1 if `whereis php` =~ /php: (\/?.*?\/bin\/.*?\/?php\d?\.?\d?)/;
my $GAME_WEB_ROOT     = "/var/www/html/$DOM.$TLD/$SUB";
my $GAME_TEMPLATE_DIR = "$GAME_WEB_ROOT/install/templates";
my $GAME_SCRIPTS_DIR  = "$GAME_WEB_ROOT/install/scripts";
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
my $SQL_USERNAME           = 'user_loa';
my $SQL_PASSWORD           = gen_random(15);
my $SQL_DATABASE           = 'db_loa';
my $SQL_HOST               = '127.0.2.1';
my $SQL_PORT               = 3306;
my $SQL_SERVER_CONFIG_FILE = '/etc/mysql/mariadb.conf.d/50-server.cnf';

# CONFIG - SSL Certificate information #
my $SSL_ENABLED = 1;
my $SSL_FULLCER = "/etc/letsencrypt/live/$FQDN/fullchain.pem";
my $SSL_PRIVKEY = "/etc/letsencrypt/live/$FQDN/privkey.pem";

# CONFIG - Apache VirtualHost configuration #
my $VIRTHOST_CONF_FILE     = "/etc/apache2/sites-available/$FQDN.conf";
my $VIRTHOST_CONF_FILE_SSL = "/etc/apache2/sites-available/ssl-$FQDN.conf";

# CONFIG - XAMPP configuration #
my $XAMPP_INSTALLER_BIN =
  'https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.3.4/xampp-windows-x64-8.3.4-0-VS16-installer.exe';
my $XAMPP_INSTALLER_ARGS =
  '--mode unattended --enabled-components xampp_server,xampp_apache,xampp_mysql,xampp_program_languages,xampp_php,xampp_perl,xampp_tools';
my $XAMPP_MARIADB_CHPW = 'mysqladmin.exe -u root password';

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
my $RESET  = "\e[0m";

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
               composer templates php sqlimport crons certificate services/;

foreach my $step (@steps) {
    -e "$GAME_WEB_ROOT/.loa.step.$step"
      ? ($completed{$step} = 1)
      : ($completed{$step} = 0);
}

foreach my $key (keys %completed) {
    next if !$completed{$key};
    print "It looks like you have ran this script before; do you want to\n";
    print "[c]ontinue from step '$key' where you left off, or [r]estart from\n";
    print "the beginning?\n[r]estart/[c]ontinue: ";
    chomp (my $answer = <STDIN>);

    if ($answer =~ /[rR](estart)?/) {
        clean_up();
        foreach my $key (keys %completed) {
            $completed{$key} = 0;
        }
    }
}

check_platform();

if (!-e '/etc/debian_version' && check_platform() eq "linux") {
    die "sry no :')";
}

if (ask_user("Update hosts file?")) {
    update_hosts() if !$completed{hosts};
    `touch $GAME_WEB_ROOT/.loa-step-hosts`;
}

if (ask_user("Install required software?")) {
    install_software() if !$completed{software};
    `touch $GAME_WEB_ROOT/.loa-step-software`;
}

if (ask_user("Update system hostname to match FQDN?")) {
    update_hostname() if !$completed{hostname};
    `touch $GAME_WEB_ROOT/.loa.step.hostname`;
}

if (ask_user("Perform necessary apache updates?")) {
    apache_config() if !$completed{apache};
    `touch $GAME_WEB_ROOT/.loa.step.apache`;
}

if (ask_user("Enable the required Apache conf/mods/sites?")) {
    apache_enables() if !$completed{apache_enables};
    `touch $GAME_WEB_ROOT/.loa.step.apache_enables`;
}

if (ask_user("Update PHP configurations? (security, performance)")) {
    update_php_confs() if !$completed{php};
    `touch $GAME_WEB_ROOT/.loa.step.php`;
}

if (ask_user("Run composer to download required dependencies?")) {
    composer_pull() if !$completed{composer};
    `touch $GAME_WEB_ROOT/.loa.step.composer`;
}

if (ask_user(
        "Multiple variables in this script are marked with " .
        "'Template Replacements' - Make sure these are filled out properly before " .
        "continuing\nContinue and process templates?"
    )) {
    process_templates();
    `touch $GAME_WEB_ROOT/.loa.step.templates`;
}

if (ask_user("Configure the SQL server and import processed template to create " .
             "databases, users and tables?")) {
    mysql_setup();
    `touch $GAME_WEB_ROOT/.loa.step.mysql`;
}

if (ask_user("Start all services?")) {
   start_services();
   `touch $GAME_WEB_ROOT/.loa.step.services`;
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
    if (check_platform() eq "linux") {
        open my $fh, '<', $LINUX_HOSTS_FILE
          or die "Unable to open file for read: $!\n";

        my $tmp   = <$fh>;
        my @hosts = split /[\r\n]/, $tmp;

        close $fh;

        if (grep (/$IP_ADDRESS\s+loa\./, @hosts)) {
            tell_user('WARNING',
                'Looks like the entry is already there, skipping');
        } else {
            open my $fh, '>', $LINUX_HOSTS_FILE
              or die "Unable to open file for write: $!\n";

            print $fh @hosts;
            print $fh "\n# Added by LoA\n$IP_ADDRESS\t$FQDN $SUB\n";

            tell_user('SUCCESS', 'Hosts entry added');

            close $fh;
        }
    } else {
        open my $fh, '<', $WIN32_HOSTS_FILE
          or die "Unable to open file for rw: $!\n";

        close $fh
          or die "Unable to close file: $!\n";
    }

}

#Step: software
sub install_software {
    my ($apt_output, $sury_output);
    my $install_cmd;

    if (-e '/etc/apt/sources.list.d/php.list') {
        tell_user('INFO', 'Sury repo entries already present');
    } else {
        tell_user('INFO',
            'Sury PHP repositories not found, adding necessary entries');

        tell_user('SYSTEM', `sh $GAME_SCRIPTS_DIR/sury_setup.sh`);
    }

    tell_user('INFO',   'Updating system packages');
    tell_user('SYSTEM', `apt update 2>&1`);

    my @packages = (
        'php8.2',                'php8.2-cli',
        'php8.2-common',         'php8.2-curl',
        'php8.2-dev',            'php8.2-fpm',
        'php8.2-mbstring',       'php8.2-mysql',
        'php8.2-xml',            'mariadb-server',
        'apache2',               'libapache2-mod-php',
        'libapache2-mod-php8.2', 'composer',
    );

    my $apt_cmd = 'apt install -y ' . join (' ', @packages) . ' 2>&1';
    tell_user('SYSTEM', `$apt_cmd`);
}

# Step: hostname
sub update_hostname {
    my $output = `hostnamectl set-hostname $FQDN 2>&1 | grep -v Hint`;
    $output .= `hostnamectl set-hostname $FQDN --pretty 2>&1 | grep -v Hint`;
    chomp (my $hostname = `hostname -f`);

    if ($hostname eq $FQDN) {
        tell_user('SUCCESS',
                "Hostname for the system has been successfully set\n"
              . "Please reboot after\n\tOutput if any: $output\n");
    } else {
        tell_user(
            'ERROR',
            "Something went wrong setting the hostname\nOutput below:\n\t$output"
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

    if (
        ask_user(
                "Do you want to add the default virtual host "
              . "configuration for $FQDN to $VIRTHOST_CONF_FILE?"
        )
    ) {
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
            open my $fh, '>', $VIRTHOST_CONF_FILE_SSL;
            print $fh $ssl_conf_file_data;
            close $fh;
        }
    }

    if (
        ask_user(
                "Do you also want to redirect traffic from http:80 to "
              . "https:443? A valid certificate needs to be set in the "
              . "script configuration!\n- Currently set -\n"
              . "Certificate: $SSL_FULLCER\n"
              . "Private Key: $SSL_PRIVKEY\n"
        )
    ) {

        tell_user('INFO',   'Enabling mod_rewrite if it isn\'t already');
        tell_user('SYSTEM', `a2enmod rewrite 2>&1`);

        open my $fh, '<', $VIRTHOST_CONF_FILE;
        my @lines = <$fh>;
        close $fh;

        my $vhost_output;
        for (my $i = 0; $i < scalar @lines - 1; $i++) {
            $vhost_output .= $lines[$i] . "\n";

            if ($lines[$i + 1] =~ /<\/VirtualHost>/) {
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

    tell_user('INFO',
        'Enabling required Apache configurations, sites and modules');

    my $conf_output = `a2enconf php8.2-fpm 2>&1`;
    $success = $? == 0 ? 1 : 0;

    my $mods_output = `a2enmod php8.2 rewrite setenvif 2>&1`;
    $success = $? == 0 ? 1 : 0;

    my $sites_output = `a2ensite $VIRTHOST_CONF_FILE 2>&1`;
    $success = $? == 0 ? 1 : 0;

    my $sites_ssl_output = `a2ensite $VIRTHOST_CONF_FILE_SSL 2>&1`;
    $success = !$? ? 1 : 0;

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

#Step: PHP configurations
sub update_php_confs {

}

# Step: composer
sub composer_pull {
    if (
        !ask_user(
            "Composer is going to download/install these as $COMPOSER_RUNAS\n"
              . "Do you want to change this? It should be the same user which\n"
              . "Apache runs under"
        )
    ) {
        my $cmd = "sudo -u $COMPOSER_RUNAS "
          . "composer --working-dir \"$GAME_WEB_ROOT\" install";
        my $cmd_output = `$cmd 2>&1`;
        tell_user('SYSTEM', $cmd_output);
    }
}

# Step: Template imports
sub process_templates {
    my $copy_output;
    my $cron_contents = '';
    my $fh_cron;
    my %templates;

    `sed -i 's/bind-address.*/bind-address = $IP_ADDRESS/' $SQL_SERVER_CONFIG_FILE`;

    # key = in file, value = out file
    $templates{$ENV_TEMPLATE}      = "$GAME_WEB_ROOT/.env";
    $templates{$HTACCESS_TEMPLATE} = "$GAME_WEB_ROOT/.htaccess";
    $templates{$SQL_TEMPLATE}      = "$SQL_TEMPLATE.ready";
    $templates{$CRONTAB_TEMPLATE}  = "$CRONTAB_DIRECTORY/$APACHE_RUNAS";

    foreach my $replacement (@replacements) {
        my ($search, $replace) = split /%%%/, $replacement;
        foreach my $template (keys %templates) {
            replace_in_file($search, $replace, $template, $templates{$template});
            print "[$template] $search -> $replace\n";
            `sed -i 's/$search/$replace/g' $template`;
        }
    }

    tell_user('SUCCESS', "Replacements have been made in all template files\n");
    
    tell_user('INFO',
        'Creating database, adding SQL user, granting privilges, and importing schema'
    );
    
    my $sql_cmd = "mysql -u $SQL_USERNAME -p'$SQL_PASSWORD' -h $SQL_HOST " .
                  "-P $SQL_PORT < $SQL_TEMPLATE 2>&1";

    chomp(my $sql_import_result = `$sql_cmd`);

    $sql_import_result //= 'Successfully imported database schema';

    tell_user(
        (!!$? ? 'ERROR' : 'SUCCESS'),    # LOL
        $sql_import_result
    );
}

#Step: start services
sub start_services {
    my @services = qw/mariadb php8.2-fpm apache2/;

    foreach my $service (@services) {
        tell_user('SYSTEM', `systemctl restart $service`);
    }
}

## INTERNAL SCRIPT FUNCTIONS ##

sub replace_in_file {
    my ($search, $replace, $file_in, $file_out) = @_;
    
    $file_out //= $file_in;

    open my $fh, '<', $file_in;
    my @contents = <$fh>;
    close $fh;

    for (my $i=0; $i<scalar @contents - 1; $i++) {
        print "Then: " . $contents[$i] . "\n";
        $contents[$i] =~ s/.*$search.*[\r\n]+/$replace/gm;
        print "Now : " . $contents[$i] . "\n";
    }

    print "[" . substr($file_in, -5, 5) . " -> " . substr($file_out, -5, 5) . "] $search -> $replace\n";
    
    open $fh, '>', $file_out;
    print $fh @contents;
    close $fh;
}

sub clean_up {
    foreach my $step (@steps) {
        my $file = "$GAME_WEB_ROOT/.loa.step-$step";
        eval {
            unlink ($file)
              if -e $file
              or
              tell_user('ERROR', "Couldn't remove progress file ($file): $!\n");
            tell_user('SUCCESS', "Removed progress file $file\n");
        };
    }

    tell_user('SUCCESS', 'Cleaned up all of our temp files!');
}

sub gen_random {
    my $length = $_[0];
    my $password;

    for (1 .. $length) {
        $password .= chr (int (rand (94) + 32));
    }

    return $password;
}

sub file_exists {
    my ($file, $data) = @_;
    my $fh;

    print "$file exists already...\n";
    print '[o]verwrite, [a]ppend, [s]kip -> ';

    chomp (my $answer = <STDIN>);

    if ($answer =~ /[Oo](verwrite)?/) {
        open $fh, '>', $file;
        tell_user('INFO', "Preparing to overwrite existing file: $file");
        print $fh $file;
    } elsif ($answer =~ /[Aa](ppend)?/) {
        open $fh, '>>', $file;
        tell_user('INFO', "Preparing to append to existing file: $file");
    } elsif ($answer =~ /[Ss](kip)?/) {
        tell_user('WARN', "Skipping write-to-file operation for $file");
        return;
    }

    print $fh $file;
    tell_user('SUCCESS', "Done writing to $file\n");
    close $fh;
}

sub ask_user {
    my $question = $_[0];
    my $date     = get_date();

    print $date . ' -> ' . $question;
    print "\n$date  ->Choice[${GREEN}y${RESET}/${RED}n${RESET}]: ";

    chomp (my $answer = <STDIN>);
    print "\n";

    if ($answer =~ /[Yy]e?s?/) {
        return 1;
    }

    return 0;
}

sub get_date {
    my ($sec, $min, $hour, $day, $mon, $year) = localtime ();
    my $date =
      sprintf ("[%02d-%02d %02d:%02d:%02d] -> ", $mon, $day, $hour, $min, $sec);
    return $date;
}

sub tell_user {
    my ($severity, $message, $result) = @_;
    my $date   = get_date();
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

    if ($LOG_TO_FILE) {
        open my $fh, '>>', $LOG_TO_FILE
          or die "Couldn't open log file for append '$LOG_TO_FILE': $!";

        print $fh $message;

        close $fh
          or die "Couldn't close file: $!\n";
    }
}
