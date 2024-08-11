#!/usr/bin/env perl

use warnings;
use strict;
use autodie;

use File::Path qw(make_path remove_tree);
use File::Find;
use Data::Dumper;

use vars qw/*name *dir *prune/;
*name   = *File::Find::name;
*dir    = *File::Find::dir;
*prune  = *File::Find::prune;

sub find_temp;
sub do_delete ($@);


# NOCONFIG - Colors #
my $RED    = "\e[31m";
my $GREEN  = "\e[32m";
my $YELLOW = "\e[33m";
my $BLUE   = "\e[34m";
my $CYAN   = "\e[36m";
my $GREY   = "\e[37m";
my $RESET  = "\e[0m";

# CONFIG - Server #
my $FQDN              = 'loa.dankaf.ca';
my ($SUB, $DOM, $TLD) = split /\./, $FQDN;
my $IP_ADDRESS        = '127.0.2.1';
my $LOG_TO_FILE       = 'setup.log';
my $PHP_BINARY        = $1 if `whereis php` =~ /php: (\/?.*?\/bin\/.*?\/?php\d?\.?\d?)/;
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
my $SQL_TBL_MONSTERS   = 'tbl_monsters';
my $SQL_TBL_LOGS       = 'tbl_logs';

# CONFIG - SQL Credentials / Template Replacements #
my $SQL_USERNAME           = 'user_loa';
my $SQL_PASSWORD           = gen_random(15);
my $SQL_DATABASE           = 'db_loa';
my $SQL_HOST               = $IP_ADDRESS;
my $SQL_PORT               = 3306;
my $SQL_SERVER_CONFIG_FILE = '/etc/mysql/mariadb.conf.d/50-server.cnf';

# CONFIG - SSL Certificate information #
my $SSL_ENABLED = 1;
my $SSL_FULLCER = "/etc/letsencrypt/live/$FQDN/fullchain.pem";
my $SSL_PRIVKEY = "/etc/letsencrypt/live/$FQDN/privkey.pem";

# CONFIG - Apache VirtualHost configuration #
my $APACHE_DIRECTORY       = "/etc/apache2";
my $VIRTHOST_CONF_FILE     = "$APACHE_DIRECTORY/sites-available/${FQDN}.conf";
my $VIRTHOST_CONF_FILE_SSL = "$APACHE_DIRECTORY/sites-available/ssl-${FQDN}.conf";

# CONFIG - XAMPP configuration #
my $XAMPP_INSTALLER_BIN =
  'https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.3.4/xampp-windows-x64-8.3.4-0-VS16-installer.exe';
my $XAMPP_INSTALLER_ARGS =
  '--mode unattended --enabled-components xampp_server,xampp_apache,xampp_mysql,xampp_program_languages,xampp_php,xampp_perl,xampp_tools';
my $XAMPP_MARIADB_CHPW = 'mysqladmin.exe -u root password';

# CONFIG - Composer #
my $COMPOSER_RUNAS = 'www-data';
my $APACHE_RUNAS   = 'www-data';

# CONFIG - PHP #
my $PHP_VERSION;
my $PHP_INI_FILE = 'unset';

# CONFIG - OpenAI #
my $OPENAI_ENABLE = 0;
my $OPENAI_APIKEY = 'unset';

if ($OPENAI_ENABLE) {
    print "OpenAI configuration is enabled; please enter your OpenAI API key:\n";
    print "Key: ";
    chomp($OPENAI_APIKEY = <STDIN>);
}

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

# NOCONFIG - Replacements for Templates
my @replacements = (
    "###REPL_PHP_BINARY###%%%$PHP_BINARY",

    "###REPL_WEB_ROOT###%%%$GAME_WEB_ROOT",
    "###REPL_WEB_ADMIN_EMAIL###%%%$WEB_ADMIN_EMAIL",
    "###REPL_WEB_FQDN###%%%$FQDN",
    "###REPL_WEB_DOCROOT###%%%$GAME_WEB_ROOT",
    "###REPL_WEB_SSL_FULLCER###%%%$SSL_FULLCER",
    "###REPL_WEB_SSL_PRIVKEY###%%%$SSL_PRIVKEY",

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
    "###REPL_SQL_TBL_MONSTERS###%%%$SQL_TBL_MONSTERS",
    "###REPL_SQL_USER###%%%$SQL_USERNAME",
    "###REPL_SQL_TBL_LOGS###%%%$SQL_TBL_LOGS",

    "###REPL_OPENAI_APIKEY###%%%$OPENAI_APIKEY",
);

## NO MORE CONFIGURATION BEYOND THIS POINT ##

if (check_array(\@ARGV, "--revert-system")) {
    clean_up('revert');
}

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

#if (ask_user("Update hosts file?")) {
#    update_hosts() if !$completed{hosts};
#    `touch $GAME_WEB_ROOT/.loa-step-hosts`;
#}

if (ask_user("Install required software?")) {
    install_software() if !$completed{software};
    `touch $GAME_WEB_ROOT/.loa-step-software`;
}

if (ask_user(
        "Multiple variables in this script are marked with " .
        "'Template Replacements' - Make sure these are filled out properly before " .
        "continuing\nContinue and generate templates?"
    )) {
    generate_templates();
    `touch $GAME_WEB_ROOT/.loa.step.templates.gen`;
}

if (ask_user("Process generated templates?")) {
    process_templates();
    `touch $GAME_WEB_ROOT/.loa.step.templates.process`;
}

#if (ask_user("Update system hostname to match FQDN?")) {
#    update_hostname() if !$completed{hostname};
#   `touch $GAME_WEB_ROOT/.loa.step.hostname`;
#}

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

if (ask_user("Start all services?")) {
   start_services();
   `touch $GAME_WEB_ROOT/.loa.step.services`;
}

if (ask_user("Fix all webserver permissions?")) {
    fix_permissions();
    `touch $GAME_WEB_ROOT/.loa.step.permissions`;
}

if (ask_user("Clean up temp files?")) {
    clean_up();
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
        tell_user(
            'INFO',
            'Sury PHP repositories not found, adding necessary entries'
        );

        tell_user('SYSTEM', `sh $GAME_SCRIPTS_DIR/sury_setup.sh`);
    }

    tell_user('INFO',   'Updating system packages');
    tell_user('SYSTEM', `apt update 2>&1`);

    if (!$PHP_VERSION) {
        tell_user('WARN', "PHP version not specified, attempting to find it...\n");

        if (check_platform() eq 'linux') {
            chomp(my $ver_output = `php --version | head -n1`);

            if ($ver_output =~ /PHP (\d+\.\d+)/) {
                $PHP_VERSION = $1;
            }

            if ($PHP_VERSION) {
                tell_user('SUCCESS', "Found PHP version $PHP_VERSION");
            } else {
                tell_user('ERROR', 'Failed to find PHP version, please manually specify in this file');
                die "Exiting on failure...\n";
            }
        }
    } else {
        die "Invalid or unsupported PHP version set in the installer\n" .
            "supported PHP versions: 7.0 - 8.3\n\n";
    }

    my @packages = (
        "cron",
        "php$PHP_VERSION",
        "php$PHP_VERSION-cli",
        "php$PHP_VERSION-common",
        "php$PHP_VERSION-curl",
        "php$PHP_VERSION-dev",
        "php$PHP_VERSION-fpm",
        "php$PHP_VERSION-mbstring",
        "php$PHP_VERSION-mysql",
        "php$PHP_VERSION-xml",
        "mariadb-server",
        "apache2",
        "letsencrypt",
        "python-is-python3",
        "python3-certbot-apache",
        "libapache2-mod-php",
        "libapache2-mod-php$PHP_VERSION",
        "composer",
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
    if (
        ask_user(
                "Do you want to redirect traffic from http:80 to "
              . "https:443? A valid certificate needs to be set in the "
              . "script configuration!\n- Currently set -\n"
              . "Certificate: $SSL_FULLCER\n"
              . "Private Key: $SSL_PRIVKEY\n"
        )
    ) {

        tell_user('INFO',   'Enabling mod_rewrite if it isn\'t already');
        tell_user('SYSTEM', `a2enmod rewrite 2>&1`);

        tell_user('INFO', "Enabling rewrite directives in $VIRTHOST_CONF_FILE");
        `sed -i 's/# REM //' $VIRTHOST_CONF_FILE`;

    }
}

# Step: Fix permissions
sub fix_permissions {
    `find $GAME_WEB_ROOT -type f -exec chmod 644 {} + 2>&1`;
    `find $GAME_WEB_ROOT -type d -exec chmod 755 {} + 2>&1`;
    `chown -R $APACHE_RUNAS:$APACHE_RUNAS $GAME_WEB_ROOT 2>&1`;
}

# Step: apache_enables
sub apache_enables {
    my $success = 0;

    tell_user('INFO',
        'Enabling required Apache configurations, sites and modules');

    my $conf_output = `a2enconf php$PHP_VERSION-fpm 2>&1`;
    $success = $? == 0 ? 1 : 0;

    my $mods_output = `a2enmod php$PHP_VERSION rewrite setenvif ssl 2>&1`;
    $success = $? == 0 ? 1 : 0;

    my $sites_output = `a2ensite $FQDN.conf 2>&1`;
    $success = $? == 0 ? 1 : 0;

    my $sites_ssl_output = `a2ensite ssl-$FQDN.conf 2>&1`;
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
    if (check_platform() eq 'linux' && $PHP_INI_FILE eq 'unset') {
        $PHP_INI_FILE = "/etc/php/$PHP_VERSION/php.ini";
    } else {
        $PHP_INI_FILE = 'C:\xampp\php\php.ini';
    }
}

# Step: composer
sub composer_pull {
    if (ask_user("Composer is going to download/install these as $COMPOSER_RUNAS - continue?\n")) {
        my $cmd = "sudo -u $COMPOSER_RUNAS composer --working-dir \"$GAME_WEB_ROOT\" install";
        my $cmd_output = `$cmd 2>&1`;
        tell_user('SYSTEM', $cmd_output);
    }
}

# Step: Template imports
sub generate_templates {
    my $copy_output;
    my $cron_contents = '';
    my $fh_cron;
    my %templates;

    `sed -i 's/bind-address.*/bind-address = $IP_ADDRESS/' $SQL_SERVER_CONFIG_FILE`;

    # key = in file, value = out file
    $templates{$ENV_TEMPLATE}          = "$ENV_TEMPLATE.ready";
    $templates{$HTACCESS_TEMPLATE}     = "$HTACCESS_TEMPLATE.ready";
    $templates{$SQL_TEMPLATE}          = "$SQL_TEMPLATE.ready";
    $templates{$CRONTAB_TEMPLATE}      = "$CRONTAB_TEMPLATE.ready";
    $templates{$VIRTHOST_SSL_TEMPLATE} = "$VIRTHOST_SSL_TEMPLATE.ready";
    $templates{$VIRTHOST_TEMPLATE}     = "$VIRTHOST_TEMPLATE.ready";
    
    while(my ($key, $val) = each %templates) {
        open my $fh, '<', $key;
        local $/;
        my $contents = <$fh>;
        close $fh;

        foreach my $replacement (@replacements) {
            my ($search, $replace) = split '%%%', $replacement;
            $contents =~ s/$search/$replace/gm;
            file_write($templates{$key}, $contents, 'data', 1);           
        }
    }

    tell_user('SUCCESS', "Replacements have been made in all template files\n");
}

