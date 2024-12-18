#!/usr/bin/env perl

use warnings;
use strict;
use autodie;

use Config::IniFiles;
use Data::Dumper;
use File::Path qw(make_path remove_tree);
use File::Find;
use File::Copy;

use vars qw/*name *dir *prune/;

# Steps
use constant {
    HOSTS      => 1,
    SOFTWARE   => 2,
    HOSTNAME   => 3,
    APACHE     => 4,
    ENABLES    => 5,
    COMPOSER   => 6,
    TEMPLATES  => 7,
    PHP        => 8,
    SQLIMPORT  => 9,
    CRONS      => 10,
    CERTS      => 11,
    SERVICES   => 12,
    CLEANUP    => 13,
    PERMS      => 14,
};

use constant {
    CFG_R_MAIN   => 1,
    CFG_R_SQL    => 2,
    CFG_R_DOMAIN => 3,
    CFG_W_MAIN   => 4,
    CFG_W_SQL    => 5,
    CFG_W_DOMAIN => 6,
};

*name   = *File::Find::name;
*dir    = *File::Find::dir;
*prune  = *File::Find::prune;

sub find_temp;
sub do_delete ($@);

my %defaults;    # default/example values, used mostly for questioning user
my %cfg;         # the focused fqdn's config, a tied hash from Config::IniFiles
my %clr;         # color constants
my %ini;         # full configuration ini
my %sql;         # object containing constant sql configurations
my $fqdn;        # fully qualified domain name to set up
my $question;    # current question to ask the user

my $cfg_file = 'config.ini';    # file which holds the scripts configuration ini

tie %ini, 'Config::IniFiles', (
    -file => $cfg_file,
    -default => 'all',
    -allowempty => 1,
    -nomultiline => 1,
);

%clr = %{$ini{colors}};

$question = "Enter the FQDN where the game will be accessed (e.g. loa.example.com)";
$fqdn     = ask_user($question, '', 'input');

$ini{$fqdn} = {};
$ini{$fqdn}{fqdn} = $fqdn;

if ($ini{$fqdn}) {
    %cfg = %{$ini{$fqdn}};
    tell_user('INFO', "The FQDN '$cfg{$fqdn}' has an existing configuration file");

    if (ask_user("Would you like to load the configurations?")) {
        %cfg = %{$ini{$fqdn}};

        if ($cfg{step}) {
            my $step_continue = ask_user("Would you like to continue from step $cfg{step} or start from the beginning?", '[s]tart over/[c]ontinue', 'input');
            if ($step_continue =~ /[sS](tart)?/) {
                $cfg{step} = 0;
            }
        }
    } else {
        if (ask_user("Are you sure? This will wipe the current config for this FQDN", 'yes', 'yesno')) {
            delete $ini{$fqdn};
            $ini{$fqdn} = {};
            %cfg = %{$ini{$fqdn}};
        } else {
            tell_user('WARN', "Continuing with loaded configuration");
        }
    }
} else {
    tell_user('WARN', 'No configuration for this FQDN, creating new entry');
    $ini{$fqdn} = {};
    write_config('null', CFG_W_DOMAIN, $fqdn);
}

my $os = check_platform();
my $distro;

if ($os eq 'linux') {
    my $ver = `uname -v`;
    if ($ver =~ /(debian|ubuntu)/i) {
        $distro = $1;
    } else {
        if (!ask_user("Unsupported distro, try anyway?", 'no', 'yesno')) {
            die "Unsupported distro, exiting\n";
        }
    }
    tell_user('INFO', "$distro found to be the current distribution");
    %defaults = %{$ini{lin_examples}};
} else {
    %defaults = %{$ini{win_examples}};
}

$cfg{os} = $os;
$cfg{distro} = $distro;

$ini{$cfg{fqdn}} = %cfg;

tied(%ini)->WriteConfig($cfg_file);

chomp(my $loc_check = `pwd`);
$loc_check =~ s/\/install//;

$question = "Enter the location of your webserver's config directory (e.g. /etc/apache2)";
$cfg{apache_directory} = ask_user($question, '/etc/apache2', 'input');

# NOCONFIG - See above
if ($os eq "linux") {
    $cfg{game_web_root}      = "/var/www/html";
    $cfg{php_binary}         = "/usr/bin/php";
    $cfg{sql_config_file}    = '/etc/mysql/mariadb.conf.d/50-server.cnf';
    $cfg{apache_directory}   = '/etc/apache2';
} elsif ($os eq "windows") {
    #$cfg{game_web_root} = "C:\\Program Files\\Apache Software Foundation\\Apache2.4";
    $cfg{game_web_root}      = 'C:\xampp\htdocs';
    $cfg{php_binary}         = 'C:\xampp\php\php.exe';
    $cfg{sql_config_file}    = 'C:\xampp\mysql\bin\my.ini';
    $cfg{apache_directory}   = 'C:\xampp\apache';
    $cfg{virthost_conf_file} = 'C:\xampp\apache\conf\httpd.conf';
    $cfg{ssl_fullcer}        = 'C:\xampp\apache\conf\ssl.crt\server.crt';
    $cfg{ssl_privkey}        = 'C:\xampp\apache\conf\ssl.key\server.key';
}

$question = "Please enter the path to where the game will reside (e.g. /var/www/html/example.com/loa)";
$cfg{game_web_root} = ask_user($question, $cfg{game_web_root}, 'input');
$cfg{game_web_root} =~ s/\/$//;

my $LOG_TO_FILE = 'setup.log';
my $GAME_TEMPLATE_DIR = $cfg{game_web_root} . "/install/templates";
my $GAME_SCRIPTS_DIR  = $cfg{game_web_root} . "install/scripts";
my $WEB_ADMIN_EMAIL   = "webmaster\@$cfg{$fqdn}";
my $CRONTAB_DIRECTORY = '/var/spool/cron/crontabs';

if ($loc_check ne $cfg{game_web_root}) {
    my $error = "Setup has determined the files are not in the correct place,\n" .
                " or you're not in the correct folder. Please move the contents\n" .
                "of the legendofaetheria folder to your webroot, and make sure you're\n" .
                "the 'install' directory when you run this script.\n\n" .
                "Specified webroot directory: $cfg{game_web_root}\n" .
                "Current location           : $loc_check\n";
    die $error;
}

if (ask_user("Install required software?", 'yes', 'yesno')) {
    install_software() if $cfg{step} < SOFTWARE;
    $cfg{step} = SOFTWARE;
}

# CONFIG - SQL Tables / Template Replacements #
$sql{tbl_characters} = 'tbl_characters';
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
my $SQL_HOST               = '127.0.2.1';
my $SQL_PORT               = 3306;

$question = "Please enter the location of your MySQL configuration file (e.g. /etc/mysql/mariadb/mariadb.conf.d/50-server.conf)";
$cfg{sql_config_file} = ask_user($question, $cfg{sql_config_file}, 'input');

$question = "Please enter the SQL username to be used for the database";
$SQL_USERNAME = ask_user($question, $SQL_USERNAME, 'input');

$question = "Please enter the SQL password to be used for the database";
$SQL_PASSWORD = ask_user($question, $SQL_PASSWORD, 'input');

$question = "Please enter the SQL database to be used for the game";
$SQL_DATABASE = ask_user($question, $SQL_DATABASE, 'input');

$question = "Please enter the SQL host to be used for the database";
$SQL_HOST = ask_user($question, $SQL_HOST, 'input');

$question = "Please enter the SQL port to be used for the database";
$SQL_PORT = ask_user($question, $SQL_PORT, 'input');

# CONFIG - XAMPP configuration #
my $XAMPP_INSTALLER_BIN  = 'https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.3.4/xampp-windows-x64-8.3.4-0-VS16-installer.exe';
my $XAMPP_INSTALLER_ARGS = '--mode unattended --enabled-components xampp_server,xampp_apache,xampp_mysql,xampp_program_languages,xampp_php,xampp_perl,xampp_tools';
my $XAMPP_MARIADB_CHPW   = 'mysqladmin.exe -u root password';

# CONFIG - Composer #
my $COMPOSER_RUNAS = 'www-data';
my $APACHE_RUNAS   = 'www-data';

# CONFIG - PHP #
my $PHP_VERSION;
my $PHP_INI_FILE = 'unset';

if (`whereis php` =~ /php: (\/?.*?\/bin\/.*?\/?php\d?\.?\d?)/) {
    my $found_location = $1;

    $question = "PHP was found on this system at '$found_location' - is this the correct" .
                "location to the PHP binary you want to use?";

    if (ask_user($question, 'yes', 'yesno')) {
        $cfg{php_binary} = $found_location;
    } else {
        $question = "Please enter the location of your PHP binary (e.g. /usr/bin/php7.4)";
        $cfg{php_binary} = ask_user($question, $cfg{php_binary}, 'input');
    }
}

# CONFIG - OpenAI #
my $OPENAI_ENABLE = ask_user("Enable OpenAI features? API key required", 'no', 'yesno');
my $OPENAI_APIKEY = 'unset';

if ($OPENAI_ENABLE) {
    $question = "OpenAI configuration is enabled; please enter your OpenAI API key";
    $OPENAI_APIKEY = ask_user($question, '', 'input');
}

# NOCONFIG - Template Files #
my $VIRTHOST_SSL_TEMPLATE = "$GAME_TEMPLATE_DIR/virtual_host_ssl.template";
my $VIRTHOST_TEMPLATE     = "$GAME_TEMPLATE_DIR/virtual_host.template";
my $HTACCESS_TEMPLATE     = "$GAME_TEMPLATE_DIR/htaccess.template";
my $CRONTAB_TEMPLATE      = "$GAME_TEMPLATE_DIR/crontab.template";
my $ENV_TEMPLATE          = "$GAME_TEMPLATE_DIR/env.template";
my $SQL_TEMPLATE          = "$GAME_TEMPLATE_DIR/sql.template";
my $PHP_TEMPLATE          = "$GAME_TEMPLATE_DIR/php.template";

# NOCONFIG - Hosts files #
my $WIN32_HOSTS_FILE = 'c:\windows\system32\drivers\etc\hosts';
my $LINUX_HOSTS_FILE = '/etc/hosts';

# NOCONFIG - Replacements for Templates
my @replacements = (
    "###REPL_PHP_BINARY###%%%$cfg{php_binary}",

    "###REPL_WEB_ROOT###%%%$cfg{game_web_root}",
    "###REPL_WEB_ADMIN_EMAIL###%%%$WEB_ADMIN_EMAIL",
    "###REPL_WEB_FQDN###%%%$cfg{fqdn}",
    "###REPL_WEB_DOCROOT###%%%$cfg{game_web_root}",
    "###REPL_WEB_SSL_FULLCER###%%%$cfg{ssl_fullcer}",
    "###REPL_WEB_SSL_PRIVKEY###%%%$cfg{ssl_privkey}",

    "###REPL_SQL_DB###%%%$SQL_DATABASE",
    "###REPL_SQL_USER###%%%$SQL_USERNAME",
    "###REPL_SQL_PASS###%%%$SQL_PASSWORD",
    "###REPL_SQL_HOST###%%%$SQL_HOST",
    "###REPL_SQL_PORT###%%%$SQL_PORT",

    "###REPL_SQL_TBL_ACCOUNTS###%%%$SQL_TBL_ACCOUNTS",
    "###REPL_SQL_TBL_CHARACTERS###%%%$sql{tbl_characters}",
    "###REPL_SQL_TBL_FAMILIARS###%%%$SQL_TBL_FAMILIARS",
    "###REPL_SQL_TBL_FRIENDS###%%%$SQL_TBL_FRIENDS",
    "###REPL_SQL_TBL_GLOBALS###%%%$SQL_TBL_GLOBALS",
    "###REPL_SQL_TBL_MAIL###%%%$SQL_TBL_MAIL",
    "###REPL_SQL_TBL_MONSTERS###%%%$SQL_TBL_MONSTERS",
    "###REPL_SQL_USER###%%%$SQL_USERNAME",
    "###REPL_SQL_TBL_LOGS###%%%$SQL_TBL_LOGS",
    "###REPL_SQL_TBL_GLOBALS###%%%$SQL_TBL_GLOBALS",

    "###REPL_OPENAI_APIKEY###%%%$OPENAI_APIKEY",

    "###REPL_PHP_COOKIEDOMAIN###%%%$cfg{fqdn}"
);

## NO MORE CONFIGURATION BEYOND THIS POINT ##

if (check_array(\@ARGV, "--revert-system")) {
    clean_up('revert');
}


if ($cfg{step}) {
    print "It looks like you have ran this script before; do you want to\n";
    print "[c]ontinue from where you left off, or [r]estart from\n";
    print "the beginning?\n[r]estart/[c]ontinue: ";
    chomp(my $answer = <STDIN>);

    if ($answer =~ /[rR](estart)?/) {
        clean_up();
        $cfg{step} = 0;
    }
}

if (ask_user(
        "Multiple variables in this script are marked with " .
        "'Template Replacements' - Make sure these are filled out properly before " .
        "continuing\nContinue and generate templates?", 'yes', 'yesno'
    )) {
    generate_templates();
    `touch $cfg{game_web_root}/.loa.step.templates.gen`;
}

if (ask_user("Process generated templates?", 'yes', 'yesno')) {
    process_templates();
    `touch $cfg{game_web_root}/.loa.step.templates.process`;
}

#if (ask_user("Update system hostname to match FQDN?")) {
#    update_hostname() if !$cfg{step} == hostname;
#   `touch $cfg{game_web_root}/.loa.step.hostname`;
#}

if (ask_user("Perform necessary apache updates?", 'yes', 'yesno')) {
    apache_config() if $cfg{step} < APACHE;
    `touch $cfg{game_web_root}/.loa.step.apache`;
}

if (ask_user("Enable the required Apache conf/mods/sites?", 'yes', 'yesno')) {
    apache_enables() if $cfg{step} < ENABLES;
    `touch $cfg{game_web_root}/.loa.step.apache_enables`;
}

if (ask_user("Update PHP configurations? (security, performance)", 'yes', 'yesno')) {
    update_php_confs() if $cfg{step} < PHP;
    `touch $cfg{game_web_root}/.loa.step.php`;
}

if (ask_user("Run composer to download required dependencies?", 'yes', 'yesno')) {
    composer_pull() if $cfg{step} < COMPOSER;
    `touch $cfg{game_web_root}/.loa.step.composer`;
}

if (ask_user("Start all services?", 'yes', 'yesno')) {
   start_services() if $cfg{step} < SERVICES;
   `touch $cfg{game_web_root}/.loa.step.services`;
}

if (ask_user("Fix all webserver permissions?", 'yes', 'yesno')) {
    fix_permissions() if $cfg{step} < PERMISSIONS;
    `touch $cfg{game_web_root}/.loa.step.permissions`;
}

if (ask_user("Clean up temp files?", 'yes', 'yesno')) {
    clean_up() if $cfg{step} < CLEANUP;
}

#Step: software
sub install_software {
    my ($apt_output, $sury_output);

    if (-e '/etc/apt/sources.list.d/php.list' || -e '/etc/apt/sources.list.d/ondrej-ubuntu-php-noble.sources') {
        tell_user('INFO', 'Sury repo entries already present');
    } else {
        my $cmd;
        tell_user('INFO', 'Sury PHP repositories not found, adding necessary entries');

        if ($distro eq 'ubuntu') {
            $cmd = "bash $GAME_SCRIPTS_DIR/sury_setup_ubnt.sh";
        } elsif ($distro eq 'debian') {
            $cmd = "bash $GAME_SCRIPTS_DIR/sury_setup_deb.sh";
        } elsif ($os eq 'windows') {
            # TODO: implement windows setup
        }
        tell_user('SYSTEM', `$cmd; apt install php -y`);
    }

    tell_user('INFO',   'Updating system packages');

    if (!$PHP_VERSION) {
        tell_user('WARN', "PHP version not specified, attempting to find it...\n");

        if ($os eq 'linux') {
            chomp(my $ver_output = `php --version | head -n1`);

            if ($ver_output =~ /PHP (\d+\.\d+)/) {
                $PHP_VERSION = $1;
            }

            if ($PHP_VERSION) {
                tell_user('SUCCESS', "Found PHP version $PHP_VERSION");
            } else {
                tell_user('ERROR', 'Failed to find PHP version');
                die "Exiting on failure...\n";
            }
        }
    } else {
        die "Invalid or unsupported PHP version set in the installer\n" .
            "supported PHP versions: 7.0 - 8.4\n\n";
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
    tell_user('SYSTEM', `$apt_cmd` . "\n");
}

# Step: hostname
sub update_hostname {
    my $output = `hostnamectl set-hostname $cfg{fqdn} 2>&1 | grep -v Hint`;
    $output .= `hostnamectl set-hostname $cfg{fqdn} --pretty 2>&1 | grep -v Hint`;
    chomp (my $hostname = `hostname -f`);

    if ($hostname eq $cfg{fqdn}) {
        tell_user('SUCCESS',
                "Hostname for the system has been successfully set\n" .
                "Please reboot after\n\tOutput if any: $output\n");
    } else {
        tell_user('ERROR', "Something went wrong setting the hostname\nOutput below:\n\t$output");

        if (!ask_user('Continue anyway?', 'yes', 'yesno')) {
            die "Errors occured during hostname configuration - halting";
        }
    }
}

# Step: apache
sub apache_config {
    if (ask_user("Do you want to redirect traffic from http:80 to "
        . "https:443? A valid certificate needs to be set in the "
        . "script configuration!\n- Currently set -\n"
        . "Certificate: $cfg{ssl_fullcer}\n"
        . "Private Key: $cfg{ssl_privkey}\n"
        , 'yes', 'yesno')) {

        tell_user('INFO',   'Enabling mod_rewrite if it isn\'t already');
        tell_user('SYSTEM', `a2enmod rewrite 2>&1`);

        tell_user('INFO', "Enabling rewrite directives in $cfg{virthost_conf_file}");
        `sed -i 's/# REM //' $cfg{virthost_conf_file}`;
    }
}

# Step: Fix permissions
sub fix_permissions {
    `find $cfg{game_web_root} -type f -exec chmod 644 {} + 2>&1`;
    `find $cfg{game_web_root} -type d -exec chmod 755 {} + 2>&1`;
    `chown -R $APACHE_RUNAS:$APACHE_RUNAS $cfg{game_web_root} 2>&1`;
    tell_user('SUCCESS', 'Permissions fixed!');
}

# Step: apache_enables
sub apache_enables {
    my $success = 0;
    tell_user('INFO', 'Enabling required Apache configurations, sites and modules');

    my $conf_output = `a2enconf php$PHP_VERSION-fpm 2>&1`;
    $success = $? == 0 ? 1 : 0;

    my $mods_output = `a2enmod php$PHP_VERSION rewrite setenvif ssl 2>&1`;
    $success = $? == 0 ? 1 : 0;

    my $sites_output = `a2ensite $cfg{fqdn}.conf 2>&1`;
    $success = $? == 0 ? 1 : 0;

    my $sites_ssl_output = `a2ensite ssl-$cfg{fqdn}.conf 2>&1`;
    $success = !!!$? ? !0 : 0; #lol job security

    tell_user('SYSTEM', "          conf result: $conf_output");
    tell_user('SYSTEM', "          mods result: $mods_output");
    tell_user('SYSTEM', "site (non-ssl) result: $sites_output");
    tell_user('SYSTEM', "    site (ssl) result: $sites_ssl_output");

    if ($success) {
        tell_user('SUCCESS', "Apache configuration completed");
    } else {
        tell_user('ERROR', "There were errors - See above output\n");
        if (!ask_user('Continue?', 'no', 'yesno')) {
            die "Quitting at user request\n";
        }
    }
}

#Step: PHP configurations
sub update_php_confs {
    if ($os eq 'linux') {
        my @keys = qw/expose_php error_reporting display_errors display_startup_errors allow_url_fopen allow_url_include session.gc_maxlifetime disable_functions session.cookie_domain session.use_strict_mode session.use_cookies session.cookie_lifetime session.cookie_secure session.cookie_httponly session.cookie_samesite session.cache_expire/;
        my $ini_contents;
        my $template_file;

        if ($PHP_INI_FILE eq 'unset') {
            $PHP_INI_FILE = "/etc/php/$PHP_VERSION/apache2/php.ini";
        }

        {
            local $/;
            open my $t_fh, '<', "$GAME_TEMPLATE_DIR/php.template"
		        or die "Couldn't open template file for read: $!\n";
            $template_file = <$t_fh>;
            close $t_fh;
        }

        {
            local $/;
            open my $fh, '<', $PHP_INI_FILE
                or die "Couldn't open '$PHP_INI_FILE' for read: $!\n";
            $ini_contents = <$fh>;
            close $fh;
        }

        foreach my $key (@keys) {
            $ini_contents =~ s/$key ?=.*?$[\r\n]//;
        }

        file_write('install\templates\php.template', $PHP_INI_FILE, 'file', 1);
    } else {
        $PHP_INI_FILE = 'C:\xampp\php\php.ini';
    }
}

# Step: composer
sub composer_pull {
    if (ask_user("Composer is going to download/install these as $COMPOSER_RUNAS - continue?", 'yes', 'yesno')) {
        my $cmd = "sudo -u $COMPOSER_RUNAS composer --working-dir \"$cfg{game_web_root}\" install";
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

    `sed -i 's/bind-address.*/bind-address = $SQL_HOST/' $cfg{sql_config_file}`;

    # key = in file, value = out file
    $templates{$ENV_TEMPLATE}          = "$ENV_TEMPLATE.ready";
    $templates{$HTACCESS_TEMPLATE}     = "$HTACCESS_TEMPLATE.ready";
    $templates{$SQL_TEMPLATE}          = "$SQL_TEMPLATE.ready";
    $templates{$CRONTAB_TEMPLATE}      = "$CRONTAB_TEMPLATE.ready";
    $templates{$VIRTHOST_SSL_TEMPLATE} = "$VIRTHOST_SSL_TEMPLATE.ready";
    $templates{$VIRTHOST_TEMPLATE}     = "$VIRTHOST_TEMPLATE.ready";
    $templates{$PHP_TEMPLATE}          = "$PHP_TEMPLATE.ready";

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

    tell_user('INFO', "Copying over $ENV_TEMPLATE.ready to $cfg{game_web_root}/.env");
    file_write("$cfg{game_web_root}/.env", "$ENV_TEMPLATE.ready", 'file');

    tell_user('INFO', "Copying over $VIRTHOST_TEMPLATE.ready to $cfg{virthost_conf_file}");
    file_write($cfg{virthost_conf_file}, "$VIRTHOST_TEMPLATE.ready", 'file');

    if ($cfg{ssl_enabled}) {
        tell_user('INFO', "Copying over $VIRTHOST_SSL_TEMPLATE.ready to $cfg{virthost_conf_file_ssl}");
        file_write($cfg{virthost_conf_file_ssl}, "$VIRTHOST_SSL_TEMPLATE.ready", 'file');
    }

    tell_user('INFO', "Copying over $HTACCESS_TEMPLATE.ready to $cfg{game_web_root}/.htaccess");
    file_write("$cfg{game_web_root}/.htaccess", "$HTACCESS_TEMPLATE.ready", 'file');

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
        tell_user("INFO", "Searching for temporary and generated files to clean up in $cfg{game_web_root}");
        File::Find::find({wanted => \&find_temp}, "$cfg{game_web_root}/");

        tell_user("INFO", "Searching for temporary and generated files to clean up in $cfg{apache_directory}");
        `a2dissite $cfg{virthost_conf_file}`;

        if ($cfg{ssl_enabled}) {
            `a2dissite $cfg{virthost_conf_file_ssl}`;
        }

        File::Find::find({wanted => \&find_temp}, "$cfg{apache_directory}/");

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
        "ssl-$cfg{fqdn}.conf\$",
        "$cfg{fqdn}.conf\$",
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
    my ($fh, $data, $answer);

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
%{$ini{$fqdn}} = %cfg;
tied(%ini)->WriteConfig($cfg_file);
    if (!-e $dst) {
        tell_user('INFO', "File '$dst' doesn't exist already, writing new file");

        open $fh, '>', $dst or die "Can't open file '$dst' for write: $@\n";
            print $fh $data;
        close $fh or die "Can't close file '$dst': $@";

        return 0;
    }

    if (!$force) {
        while ($answer !~ /^[OoAaSs](verwrite|kip|ppend)?/) {
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
    my ($prompt, $default, $type) = @_;
    my $date = get_date();

    print "$date $prompt\n";

    if ($type eq 'yesno') {
        print "\n\tChoice[$clr{green}y$clr{reset}/$clr{red}n$clr{reset}]: ";
    } elsif ($type eq 'input') {
        if ($default) {
           print "[$default]> ";
        } else {
            print "enter> ";
        }
    }

    chomp (my $answer = <STDIN>);
    print "\n";

    if ($answer =~ /[Yy]e?s?/ && $type eq 'yesno') {
        return 1;
    } elsif ($answer =~ /[Nn]o?/ && $type eq 'yesno') {
        return 0;
    }

    if (($answer eq '' or !$answer) && $type eq 'input') {
        return $default;
    } else {
        return $answer;
    }

    return -1;
}

sub check_array {
    my ($haystack, $needle) = @_;

    if (grep /$needle/, @{$haystack}) {
        return 1;
    }

    return 0;
}

sub get_date {
    my ($sec, $min, $hour, $mday, $mon, $year, $wday, $yday) = localtime();
    my $date = sprintf ("[%2d-%02d-%02d %02d:%02d:%02d] -> ", $year % 100, $mon, $mday, $hour, $min, $sec);
    return $date;
}

sub tell_user {
    my ($severity, $message, $result) = @_;
    my $date   = get_date();
    my $prefix = "$date [";

    if ($severity eq 'INFO') {
        $prefix .= $clr{blue} . '?';
    } elsif ($severity eq 'WARN') {
        $prefix .= $clr{yellow} . '!';
    } elsif ($severity eq 'ERROR') {
        $prefix .= $clr{red} . '-';
    } elsif ($severity eq 'SUCCESS') {
        $prefix .= $clr{green} . '+';
    } elsif ($severity eq 'SYSTEM') {
        $message =~ s/[\r\n]/\n\t\t/g;
        $prefix .= $clr{grey} . '*';
    } else {
        print "$message\n";
    }

    $prefix .= "$clr{reset}] ";

    print "$prefix -> $message\n";

    if ($result) {
        $result =~ s/[\r\n]/\n\t=> /g;
        print $result;
    }

    if ($LOG_TO_FILE) {
        open my $fh, '>>', $LOG_TO_FILE
          or die "Couldn't open log file for append '$LOG_TO_FILE': $!";

        print $fh $message;

        close $fh or die "Couldn't close file: $!\n";
    }
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

sub handle_cfg {
    my ($href_section, $op, $fqdn) = @_;
    my %t_hash = %{$href_section};

    if ($op eq CFG_W_DOMAIN) {
        if (exists($ini{$fqdn})) {
            %{$ini{$fqdn}} = %t_hash;
            tied(%ini)->WriteConfig($cfg_file);
            tell_user('SUCCESS', "Updated domain '$fqdn' config");
        } else {
            %{$ini{$fqdn}} = {};
            %{$ini{$fqdn}} = %t_hash;
            tied(%ini)->WriteConfig($cfg_file);
            tell_user('SUCCESS', "Added new webroot '$cfg_file'\n");
        }
    }
}

# TODO:
# - Remove install folder after
# - Fix PHP ini not populating on template overwrite
#@values = $cfg->val('Section', 'Parameter');
