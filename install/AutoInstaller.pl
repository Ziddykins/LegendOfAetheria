#!/usr/bin/env perl

use warnings;
use strict;
use autodie;

our $VERSION = "2.6.4.28";

BEGIN {
    eval { require Config::IniFiles; };
    eval { require Term::ReadKey; };
};

use File::Path qw(make_path remove_tree);
use Getopt::Long;
use Data::Dumper;
use File::Find;
use File::Copy;
use Carp;

GetOptions(
    'f|fqdn=s' => \my $opt_fqdn,
    's|step=s' => \my $opt_step,
    'o|only'   => \my $opt_only,
    'l|list-steps' => sub { for (1 .. 11) { print "[$_: ", const_to_name($_), "] "; } exit; },
    'h|help'   => \&help,
    'v|version' => sub { print "AutoInstaller.pl v$VERSION\n"; exit; },
);

if ($opt_step =~ /[a-z]/i) {
    my $t_step = name_to_const(uc $opt_step);
    if ($t_step) {
        $opt_step = $t_step;
    } else {
        die "Invalid step name - Please either supply the step name or number (check --list-steps)\n";
    }
}

# ==================================[ cfg-start ]==================================== #
$| = 1;

use constant {
    FIRSTRUN  => 1,
    SOFTWARE  => 2,
    PHP       => 3,
    SERVICES  => 4,
    SQL       => 5,
    OPENAI    => 6,
    TEMPLATES => 7,
    APACHE    => 8,
    PERMS     => 9,
    COMPOSER  => 10,
    CLEANUP   => 11,
    CERTS     => 99, #TODO: implement :D
};

use constant {
    CFG_R_MAIN   => 1,
    CFG_R_DOMAIN => 3,
    CFG_W_MAIN   => 4,
    CFG_W_DOMAIN => 6,
};