sub process_templates {
    my $cp_cmd;
    if (check_platform() eq 'linux') {
        $cp_cmd = "cp";
    } else {
        $cp_cmd = "copy";
    }

    tell_user('INFO', "Importing SQL schema, creating user and granting privileges");
    
    my $sql_cmd = "mysql -u root < $SQL_TEMPLATE.ready 2>&1";
    chomp(my $sql_import_result = `$sql_cmd`);
    $sql_import_result //= 'Successfully imported database schema';

    # LOL
    tell_user((!!$? ? 'ERROR' : 'SUCCESS'), $sql_import_result);
    
    tell_user('INFO', "Copying over $ENV_TEMPLATE.ready to $GAME_WEB_ROOT/.env");
    file_write("$GAME_WEB_ROOT/.env", "$ENV_TEMPLATE.ready", 'file');

    tell_user('INFO', "Copying over $VIRTHOST_TEMPLATE.ready to $VIRTHOST_CONF_FILE");
    file_write($VIRTHOST_CONF_FILE, "$VIRTHOST_TEMPLATE.ready", 'file');

    if ($SSL_ENABLED) {
        tell_user('INFO', "Copying over $VIRTHOST_SSL_TEMPLATE.ready to $VIRTHOST_CONF_FILE_SSL");
        file_write($VIRTHOST_CONF_FILE_SSL, "$VIRTHOST_SSL_TEMPLATE.ready", 'file');
    }

    tell_user('INFO', "Copying over $HTACCESS_TEMPLATE.ready to $GAME_WEB_ROOT/.htaccess");
    file_write("$GAME_WEB_ROOT/.htaccess", "$HTACCESS_TEMPLATE.ready", 'file');

    tell_user('INFO', "Copying over $CRONTAB_TEMPLATE to $CRONTAB_DIRECTORY/$APACHE_RUNAS");
    
    if (!-d $CRONTAB_DIRECTORY) {
        make_path($CRONTAB_DIRECTORY);
    }
    if (-e "$CRONTAB_DIRECTORY/$APACHE_RUNAS") {
        unlink("$CRONTAB_DIRECTORY/$APACHE_RUNAS");
    }

    file_write("$CRONTAB_DIRECTORY/$APACHE_RUNAS", "$CRONTAB_TEMPLATE.ready");
    
    tell_user('INFO', "Updating permissions on new crontab to $APACHE_RUNAS:crontab");

    `chown $APACHE_RUNAS:crontab $CRONTAB_DIRECTORY/$APACHE_RUNAS`;

    tell_user('SUCCESS', "All template files have been applied");    
}

