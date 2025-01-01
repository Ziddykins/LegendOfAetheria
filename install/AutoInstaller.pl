#!/usr/bin/env perl

use warnings;
use strict;
use autodie;
use diagnostics;

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
    SERVICES   => 4,
    APACHE     => 8,
    ENABLES    => 16,
    COMPOSER   => 32,
    TEMPLATES  => 64,
    PHP        => 128,
    SQL        => 256,
    CRONS      => 512,
    CERTS      => 1024,
    HOSTNAME   => 2048,
    CLEANUP    => 4096,
    PERMS      => 8192,
};

use constant {
    CFG_R_MAIN   => 1,
    CFG_R_SQL    => 2,
    CFG_R_DOMAIN => 3,
    CFG_W_MAIN   => 4,
    CFG_W_SQL    => 5,
    CFG_W_DOMAIN => 6,
};

$|++;

*name   = *File::Find::name;
*dir    = *File::Find::dir;
*prune  = *File::Find::prune;

sub find_temp;
sub do_delete ($@);

my %def;         # default/example values, unless pulled from users' existing cfg
my %cfg;         # the focused fqdn's config, a tied hash from Config::IniFiles
my %glb;         # hash which holds all other required config variables
my %ini;         # full configuration ini, all domains
my %sql;         # object containing constant sql table names
my %clr;         # color constants

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
%sql = %{$ini{sql_tables}};

if ($ENV{'USER'} ne 'root') {
    die $clr{red} . 'This script must be ran as root, not ',
        $clr{yellow}, $ENV{'USER'}, $clr{red}, '!', $clr{reset}, "\n";
}

$question = "Enter the FQDN where the game will be accessed (e.g. loa.example.com)";
$fqdn     = ask_user($question, '', 'input');

if ($ini{$fqdn}) {
    %cfg = %{$ini{$fqdn}};

    tell_user('INFO', "The FQDN '" . $cfg{fqdn} . "' has an existing configuration file");

    if (ask_user("Would you like to load the configurations?", 'yes', 'yesno')) {
        %cfg = %{$ini{$fqdn}};

        if ($cfg{step}) {
            if (ask_user("Would you like to continue from step $cfg{step} or start from the beginning?", '[s]tart over/[c]ontinue', 'input')) {
                $cfg{step} = 0;
            }
        }
    } else {
        if (ask_user("Are you sure? This will wipe the current config for this FQDN", 'yes', 'yesno')) {
            delete $ini{$fqdn};
            $ini{$fqdn} = {};
        } else {
            tell_user('WARN', "Continuing with loaded configuration");
        }
        %cfg = %{$ini{$fqdn}};
    }
} else {
    tell_user('WARN', 'No configuration for this FQDN, creating new entry');
    $ini{$fqdn} = {};
    handle_cfg({}, CFG_W_MAIN, $fqdn);
}

$cfg{fqdn} = $fqdn;

my $os = check_platform();
my $distro;

if ($os eq 'linux') {
    my $ver = `uname -v`;
    if ($ver =~ /(debian|ubuntu|kali)/i) {
        $distro = $1;
    } else {
        if (!ask_user("Unsupported distro, try anyway?", 'no', 'yesno')) {
            die "Unsupported distro, exiting\n";
        }
        $distro = "unsupported";
    }
    tell_user('INFO', "$distro found to be the current distribution");
    %def = %{$ini{lin_examples}};
} else {
    %def = %{$ini{win_examples}};
}

merge_hashes(\%cfg, \%def);
merge_hashes(\%cfg, \%glb);

$cfg{os} = $os;
$cfg{distro} = $distro;

handle_cfg(\%cfg, CFG_W_DOMAIN, $fqdn);

chomp(my $loc_check = `pwd`);
$loc_check =~ s/\/install//;

$question = "Enter the location of your webserver's config directory (e.g. /etc/apache2)";
$cfg{apache_directory} = ask_user($question, '/etc/apache2', 'input');