use vars qw/*name *dir *prune/;
*name   = *File::Find::name;
*dir    = *File::Find::dir;
*prune  = *File::Find::prune;

sub find_temp;
sub do_delete($@);

my %def; # default/example values, unless pulled from users' existing cfg
my %cfg; # the focused fqdn's config, an un-tied hash from Config::IniFiles
my %glb; # hash which holds all other required config variables
my %ini; # full configuration ini, all domains
my %sql; # object containing constant sql table names
my %clr; # color constants

my $fqdn;     # fully qualified domain name to set up
$fqdn = $opt_fqdn if $opt_fqdn;

my $question; # current question to ask the user

my $cfg_file = 'config.ini';    # file which holds the scripts configuration ini

if (!-e $cfg_file && -e 'config.ini.default') {
    croak(
        'You need to have a config.ini file; either create one or rename the' .
        "'config.ini.default' file to 'config.ini' before continuing\n"
    );
}

tie %ini, 'Config::IniFiles', (
    -file => $cfg_file,
    -default => 'all',
    -allowempty => 1,
    -nomultiline => 1,
);

%clr = %{$ini{colors}};
%sql = %{$ini{sql_tables}};
# ===================================[ cfg-end ]===================================== #

# ==================================[ main-start ]=================================== #
if ($ENV{'USER'} ne 'root') {
    die $clr{red} . 'This script must be ran as root, not ',
        $clr{yellow}, $ENV{'USER'}, $clr{red}, '!', $clr{reset}, "\n";
}

get_sysinfo();

if (!$opt_step) {
    step_firstrun();
} else {
    %cfg = %{$ini{$opt_fqdn}};
    $cfg{step} = $opt_step;
}

if ($cfg{step} == SOFTWARE) {
    if (ask_user("Install required software?", 'y', 'yesno')) {
        step_install_software();
        step_webserver_configure();
    }
    next_step();
}

if ($cfg{step} == PHP) {
    if (ask_user("Go through PHP configurations?", 'y', 'yesno')) {
        step_php_configure();
    }
    next_step();
}

if ($cfg{step} == SERVICES) {
    if (ask_user("Start all required services now?", 'y', 'yesno')) {
        step_start_services();
    }
    next_step();
}

if ($cfg{step} == SQL) {
    if (ask_user("Go through SQL configurations?", 'y', 'yesno')) {
        step_sql_configure();
    }
    next_step();
}

if ($cfg{step} == OPENAI) {
    $cfg{openai_enable} = ask_user("Enable OpenAI features? API key required", 'n', 'yesno');
    $cfg{openai_apikey} = 'unset';

    if ($cfg{openai_enable}) {
        $question = "OpenAI configuration is enabled; please enter your OpenAI API key";
        $cfg{openai_apikey} = ask_user($question, '', 'input');
    }

    next_step();
}

if ($cfg{step} == TEMPLATES) {
    tell_user('INFO', "I'ma let you run some templates, but first I just needa know...");
    if (ask_user("Do you want to enable SSL?", 'y', 'yesno')) {
        step_vhost_ssl();
    }
    
    my @replacements = (
        "###REPL_PHP_BINARY###%%%$cfg{php_binary}",

        "###REPL_WEB_DOCROOT###%%%$cfg{web_root}",
        "###REPL_WEB_ADMIN_EMAIL###%%%$cfg{admin_email}",
        "###REPL_WEB_FQDN###%%%$cfg{fqdn}",
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
        "###REPL_SQL_TBL_BANNED###%%%$sql{tbl_banned}",
        "###REPL_SQL_TBL_GLOBALCHAT%%%$sql{tbl_globalchat}",
        

        "###REPL_OPENAI_APIKEY###%%%$cfg{openai_apikey}",

        "###REPL_PHP_COOKIEDOMAIN###%%%$cfg{fqdn}",

        "###REPL_APACHE_HTTP_PORT###%%%$cfg{apache_http_port}",
        "###REPL_APACHE_HTTPS_PORT###%%%$cfg{apache_https_port}",
    );

    if (ask_user("Generate templates?", 'y', 'yesno')) {
        step_generate_templates(\@replacements);
    }

    if (ask_user("Process generated templates?", 'y', 'yesno')) {
        step_process_templates();
    }

    next_step();
}

if ($cfg{step} == APACHE) {
    if (ask_user("Enable the required Apache conf/mods/sites?", 'y', 'yesno')) {
        step_apache_enables();
    }

    next_step();
}

if ($cfg{step} == PERMS) {
    if (ask_user("Fix all webserver permissions?", 'y', 'yesno')) {
        step_fix_permissions();
    }
    next_step();
}

if ($cfg{step} == COMPOSER) {
    if (ask_user("Run composer to download required dependencies?", 'y', 'yesno')) {
        step_composer_pull();
    }
    step_start_services();
    next_step();
}

if ($cfg{step} == CLEANUP) {
    if (ask_user("Clean up temp files?", 'y', 'yesno')) {
        clean_up();
    }
    next_step();
 }

print "$clr{green}All steps completed$clr{reset}\n";

# ===================================[ main-end ]=================================== #

# ==================================[ steps-start ]================================= #
sub step_firstrun {
    my $question = "Enter the FQDN where the game will be accessed (e.g. loa.example.com)";

    # If fqdn was supplied as an argument, skip the question
    $fqdn //= ask_user($question, '', 'input');

    if ($ini{$fqdn}) {
        %cfg = %{$ini{$fqdn}};

        tell_user('INFO', "The FQDN '" . $cfg{fqdn} . "' has an existing configuration file");

        if (ask_user("Would you like to load the configurations?", 'y', 'yesno')) {
            %cfg = %{$ini{$fqdn}};

            if ($cfg{step} > 1) {
                my $answer = 0;

                while ($answer != 1 and $answer != 2) {
                    $question = "What would you like to do:\n"
                                 . "1. Continue with previous config from step " . const_to_name($cfg{step}) . "\n"
                                 . "2. Continue with previous config from the beginning\n";
                    $answer = int(ask_user($question, 'Choice', 'input'));
                }

                if ($answer == 1) {
                    tell_user('INFO', 'Continuing script execution from previously ran install');
                    return;
                } else {
                    $cfg{step} = SOFTWARE;
                    tell_user('INFO', 'Restarting install from beginning step with previous configuration');
                    return;
                }
            }
        } else {
            if (ask_user("Are you sure? This will wipe the current config for this FQDN", 'y', 'yesno')) {
                delete $ini{$fqdn};
                $ini{$fqdn} = {};
            } else {
                tell_user('WARN', "Continuing with loaded configuration");
                %cfg = %{$ini{$fqdn}};
            }
        }
    } else {
        tell_user('WARN', 'No configuration for this FQDN, creating new entry');
        $ini{$fqdn} = {};
        handle_cfg({}, CFG_W_MAIN, $fqdn);
    }

    $cfg{fqdn} = $fqdn;



    if ($cfg{os} eq 'linux') {
        %def = %{$ini{lin_examples}};
    } else {
        %def = %{$ini{win_examples}};
    }

    merge_hashes(\%cfg, \%def);
    merge_hashes(\%cfg, \%glb);

    chomp(my $loc_check = `pwd`);
    $loc_check =~ s/\/install//;

    $cfg{web_root} = ask_user("Please enter the location where the game will be served from", $def{web_root}, 'input');

    if ($loc_check ne $cfg{web_root}) {
        my $error = "Setup has determined the files are not in the correct place,\n" .
                    " or you're not in the correct folder. Please move the contents\n" .
                    "of the legendofaetheria folder to your webroot, and make sure you're\n" .
                    "the 'install' directory when you run this script.\n\n" .
                    "Specified webroot directory: $cfg{web_root}\n" .
                    "Current location           : \e[31m$loc_check\e[0m\n";
        croak($error);
    }

    $cfg{template_dir} = $cfg{web_root} . '/install/templates';
    $cfg{scripts_dir}  = $cfg{web_root} . '/install/scripts';
    $cfg{admin_email}  = "webmaster\@$cfg{fqdn}";
    $cfg{setup_log}    = $cfg{web_root} . '/install/setup.log';

    $cfg{virthost_ssl_template} = "$cfg{template_dir}/virtual_host_ssl.template";
    $cfg{virthost_template}     = "$cfg{template_dir}/virtual_host.template";
    $cfg{htaccess_template}     = "$cfg{template_dir}/htaccess.template";
    $cfg{crontab_template}      = "$cfg{template_dir}/crontab.template";
    $cfg{env_template}          = "$cfg{template_dir}/env.template";
    $cfg{sql_template}          = "$cfg{template_dir}/sql.template";
    $cfg{php_template}          = "$cfg{template_dir}/php.template";
    $cfg{constants_template}    = "$cfg{template_dir}/constants.template";
    $cfg{php_fpm}               = 0;
    next_step();

    return 0;
}

sub step_install_software {
    if ($cfg{pm_cmd} =~ /^apt/) {
        if (-e '/etc/apt/sources.list.d/php.list' || -e '/etc/apt/sources.list.d/ondrej-ubuntu-php-noble.sources') {
            tell_user('INFO', 'Sury repo entries already present');
        } else {
            my $cmd;
            tell_user('INFO', 'Sury PHP repositories not found, adding necessary entries');

            if ($cfg{distro} =~ /ubuntu/i) {
                `chmod +x $cfg{scripts_dir}/sury_setup_ubnt.sh`;
                $cmd = "/bin/sh $cfg{scripts_dir}/sury_setup_ubnt.sh";
            } elsif ($cfg{distro} =~ /debian|kali/i) {
                $cmd = "/bin/sh $cfg{scripts_dir}/sury_setup_deb.sh";
            } elsif ($cfg{os} eq 'windows') {
                # TODO: implement windows setup
            }
            tell_user('SYSTEM', `$cmd`);
        }
        tell_user('INFO', 'Updating system packages');
    }

    if (!$cfg{php_version}) {
        tell_user('WARN', "PHP version not specified, attempting to find it...\n");

        if ($cfg{os} eq 'linux') {
            chomp(my $ver_output = `php --version | head -n1`);

            if ($ver_output =~ /PHP (\d+\.\d+)/) {
                $cfg{php_version} = $1;
            }

            if ($cfg{php_version}) {
                tell_user('SUCCESS', "Found PHP version $cfg{php_version}");
            } else {
                my $t_phpv;
                if ($cfg{distro} eq 'alpine') {
                    $t_phpv = "8.3";
                } else {
                    $t_phpv = "8.4";
                }

                if (ask_user("Failed to find a PHP version. Agree on $t_phpv?", 'y', 'yesno')) {
                    $cfg{php_version} = "$t_phpv";
                } else {
                    croak("Couldn't come to a conclusion for PHP version");
                }                
            }
        }
    } else {
        tell_user('ERROR', "Invalid or unsupported PHP version set in the installer\n" .
                           "supported PHP versions: 7.0 - 8.4\n\n");
        if (ask_user('Try with version 8.4?', 'y', 'yesno')) {
            $cfg{php_version} = "8.4";
        }
    }

    my $alp_phpv = 83;
    if ($cfg{distro} eq 'alpine') {
        ($alp_phpv = $cfg{php_version}) =~ s/\.//;
    }
        
    my @packages = (
		"deb:php$cfg{php_version}",
		"deb:php$cfg{php_version}-cli",
		"deb:php$cfg{php_version}-common",
		"deb:php$cfg{php_version}-curl",
		"deb:php$cfg{php_version}-dev",
		"deb:php$cfg{php_version}-mysql",
		"deb:php$cfg{php_version}-xml",
		"deb:php$cfg{php_version}-intl",
		"deb:php$cfg{php_version}-mbstring",
		"deb:cron",
		"deb:mariadb-server",
		"deb:apache2",
		"deb:letsencrypt",
		"deb:python-is-python3",
		"deb:python3-certbot-apache",
		"deb:libapache2-mod-php$cfg{php_version}",
		"deb:composer",
		"deb:openssl",
		"alp:lsb_release",
		"alp:php$cfg{php_version}",
		"alp:php$cfg{php_version}-{cli,common,curl,dev,xml,intl,mbstring,apache2}",
		"alp:mariadb",
		"alp:apache2",
		"alp:certbot",
		"alp:composer",
		"alp:openssl",
		"alp:certbot-apache",
    );

    if (ask_user("Do you want to use PHP-FPM? This will use mpm_worker instead of the default mpm_prefork.", 'y', 'yesno')) {
        $cfg{php_fpm} = 1;
        push @packages,  $cfg{software} eq 'deb' ? "deb:php$cfg{php_version}-fpm" : "alp:php$cfg{php_version}-fpm"
    } else {
        $cfg{php_fpm} = 0;
    }     

    tell_user('INFO', 'Installing ' . @packages . ' packages\n');
    
    my $sw_cmd = "$cfg{pm_cmd} " . join (' ', grep { /$cfg{software}/ } @packages) . ' >/dev/null 2>&1';
    $sw_cmd =~ s/$cfg{software}://g;
    tell_user('INFO', "Installing software with command: $sw_cmd");
    
    my $sw_output = `$sw_cmd`;
    if ($? == 0) {
        tell_user('SUCCESS', 'All necessary software has been installed');
    } else {
        tell_user('ERROR', 'There were errors installing the software');
        tell_user('ERROR', $sw_output);
    }

    return 0;
}

sub step_webserver_configure {
    my $question = "Enter the location of your webserver's config directory (e.g. /etc/apache2)";
    $cfg{apache_directory} = ask_user($question, '/etc/apache2', 'input');

    $question = "Please enter the path to where the game will reside (e.g. /var/www/html/example.com/loa)";
    $cfg{web_root} = ask_user($question, $cfg{web_root} ? $cfg{web_root} : '/var/www/html/kali.local/loa', 'input');
    $cfg{web_root} =~ s/\/$//;

    $cfg{virthost_conf_file}     = "$cfg{apache_directory}/sites-available/$cfg{fqdn}.conf";
    $cfg{virthost_conf_file_ssl} = "$cfg{apache_directory}/sites-available/ssl-$cfg{fqdn}.conf";

    if ($cfg{os} eq 'linux') {
        $cfg{composer_runas} = 'www-data';
        $cfg{apache_runas}   = 'www-data';
    }

    $cfg{apache_https_port} = ask_user('Enter apache port for HTTPS', '443', 'input');
    $cfg{apache_http_port}  = ask_user('Enter apache port for HTTP',   '80', 'input');
    
    return 0;
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
    
    }

sub step_vhost_ssl {
    my $answer;
    my $options = "1. Generate a self-signed certificate for $cfg{fqdn} and\n"
                . "   enable HTTP -> HTTPS Redirection\n"
                . "2. Keep current SSL and enable HTTP -> HTTPS redirection\n"
                . "3. Don't enable HTTP -> HTTPS redirection\n"
                . "4. Manually enter certificate and private key locations\n"
                . "6. Skip\n\n"
                . "0. Exit\n\n";

    if (ask_user("Do you want to enable SSL?", 'y', 'yesno')) {
        $cfg{ssl_enabled} = 1;
        `sed -i 's/# REM //' $cfg{virthost_conf_file_ssl}`;

        print "Do you want to redirect traffic from http:80 to "
            . "https:443? A valid certificate needs to be set in the "
            . "script configuration!\n- Currently set -\n"
            . "Certificate: $cfg{ssl_fullcer}\n"
            . "Private Key: $cfg{ssl_privkey}\n\n";

        print $options;

        $answer = int(ask_user('Choice', 1, 'input'));

        if ($answer == 1) {
            my $gen_output = `openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/$cfg{fqdn}.key -out /etc/ssl/certs/$cfg{fqdn}.crt -subj '/CN=$cfg{fqdn}/O=$cfg{fqdn}/C=ZA' -batch`;
            $cfg{ssl_fullcer} = "/etc/ssl/certs/$cfg{fqdn}.crt";
            $cfg{ssl_privkey} = "/etc/ssl/private/$cfg{fqdn}.key";
            `sed -i 's/# REM //' $cfg{virthost_conf_file}`;

            tell_user('SYSTEM', $gen_output);
            tell_user('SUCCESS', "Generated self-signed certificates and enabled HTTP to HTTPS redirection");
            tell_user('INFO', "Certificates and private key stored in /etc/ssl/certs and /etc/ssl/private, respectively");
        } elsif ($answer == 2) {
            tell_user('INFO', "Enabling rewrite directives in $cfg{virthost_conf_file}");
            `sed -i 's/# REM //' $cfg{virthost_conf_file}`;
        } elsif ($answer == 4) {
            my ($cert, $pkey);

            while ((!-e $cert or !-e $pkey)) {
                $cert = ask_user('Enter path to certificate', '', 'input');
                $pkey = ask_user('Enter path to private key', '', 'input');
            }
            $cfg{ssl_fullcer} = $cert;
            $cfg{ssl_privkey} = $pkey;
            tell_user('SUCCESS', 'Valid certificate and key pair supplied, continuing');
        }
    } else {
        $cfg{ssl_enabled} = 0;
        `sed -i 's/# REM //' $cfg{virthost_conf_file}`;
    }
}

sub step_fix_permissions {
    # Web root files
    `find $cfg{web_root} -type f -exec chmod 644 {} + 2>&1`;
    `find $cfg{web_root} -type d -exec chmod 755 {} + 2>&1`;

    # Configuration files
    chmod 0600, "$cfg{web_root}/.env" if -e "$cfg{web_root}/.env";
    chmod 0600, "$cfg{web_root}/install/config.ini" if -e "$cfg{web_root}/install/config.ini";
    chmod 0600, "$cfg{web_root}/install/config.ini.default" if -e "$cfg{web_root}/install/config.ini.default";

    # Script files
    chmod 0600, "$cfg{web_root}/install/AutoInstaller.pl" if -e "$cfg{web_root}/install/AutoInstaller.pl";

    opendir my $sd, $cfg{scripts_dir};
    while (readdir($sd)) {
        next if $_ =~ /^\./;
        chmod 0600, "scripts/$_";
    }
    closedir $sd;

    # Log files
    chmod 0640, "$cfg{web_root}/install/setup.log" if -e "$cfg{web_root}/install/setup.log";
    chmod 0640, "$cfg{web_root}/gamelog.txt" if -e "$cfg{web_root}/gamelog.txt";

    # Apache configuration files
    chmod 0644, $cfg{virthost_conf_file} if -e $cfg{virthost_conf_file};
    if ($cfg{enable_ssl}) {
        chmod 0644, $cfg{virthost_conf_file_ssl} if -e $cfg{virthost_conf_file_ssl};
    }

    # Template directory
    opendir my $td, $cfg{template_dir};
    while (readdir($td)) {
        next if $_ =~ /^\./;
        chmod 0600, "templates/$_";
    }
    closedir $td;

    # Owner/group
    tell_user('SYSTEM', `chown -R $cfg{apache_runas}:$cfg{apache_runas} $cfg{web_root} 2>&1`);
    tell_user('SUCCESS', 'Permissions fixed!');
}

sub step_apache_enables {
    my $success = 1000;
    my $mods = 'rewrite ssl ';
    my ($conf_output, $dismod_output, $mods_output, $sites_output, $sites_ssl_output);

    tell_user('INFO', 'Enabling required Apache configurations, sites and modules');

    if (int($cfg{php_fpm}) == 1) {
        $conf_output = `a2enconf php$cfg{php_version}-fpm 2>&1`;
        $success += $?;

        $dismod_output = `a2dismod php* mpm_prefork 2>&1`;
        $success += $?;

        $mods .= 'proxy_fcgi setenvif mpm_event';
    } else {
        $mods .= "php$cfg{php_version}";
    }
    
    $mods_output = `a2enmod $mods 2>&1`;
    $success += $?;

    $sites_output = `a2ensite $ 2>&1`;
    $success += $?;

    if ($cfg{ssl_enabled}) {
        $sites_ssl_output = `a2ensite ssl-$cfg{fqdn}.conf 2>&1`;
        $success += $?;
        tell_user('SYSTEM', "    site (ssl) result: $sites_ssl_output");
    }

    if ($cfg{php_frm}) {
        tell_user('SYSTEM', "        dismod result: $dismod_output");
        tell_user('SYSTEM', "          conf result: $conf_output");
    }

    tell_user('SYSTEM', "          mods result: $mods_output");
    tell_user('SYSTEM', "site (non-ssl) result: $sites_output");
   
    if ($success == 1000) {
        tell_user('SUCCESS', "Apache configuration completed");
    } else {
        tell_user('ERROR', "There were errors - See above output\n");

        if (!ask_user('Continue?', 'n', 'yesno')) {
            die "Quitting at user request\n";
        }
    }    
}

sub step_php_configure {
    my $ini_contents;
    my $template_file;
    my %ini_patch = (
        'expose_php'              => 'off',
	    'error_reporting'         => 'E_NONE',
	    'display_errors'          => 'Off',
	    'display_startup_errors'  => 'Off',
	    'allow_url_fopen'         => 'Off',
	    'allow_url_include'       => 'Off',
	    'session.gc_maxlifetime'  => 600,
	    'disable_functions'       => 'apache_child_terminate, ' .
									 'apache_setenv, ' .
									 'chdir, ' .
									 'chmod, ' .
									 'dbase_open, ' .
									 'dbmopen, ' .
									 'define_syslog_variables, ' .
									 'escapeshellarg, ' .
									 'escapeshellcmd, ' .
									 'eval, ' .
									 'exec, ' .
									 'filepro, ' .
									 'filepro_retrieve, ' .
									 'filepro_rowcount, ' .
									 'fopen_with_path, ' .
									 'fp, ' .
									 'fput, ' .
									 'ftp_connect, ' .
									 'ftp_exec, ' .
									 'ftp_get, ' .
									 'ftp_login, ' .
									 'ftp_nb_fput, ' .
									 'ftp_put, ' .
									 'ftp_raw, ' .
									 'ftp_rawlist, ' .
									 'highlight_file, ' .
									 'ini_alter, ' .
									 'ini_get_all, ' .
									 'ini_restore, ' .
									 'inject_code, ' .
									 'mkdir, ' .
									 'move_uploaded_file, ' .
									 'mysql_pconnect, ' .
									 'openlog, ' .
									 'passthru, ' .
									 'phpAds_XmlRpc, ' .
									 'phpAds_remoteInfo, ' .
									 'phpAds_xmlrpcDecode, ' .
									 'phpAds_xmlrpcEncode, ' .
									 'php_uname, ' .
									 'phpinfo, ' .
									 'popen, ' .
									 'posix_getpwuid, ' .
									 'posix_kill, ' .
									 'posix_mkfifo posix_mkfifo, ' .
									 'posix_setpgid, ' .
									 'posix_setsid, ' .
									 'posix_setuid, ' .
									 'posix_uname, ' .
									 'proc_close, ' .
									 'proc_get_status, ' .
									 'proc_nice, ' .
									 'proc_open, ' .
									 'proc_terminate, ' .
									 'putenv, ' .
									 'rename, ' .
									 'rmdir, ' .
									 'shell_exec, ' .
									 'show_source, ' .
									 'syslog, ' .
									 'system, ' .
									 'xmlrpc_entity_decode',
			
	    'session.cookie_domain'   => $cfg{fqdn},
	    'session.use_strict_mode' => 1,
	    'session.use_cookies'     => 1,
	    'session.cookie_lifetime' => 14400,
	    'session.cookie_secure'   => 1,
	    'session.cookie_httponly' => 1,
	    'session.cookie_samesite' => 'Strict',
	    'session.cache_expire'    => 30,
    );

    if ($cfg{os} eq 'linux') {
        chomp($cfg{php_binary} = `which php$cfg{php_version}`);
        if (int($cfg{php_fpm}) == 1) {
            $cfg{php_ini} = "/etc/php/$cfg{php_version}/fpm/php.ini";
        } else {
            $cfg{php_ini} = "/etc/php/$cfg{php_version}/apache2/php.ini";
        }
    } elsif ($cfg{os} eq 'windows') {
        $cfg{php_binary} = 'C:\xampp\php\php.exe';
        $cfg{php_ini} = 'C:\xampp\php\php.ini';
    }

    # Binary
    if (ask_user("Located PHP binary at $cfg{php_binary} - is this correct?", 'y', 'yesno')) {
        tell_user('INFO', "PHP binary set to $cfg{php_binary}");
    } else {
        $cfg{php_binary} = ask_user('PHP binary not found. Please enter the path to the PHP binary', '', 'input');

        if (!-e $cfg{php_binary}) {
            tell_user('ERROR', "PHP binary not found at $cfg{php_binary}");
            die "Exiting on error\n";
        }
    }

    # INI file
    if (ask_user("PHP ini file is set to $cfg{php_ini} - is this correct?", 'y', 'yesno')) {
        tell_user('INFO', "PHP ini file set to $cfg{php_ini}");
    } else {
        $cfg{php_ini} = ask_user('Please enter the path to the PHP ini file', $cfg{php_ini}, 'input');

        if (!-e $cfg{php_ini}) {
            tell_user('ERROR', "PHP ini file not found at $cfg{php_ini}");
            die "Exiting on error\n";
        }
    }

    {
        local $/;
        open my $fh, '<', $cfg{php_ini}
            or die "Couldn't open '$cfg{php_ini}' for read: $!\n";
        $ini_contents = <$fh>;
        close $fh;
    }

    while (my ($key, $value) = each %ini_patch) {
        if ($ini_contents =~ /;?$key =/) {
            $ini_contents =~ s/;?$key ?=.*/$key = $value\n/g;
            print "Replaced $key with $value\n";
        } else {
            $ini_contents .= "\n$key=$value";
            print "Key not found, added $key=$value\n";
        }
    }

    file_write($cfg{php_ini}, $ini_contents, 'data');
}