#Step: start services
sub start_services {
    my @services = qq/mariadb php$PHP_VERSION-fpm apache2/;

    foreach my $service (@services) {
        tell_user('SYSTEM', `systemctl restart $service`);
    }
}

## INTERNAL SCRIPT FUNCTIONS ##
sub replace_in_file {
    my ($search, $replace, $file_in, $file_out) = @_;
    
    $file_out //= $file_in;

    local $/;
    open my $fh, '<', $file_in;
    my @contents = <$fh>;
    close $fh;
   
    for (my $i=0; $i<scalar @contents - 1; $i++) {
        next if $contents[$i] !~ /$search/;
        my $new;
        ($new = $contents[$i]) =~ s/$search/$replace/g;
        $contents[$i] = $new;
        print "NEW: $new\n";
    }

    print "[" . substr($file_in, -5, 5) . " -> " . substr($file_out, -5, 5) . "] $search -> $replace\n";
    
    open $fh, '>', $file_out;
    print $fh @contents;
    close $fh;
}

sub clean_up {
    my ($mode, $file) = @_;

    if ($mode eq 'revert') {
        tell_user("INFO", "Searching for temporary and generated files to clean up in $GAME_WEB_ROOT");
        File::Find::find({wanted => \&find_temp}, "$GAME_WEB_ROOT/");

        tell_user("INFO", "Searching for temporary and generated files to clean up in $APACHE_DIRECTORY");
        `a2dissite $VIRTHOST_CONF_FILE`;
        
        if ($SSL_ENABLED) {
            `a2dissite $VIRTHOST_CONF_FILE_SSL`;
        }

        File::Find::find({wanted => \&find_temp}, "$APACHE_DIRECTORY/");

        tell_user("INFO", "Dropping sury repos from sources.list.d");
        File::Find::find({wanted => \&find_temp}, '/etc/apt/sources.list.d/');

        tell_user("INFO", "Dropping database $SQL_DATABASE and dropping user $SQL_USERNAME");
        `mysql -e 'DROP DATABASE $SQL_DATABASE; DROP USER $SQL_USERNAME;'`;

        tell_user("INFO", "Removing our crontab at $CRONTAB_DIRECTORY/$APACHE_RUNAS");
        unlink("$CRONTAB_DIRECTORY/$APACHE_RUNAS");
    }



    tell_user('SUCCESS', 'Cleaned up all of our temp files!');
}