$question = "Please enter the path to where the game will reside (e.g. /var/www/html/example.com/loa)";
$cfg{web_root} = ask_user($question, $cfg{web_root} ? $cfg{web_root} : '/var/www/html/kali.local/loa', 'input');
$cfg{web_root} =~ s/\/$//;

$cfg{template_dir} = $cfg{web_root} . "/install/templates";
$cfg{scripts_dir}  = $cfg{web_root} . "/install/scripts";
$cfg{admin_email}  = "webmaster\@$cfg{fqdn}";


$cfg{setup_log}    = $cfg{web_root} . '/setup.log';
$cfg{step}         = 1;

$cfg{virthost_conf_file}    = "$cfg{apache_directory}/sites-available/$cfg{fqdn}.conf";
$cfg{virthost_conf_file_ssl} = "$cfg{apache_directory}/sites-available/ssl-$cfg{fqdn}.conf";

if ($loc_check ne $cfg{web_root}) {
    my $error = "Setup has determined the files are not in the correct place,\n" .
                " or you're not in the correct folder. Please move the contents\n" .
                "of the legendofaetheria folder to your webroot, and make sure you're\n" .
                "the 'install' directory when you run this script.\n\n" .
                "Specified webroot directory: $cfg{web_root}\n" .
                "Current location           : $loc_check\n";
    die $error;
}

if ($cfg{step} < SOFTWARE) {
    if (ask_user("Install required software?", 'yes', 'yesno')) {
        step_install_software();
        $cfg{step} += SOFTWARE;
        handle_cfg(\%cfg, CFG_W_DOMAIN, $fqdn);
    }
}

if ($cfg{step} < SERVICES) {
    if (ask_user("Start all required services now?", 'yes', 'yesno')) {
        step_start_services();
        $cfg{step} += SERVICES;
        handle_cfg(\%cfg, CFG_W_DOMAIN, $fqdn);
    }
}

if ($cfg{step} < SQL) {
    if (ask_user("Go through SQL configurations?", 'yes', 'yesno')) {
        step_sql_configure();
        $cfg{step} += SQL;
    }
}

# CONFIG - XAMPP configuration #
my $XAMPP_INSTALLER_BIN  = 'https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.3.4/xampp-windows-x64-8.3.4-0-VS16-installer.exe';
my $XAMPP_INSTALLER_ARGS = '--mode unattended --enabled-components xampp_server,xampp_apache,xampp_mysql,xampp_program_languages,xampp_php,xampp_perl,xampp_tools';
my $XAMPP_MARIADB_CHPW   = 'mysqladmin.exe -u root password';

# CONFIG - Composer #
$cfg{composer_runas} = 'www-data';
$cfg{apache_runas}   = 'www-data';
handle_cfg(\%cfg, CFG_W_DOMAIN, $fqdn);

# CONFIG - PHP #
$cfg{php_ini} = 'unset';

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
handle_cfg(\%cfg, CFG_W_DOMAIN, $fqdn);

# NOCONFIG - Template Files #
my $VIRTHOST_SSL_TEMPLATE = "$cfg{template_dir}/virtual_host_ssl.template";
my $VIRTHOST_TEMPLATE     = "$cfg{template_dir}/virtual_host.template";
my $HTACCESS_TEMPLATE     = "$cfg{template_dir}/htaccess.template";
my $CRONTAB_TEMPLATE      = "$cfg{template_dir}/crontab.template";
my $ENV_TEMPLATE          = "$cfg{template_dir}/env.template";
my $SQL_TEMPLATE          = "$cfg{template_dir}/sql.template";
my $PHP_TEMPLATE          = "$cfg{template_dir}/php.template";