sub step_composer_pull {
    if (ask_user("Composer is going to download/install these as $cfg{composer_runas} - continue?", 'y', 'yesno')) {
        my $cmd = "sudo -u $cfg{composer_runas} composer --working-dir \"$cfg{web_root}\" install 2>/dev/null;";
        $cmd .= "sudo -u $cfg{composer_runas} composer --working-dir \"$cfg{web_root}\" update 2>/dev/null";
        
        my $cmd_output = `$cmd`;
        tell_user('SYSTEM', $cmd_output);
    }
}
                                           
sub step_generate_templates {
    my @replacements = @{$_[0]};
    my $copy_output;
    my $cron_contents = '';
    my $fh_cron;
    my %templates;

    # Dirty-fix SQL bind address
    `sed -i 's/bind-address.*/bind-address = $cfg{sql_host}/' $cfg{sql_config_file}`;

    # Dirty-fix constants.php file TODO: Figure this out
    # define('PATH_WEBROOTECTORY', '/var/www/html/dankaf.ca/loa/');
    # `sed -i 's/.*PATH_WEBROOTECTORY.*//'`;

    # key = in file, value = out file
    $templates{$cfg{env_template}}          = "$cfg{env_template}.ready";
    $templates{$cfg{htaccess_template}}     = "$cfg{htaccess_template}.ready";
    $templates{$cfg{sql_template}}          = "$cfg{sql_template}.ready";
    $templates{$cfg{crontab_template}}      = "$cfg{crontab_template}.ready";
    $templates{$cfg{virthost_ssl_template}} = "$cfg{virthost_ssl_template}.ready";
    $templates{$cfg{virthost_template}}     = "$cfg{virthost_template}.ready";
    $templates{$cfg{constants_template}}    = "$cfg{constants_template}.ready";

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
    use Term::ReadKey;
    my ($cp_cmd, $sql_cmd, $sqlrpw, $tmp);
    my $sql_import_result;
    my $crontab_output;

    if (check_platform() eq 'linux') {
        $cp_cmd = "cp";
    } else {
        $cp_cmd = "copy";
    }

    tell_user('INFO', "Importing SQL schema, creating user and granting privileges");
    
    ReadMode 'noecho';
    print "If the root account for the SQL server needs a password,\n";
    $tmp = ask_user("please enter it now or just hit enter", '', 'input');
    ReadMode 'normal';

    $sqlrpw = '';
    if ($tmp) {
        $sqlrpw = "-p $tmp";
    }

    $sql_cmd = "mysql -u root $sqlrpw < $cfg{sql_template}.ready 2>&1";
    chomp($sql_import_result = `$sql_cmd`);
    $sql_import_result //= 'Successfully imported database schema';

    # LOL
    tell_user((!!$? ? 'ERROR' : 'SUCCESS'), $sql_import_result);

    tell_user('INFO', "Copying over $cfg{env_template}.ready to $cfg{web_root}/.env");
    file_write("$cfg{web_root}/.env", "$cfg{env_template}.ready", 'file');

    tell_user('INFO', "Copying over $cfg{virthost_template}.ready to $cfg{virthost_conf_file}");
    file_write($cfg{virthost_conf_file}, "$cfg{virthost_template}.ready", 'file');

    if (int($cfg{ssl_enabled}) == 1) {
        tell_user('INFO', "Copying over $cfg{virthost_ssl_template}.ready to $cfg{virthost_conf_file_ssl}");
        file_write($cfg{virthost_conf_file_ssl}, "$cfg{virthost_ssl_template}.ready", 'file');
    }

    tell_user('INFO', "Copying over $cfg{htaccess_template}.ready to $cfg{web_root}/.htaccess");
    file_write("$cfg{web_root}/.htaccess", "$cfg{htaccess_template}.ready", 'file');

    tell_user('INFO', "Copying over $cfg{constants_template}.ready to $cfg{web_root}/constants.php");
    file_write("$cfg{web_root}/constants.php", "$cfg{constants_template}.ready", 'file');

    tell_user('INFO', "Installing our cronjobs under user $cfg{apache_runas}");
    
    $crontab_output = `(sudo -u $cfg{apache_runas} crontab -l; cat $cfg{crontab_template}.ready; echo;) | sudo -u $cfg{apache_runas} crontab -`;
    tell_user('SYSTEM', $crontab_output);

    tell_user('SUCCESS', "All template files have been applied");
}