sub find_temp {
    my $file = $_;

    my @files_to_remove = (
        '\.env$',
        '^\.loa-step',
        '\.ready$',
        "ssl-$FQDN.conf\$",
        "$FQDN.conf\$",
        'php.list$'
    );

    if (grep /$file/, @files_to_remove) {
        tell_user("SUCCESS", "Removed temp file $file");
#        unlink($file);
        print "unlink($file) :o\n";
    }
}

sub gen_random {
    my $length = $_[0];
    my $password;

    for (1 .. $length) {
        $password .= chr(int(rand(94)) + 33);
    }
    $password =~ s/"/\\"/g;

    return $password;
}

sub file_write {
    my ($dst, $src, $type, $force) = @_;
    my $fh;
    my $data;
    my $answer;

    if ($type eq 'file') { # essentially 'copy'
        if (-e $src) {
            local $/;
            open my $src_fh, '<', $src or die "Can't open source file '$src': $@\n";
                $data = <$src_fh>;
            close $src_fh or die "Can't close source file '$src': $@\n";
        }
        tell_user('INFO', "Loaded data from file '$src'");
    } elsif ($type eq 'data') {
        $data = $src;
        tell_user('INFO', "Loaded data directly from passed variable");
    }

    if (!-e $dst) {
        tell_user('INFO', "File '$dst' doesn't exist already, writing new file");
        
        open $fh, '>', $dst or die "Can't open file '$dst' for write: $@\n";
            print $fh $data;
        close $fh or die "Can't close file '$dst': $@";

        return 0;
    }

    if (!$force) {
        while ($answer !~ /^[OoAaSs]/) {
            print "$dst exists already\n";
            print '[o]verwrite, [a]ppend, [s]kip: ';
            chomp($answer = <STDIN>);
        }
    } else {
        $answer = 'overwrite';
    }

    if ($answer =~ /[Oo]v?e?r?w?r?i?t?e?/) {
        tell_user('INFO', "Preparing to overwrite existing file: $dst");
        open $fh, '>', $dst;
    } elsif ($answer =~ /[Aa]p?p?e?n?d?/) {
        tell_user('INFO', "Preparing to append to existing file: $dst");
        open $fh, '>>', $dst;
    } elsif ($answer =~ /[Ss]k?i?p?/) {
        tell_user('WARN', "Skipping write-to-file operation for $dst");
        return;
    }

    print $fh $data;
    tell_user('SUCCESS', "Done writing to $dst\n");
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

sub check_array {
    my ($haystack, $needle) = @_;

    if (grep /$needle/, @{$haystack}) {
        return 1;
    }

    return 0;
}

sub get_date {
    my ($sec, $min, $hour, $day, $mon, $year) = localtime();
    my $date = sprintf ("[%04d-%02d-%02d %02d:%02d:%02d] -> ", $year, $mon, $day, $hour, $min, $sec);
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