# NOCONFIG - Replacements for Templates
my @replacements = (
    "###REPL_PHP_BINARY###%%%$cfg{php_binary}",

    "###REPL_WEB_ROOT###%%%$cfg{web_root}",
    "###REPL_WEB_ADMIN_EMAIL###%%%$cfg{admin_email}",
    "###REPL_WEB_FQDN###%%%$cfg{fqdn}",
    "###REPL_WEB_DOCROOT###%%%$cfg{web_root}",
    "###REPL_WEB_SSL_FULLCER###%%%$cfg{ssl_fullcer}",
    "###REPL_WEB_SSL_PRIVKEY###%%%$cfg{ssl_privkey}",

    "###REPL_SQL_DB###%%%$cfg{sql_database}",
    "###REPL_SQL_USER###%%%$cfg{sql_username}",
    "###REPL_SQL_PASS###%%%$cfg{sql_password}",
    "###REPL_SQL_HOST###%%%$cfg{sql_host}",
    "###REPL_SQL_PORT###%%%$cfg{sql_port}",

    "###REPL_SQL_TBL_ACCOUNTS###%%%$sql{tbl_accounts}",
    "###REPL_SQL_TBL_CHARACTERS###%%%$sql{tbl_characters}",
    "###REPL_SQL_TBL_FAMILIARS###%%%$sql{tbl_familiars}",
    "###REPL_SQL_TBL_FRIENDS###%%%$sql{tbl_friends}",
    "###REPL_SQL_TBL_MAIL###%%%$sql{tbl_mail}",
    "###REPL_SQL_TBL_MONSTERS###%%%$sql{tbl_monsters}",
    "###REPL_SQL_USER###%%%$cfg{sql_username}",
    "###REPL_SQL_TBL_LOGS###%%%$sql{tbl_logs}",
    "###REPL_SQL_TBL_GLOBALS###%%%$sql{tbl_globals}",

    "###REPL_OPENAI_APIKEY###%%%$OPENAI_APIKEY",

    "###REPL_PHP_COOKIEDOMAIN###%%\%$cfg{fqdn}"
);

## NO MORE CONFIGURATION BEYOND THIS POINT ##

if (check_array(\@ARGV, "--revert-system")) {
    clean_up('revert');
}

if ($cfg{step} > 0) {
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
    step_generate_templates();
}

if (ask_user("Process generated templates?", 'yes', 'yesno')) {
    step_process_templates();
}

#if (ask_user("Update system hostname to match FQDN?")) {
#    step_update_hostname() if !$cfg{step} == hostname;
#}

if (ask_user("Perform necessary apache updates?", 'yes', 'yesno')) {
    step_apache_config() if $cfg{step} < APACHE;
}

if (ask_user("Enable the required Apache conf/mods/sites?", 'yes', 'yesno')) {
    step_apache_enables() if $cfg{step} < ENABLES;
}

if (ask_user("Update PHP configurations? (security, performance)", 'yes', 'yesno')) {
    step_update_php_confs() if $cfg{step} < PHP;
}

if (ask_user("Fix all webserver permissions?", 'yes', 'yesno')) {
    step_fix_permissions() if $cfg{step} < PERMS;
}

if (ask_user("Run composer to download required dependencies?", 'yes', 'yesno')) {
    step_composer_pull() if $cfg{step} < COMPOSER;
}

if (ask_user("Start all services?", 'yes', 'yesno')) {
   step_start_services() if $cfg{step} < SERVICES;
}

if (ask_user("Clean up temp files?", 'yes', 'yesno')) {
    clean_up() if $cfg{step} < CLEANUP;
}