sub step_start_services {
    my @services = ("mariadb", "apache2");

    if (int($cfg{php_fpm}) == 1) {
        push @services, "php$cfg{php_version}-fpm";
    }

    foreach my $service (@services) {
        my $order = "start $service";
        tell_user('INFO', "Starting $service");
        

        if ($cfg{svc_cmd} eq 'service') {
            $order = "$service start";
        }

        tell_user('SYSTEM', `$cfg{svc_cmd} $order`);
    }
}

# ====================================[ steps-end ]====================================== #
# ==================================[ internal-start ]=================================== #
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

        tell_user("INFO", "Removing our crontab entries");
        my $crontab_output = `(sudo -u $cfg{apache_runas} crontab -l; cat $cfg{crontab_template}.ready | grep -v 'cron.php' ; echo;) | sudo -u $cfg{apache_runas} crontab -`;
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
        
        my $num = 0;
        
        while (!$num or ($num >= 91 && $num <= 96)) {
            $num = int(rand(74)) + 48;
        }

        $password .= chr($num);
    }

    $password =~ s/\\/\\\\/g;
    $password =~ s/'/\\'/g;

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
        my ($yes, $no) = ("$clr{green}y$clr{reset}", "$clr{red}n$clr{reset}");

        if ($default && $default eq 'y') {
            $yes = "[$clr{green}y$clr{reset}]";
        } elsif ($default && $default eq 'n') {
            $no = "[$clr{red}n$clr{reset}]";
        }

        print "[$yes / $no]>";        
    } elsif ($type eq 'input') {
        if ($default) {
            print "[$clr{yellow}$default$clr{reset}]> ";
        } else {
            print "$clr{yellow}enter$clr{reset}> ";
        }
    }

    chomp(my $answer = <STDIN>);
    print "\n";

    if ($answer =~ /[Yy]e?s?/ && $type eq 'yesno') {
        return 1;
    } elsif ($answer =~ /[Nn]o?/ && $type eq 'yesno') {
        return 0;
    } elsif (($answer eq '' or !$answer) && $type eq 'yesno') {
        if ($default eq 'y') {                             
            return 1;
        } else {
            return 0;
        }
    }

    if (($answer eq '' or !$answer) && $type eq 'input') {
        return $default;
    } else {
        return $answer;
    }

    return -1;
}