#Step: software
sub step_install_software {
    my ($apt_output, $sury_output);

    if (-e '/etc/apt/sources.list.d/php.list' || -e '/etc/apt/sources.list.d/ondrej-ubuntu-php-noble.sources') {
        tell_user('INFO', 'Sury repo entries already present');
    } else {
        my $cmd;
        tell_user('INFO', 'Sury PHP repositories not found, adding necessary entries');

        if ($distro eq 'ubuntu') {
            $cmd = "sh -c $cfg{scripts_dir}/sury_setup_ubnt.sh";
        } elsif ($distro =~ /debian|kali/i) {
            $cmd = "sh -c $cfg{scripts_dir}/sury_setup_deb.sh";
        } elsif ($os eq 'windows') {
            # TODO: implement windows setup
        }
        tell_user('SYSTEM', `$cmd`);
    }

    tell_user('INFO',   'Updating system packages');

    if (!$cfg{php_version}) {
        tell_user('WARN', "PHP version not specified, attempting to find it...\n");

        if ($os eq 'linux') {
            tell_user('SYSTEM', `apt update`);
            chomp(my $ver_output = `php --version | head -n1`);

            if ($ver_output =~ /PHP (\d+\.\d+)/) {
                $cfg{php_version} = $1;
            }

            if ($cfg{php_version}) {
                tell_user('SUCCESS', "Found PHP version $cfg{php_version}");
            } else {
                tell_user('ERROR', 'Failed to find PHP version');
                die "Exiting on failure...\n";
            }
        }
    } else {
        tell_user('ERROR', "Invalid or unsupported PHP version set in the installer\n" .
                           "supported PHP versions: 7.0 - 8.4\n\n");
        if (ask_user('Try with version 8.3?', 'yes', 'yesno')) {
            $cfg{php_version} = "8.3";
        }
    }

    my @packages = (
        "cron",
        "php$cfg{php_version}",
        "php$cfg{php_version}-cli",
        "php$cfg{php_version}-common",
        "php$cfg{php_version}-curl",
        "php$cfg{php_version}-dev",
        "php$cfg{php_version}-fpm",
        "php$cfg{php_version}-mbstring",
        "php$cfg{php_version}-mysql",
        "php$cfg{php_version}-xml",
        "mariadb-server",
        "apache2",
        "letsencrypt",
        "python-is-python3",
        "python3-certbot-apache",
        "libapache2-mod-php",
        "libapache2-mod-php$cfg{php_version}",
        "composer",
    );

    tell_user('INFO', 'Installing ' . @packages . ' packages\n');
    my $apt_cmd = 'apt install -y ' . join (' ', @packages) . ' 2>&1';
    tell_user('SYSTEM', `$apt_cmd` . "\n");
}

sub step_sql_configure {
    $cfg{sql_username} = 'user_loa';
    $cfg{sql_password} = gen_random(15);
    $cfg{sql_database} = 'db_loa';
    $cfg{sql_host}     = '127.0.0.1';
    $cfg{sql_port}     = 3306;

    $question = "Please enter the location of your MySQL configuration file (e.g. /etc/mysql/mariadb/mariadb.conf.d/50-server.conf)";
    $cfg{sql_config_file} = ask_user($question, $cfg{sql_config_file}, 'input');

    $question = "Please enter the SQL username to be used for the database";
    $cfg{sql_username} = ask_user($question, $cfg{sql_username}, 'input');

    $question = "Please enter the SQL password to be used for the database";
    $cfg{sql_password} = ask_user($question, $cfg{sql_password}, 'input');

    $question = "Please enter the SQL database to be used for the game";
    $cfg{sql_database} = ask_user($question, $cfg{sql_database}, 'input');

    $question = "Please enter the SQL host to be used for the database";
    $cfg{sql_host} = ask_user($question, $cfg{sql_host}, 'input');

    $question = "Please enter the SQL port to be used for the database";
    $cfg{sql_port} = ask_user($question, $cfg{sql_port}, 'input');
    handle_cfg(\%cfg, CFG_W_DOMAIN, $fqdn);
}

# Step: hostname
sub step_update_hostname {
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
sub step_apache_config {
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
sub step_fix_permissions {
    `find $cfg{web_root} -type f -exec chmod 644 {} + 2>&1`;
    `find $cfg{web_root} -type d -exec chmod 755 {} + 2>&1`;
    `chown -R $cfg{apache_runas}:$cfg{apache_runas} $cfg{web_root} 2>&1`;
    tell_user('SUCCESS', 'Permissions fixed!');
}

# Step: step_apache_enables
sub step_apache_enables {
    my $success = 0;
    tell_user('INFO', 'Enabling required Apache configurations, sites and modules');

    my $conf_output = `a2enconf php$cfg{php_version}-fpm 2>&1`;
    $success = $? == 0 ? 1 : 0;

    my $mods_output = `a2enmod php$cfg{php_version} rewrite setenvif ssl 2>&1`;
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
sub step_update_php_confs {
    if ($os eq 'linux') {
        my @keys = qw/expose_php error_reporting display_errors display_startup_errors allow_url_fopen allow_url_include session.gc_maxlifetime disable_functions session.cookie_domain session.use_strict_mode session.use_cookies session.cookie_lifetime session.cookie_secure session.cookie_httponly session.cookie_samesite session.cache_expire/;
        my $ini_contents;
        my $template_file;

        if ($cfg{php_ini} eq 'unset') {
            $cfg{php_ini} = "/etc/php/$cfg{php_version}/apache2/php.ini";
        }

        {
            local $/;
            open my $t_fh, '<', "$cfg{template_dir}/php.template"
		        or die "Couldn't open template file for read: $!\n";
            $template_file = <$t_fh>;
            close $t_fh;
        }

        {
            local $/;
            open my $fh, '<', $cfg{php_ini}
                or die "Couldn't open '$cfg{php_ini}' for read: $!\n";
            $ini_contents = <$fh>;
            close $fh;
        }

        foreach my $key (@keys) {
            $ini_contents =~ s/$key ?=.*?$[\r\n]//;
        }

        file_write('install\templates\php.template', $cfg{php_ini}, 'file', 1);
    } else {
        $cfg{php_ini} = 'C:\xampp\php\php.ini';
    }
}

# Step: composer
sub step_composer_pull {
    if (ask_user("Composer is going to download/install these as $cfg{composer_runas} - continue?", 'yes', 'yesno')) {
        my $cmd = "sudo -u $cfg{composer_runas} composer --working-dir \"$cfg{web_root}\" install";
        my $cmd_output = `$cmd 2>&1`;
        tell_user('SYSTEM', $cmd_output);
    }
}

# Step: Template imports
sub step_generate_templates {
    my $copy_output;
    my $cron_contents = '';
    my $fh_cron;
    my %templates;

    `sed -i 's/bind-address.*/bind-address = $cfg{sql_host}/' $cfg{sql_config_file}`;

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

sub step_process_templates {
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

    tell_user('INFO', "Copying over $ENV_TEMPLATE.ready to $cfg{web_root}/.env");
    file_write("$cfg{web_root}/.env", "$ENV_TEMPLATE.ready", 'file');

    tell_user('INFO', "Copying over $VIRTHOST_TEMPLATE.ready to $cfg{virthost_conf_file}");
    file_write($cfg{virthost_conf_file}, "$VIRTHOST_TEMPLATE.ready", 'file');

    if ($cfg{ssl_enabled}) {
        tell_user('INFO', "Copying over $VIRTHOST_SSL_TEMPLATE.ready to $cfg{virthost_conf_file_ssl}");
        file_write($cfg{virthost_conf_file_a}, "$VIRTHOST_SSL_TEMPLATE.ready", 'file');
    }

    tell_user('INFO', "Copying over $HTACCESS_TEMPLATE.ready to $cfg{web_root}/.htaccess");
    file_write("$cfg{web_root}/.htaccess", "$HTACCESS_TEMPLATE.ready", 'file');

    tell_user('INFO', "Copying over $CRONTAB_TEMPLATE to $cfg{crontab_dir}/$cfg{apache_runas}");

    if (!-d $cfg{crontab_dir}) {
        make_path($cfg{crontab_dir});
    }

    if (-e "$cfg{crontab_dir}/$cfg{apache_runas}") {
        unlink("$cfg{crontab_dir}/$cfg{apache_runas}");
    }

    file_write("$cfg{crontab_dir}/$cfg{apache_runas}", "$CRONTAB_TEMPLATE.ready");
    tell_user('INFO', "Updating permissions on new crontab to $cfg{apache_runas}:crontab");
    `chown $cfg{apache_runas}:crontab $cfg{crontab_dir}/$cfg{apache_runas}`;
    tell_user('SUCCESS', "All template files have been applied");
}

#Step: start services
sub step_start_services {
    my @services = ("mariadb",  "php$cfg{php_version}-fpm",  "apache2");

    foreach my $service (@services) {
        tell_user('INFO', "Starting $service");
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
    $mode //= "none";

    if ($mode eq 'revert') {
        my $cmd;

        tell_user("INFO", "Searching for temporary and generated files to clean up in $cfg{web_root}");
        File::Find::find({wanted => \&find_temp}, "$cfg{web_root}/");

        tell_user("INFO", "Searching for temporary and generated files to clean up in $cfg{apache_directory}");
        
        $cmd = "a2dissite $cfg{virthost_conf_file}";
        tell_user("SYSTEM", `$cmd`);

        if ($cfg{ssl_enabled}) {
            $cmd = "a2dissite $cfg{virthost_conf_file_ssl}";
            tell_user("SYSTEM", `$cmd`);
        }

        File::Find::find({wanted => \&find_temp}, "$cfg{apache_directory}/");

        tell_user("INFO", "Dropping sury repos from sources.list.d");
        File::Find::find({wanted => \&find_temp}, '/etc/apt/sources.list.d/');

        tell_user("INFO", "Dropping database $cfg{sql_database} and dropping user $cfg{sql_username}");
        $cmd = "mysql -u root -e 'DROP DATABASE $cfg{sql_database}; DROP USER $cfg{sql_username};'";
        tell_user('SYSTEM', `$cmd`);

        tell_user("INFO", "Removing our crontab at $cfg{crontab_dir}/$cfg{apache_runas}");
        unlink("$cfg{crontab_dir}/$cfg{apache_runas}");
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
    $answer //= 'none';
    $type //= 'none';

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
    close $fh or die "Couldn't close file: $@\n";
}

sub ask_user {
    my ($prompt, $default, $type) = @_;
    my $date = get_date();
    $type //= 'input';
    print "$date $prompt\n";

    if ($type eq 'yesno') {
        print "[$clr{green}y$clr{reset}/$clr{red}n$clr{reset}]> ";
    } elsif ($type eq 'input') {
        if ($default) {
           print "[$clr{yellow}$default$clr{reset}]> ";
        } else {
            print "$clr{yellow}enter$clr{reset}> ";
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
    $message //= "<$clr{yellow}no message$clr{reset}>";

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

    if ($cfg{setup_log}) {
        open my $fh, '>>', $cfg{setup_log}
          or die "Couldn't open log file for append '$cfg{setup_log}': $!";

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
            $ini{$fqdn} = {};
            %{$ini{$fqdn}} = %t_hash;
            tied(%ini)->WriteConfig($cfg_file);
            tell_user('SUCCESS', "Added new webroot '$cfg_file'\n");
        }
    } elsif ($op eq CFG_W_MAIN) {
        tied(%ini)->WriteConfig($cfg_file);
    }
}

sub merge_hashes {
    my ($hash1, $hash2) = @_;

    foreach my $key (keys %$hash2) {
        if (exists $hash1->{$key} && ref $hash1->{$key} eq 'HASH' && ref $hash2->{$key} eq 'HASH') {
            $hash1->{$key} = merge_hashes($hash1->{$key}, $hash2->{$key});
        } elsif (exists $hash1->{$key} && $hash1->{$key} ne $hash2->{$key}) {
            next;
        } else {
            $hash1->{$key} = $hash2->{$key};
        }
    }
}

# TODO:
# - Remove install folder after
# - Fix PHP ini not populating on template overwrite
#@values = $cfg->val('Section', 'Parameter');