sub search_array {
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

sub const_to_name {
    my @names = qw/PAD FIRSTRUN SOFTWARE PHP SERVICES SQL OPENAI TEMPLATES APACHE PERMS COMPOSER CLEANUP/;
    return $names[shift];
}

sub name_to_const {
    my $name = shift;
    
    my %names = (
        'PAD'       => 0,
        'FIRSTRUN'  => 1,
        'SOFTWARE'  => 2,
        'PHP'       => 3,
        'SERVICES'  => 4,
        'SQL'       => 5,
        'OPENAI'    => 6,
        'TEMPLATES' => 7,
        'APACHE'    => 8,
        'PERMS'     => 9,
        'COMPOSER'  => 10,
        'CLEANUP'   => 11
    );

    return $names{$name};    
}

sub next_step {
    if ($opt_only) {
        print "Only running step $cfg{step}\n";
        exit 0;
    }

    print "Moving on to step " . const_to_name($cfg{step}++) . "\n";
    handle_cfg(\%cfg, CFG_W_DOMAIN, $fqdn);
}

sub get_sysinfo {
    $cfg{os} = check_platform();
    if ($cfg{os} eq 'linux') {
        my @supported_distros_kinda = (
            "redhat:yum:-y install",
            "arch:pacman:-S",
            "gentoo:emerge:-tv",
            "SuSE:zypper:in",
            "debian:apt-get:-y install",
            "alpine:apk:add",
            "kali:apt-get:-y install",
            "ubuntu:apt-get:-y install",
            "linuxmint:apt-get:-y install",
            "centos:yum:-y install",
            "fedora:dnf:-y install",
            "slackware:slackpkg:install",
        );

        chomp(my $check_docker = `mount | grep 'overlay on / type' -ao`);
        chomp(my $init_system = `SYSTEMD=\$(strings /sbin/init | grep systemd | wc -l); INITD=\$(strings /sbin/init | grep init.d | wc -l); if [ "\$SYSTEMD" -gt "0" ]; then echo "systemd"; elif [ "\$INITD" -gt "0" ]; then echo "initd"; else echo "other"; fi`);
        $cfg{init_system} = $init_system;

        foreach my $combo (@supported_distros_kinda) {
            my ($distro, $pm, $args) = split ':', $combo;

            if (-e "/etc/$distro-release") {
                $cfg{distro} = $distro;
                $cfg{pm_cmd} = "$pm $args";
                tell_user('INFO', "Found $distro-release file");
                last;
            }

            if (-e "/etc/os-release") {
                open my $fh, '<', '/etc/os-release';
                my @lines = <$fh>;
                close $fh;
                
                my $found = 0;
                foreach my $line (@lines) {
                    if ($line =~ /^ID=$distro/ || $line =~ /^LIKE=$distro/) {
                        $cfg{distro} = $distro;
                        $cfg{pm_cmd} = "$pm $args";
                        tell_user('INFO', "Found $distro in /etc/os-release");
                        $found = 1;
                    }
                    last if $found;
                }
            }

            if (!$cfg{distro}) {
                my $lsb_check = `lsb_release -i`;
                if ($lsb_check =~ /$distro/) {
                    $cfg{distro} = $distro;
                    $cfg{pm_cmd} = "$pm $args";
                    tell_user('INFO', "Found $distro in lsb_release");
                    last;
                }
            }
        }

        if ($cfg{distro}) {
            tell_user('SUCCESS', "Your distribution was found to be $cfg{distro}, so we will use $cfg{pm_cmd} to install software on this machine");

            if (!ask_user('Is this correct?', 'y', 'yesno')) {
                die "Unrecoverable error: Unable to determine distribution and package manager information\n";
            }
        } else {
            tell_user('ERROR', 'Unable to determine distro');
            
            if (ask_user('Is your distro debian-based, which uses apt-get?', 'n', 'yesno')) {
                $cfg{distro} = 'debian';
                $cfg{software} = 'deb';
                $cfg{pm_cmd} = 'apt-get install -y';
            } else {
                die "Unrecoverable error: Unable to determine distribution and package manager information\n";
            }
        }

        if ($check_docker || $cfg{init_system} eq 'init') {
            $cfg{svc_cmd} = 'service';
        } elsif ($cfg{init_system} eq 'systemd') {
            $cfg{svc_cmd} = 'systemctl';
        } else {
            tell_user('ERROR', 'Unable to determine what init system is used (e.g. "service" or "systemctl") to start services. Please enter below.');
            $cfg{svc_cmd} = ask_user('Which init system do you use?', '', 'input');
        }

        if ($cfg{distro} =~ /debian|ubuntu|kali/) {
            $cfg{software} = 'deb';
        } elsif ($cfg{distro} eq 'alpine') {
            $cfg{software} = 'alp';
        }
    } else {
        $cfg{svc_cmd} = 'sc';
        $cfg{pm_cmd} = 'choco install';
    }
}

sub help {
    print "Usage: $0 [options]\n\n";
    print "Options:\n";
    print "  -h, --help\t\tShow this help message and exit\n";
    print "  -v, --version\t\tShow version information and exit\n";
    print "  -c, --config\t\tSpecify a configuration file to use\n";
    print "  -f, --fqdn\t\tSpecify a domain to use\n";
    print "  -s, --step\t\tSpecify a step to start at\n";
    print "  -l, --list-steps\tList the available steps to supply to -s/--step\n";
    exit 0;
}