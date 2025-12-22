# AutoInstaller

<details>
<summary>Relevant source files</summary>

The following files were used as context for generating this wiki page:

- [.gitignore](.gitignore)
- [composer.json](composer.json)
- [composer.lock](composer.lock)
- [install/AutoInstaller.pl](install/AutoInstaller.pl)
- [install/scripts/windows/bootstrap.ps1](install/scripts/windows/bootstrap.ps1)
- [install/scripts/windows/main.ps1](install/scripts/windows/main.ps1)
- [install/templates/sql.template](install/templates/sql.template)
- [tbl_explore/table_explore.pl](tbl_explore/table_explore.pl)

</details>



The AutoInstaller is a Perl-based installation orchestrator that automates the complete deployment of Legend of Aetheria on Linux and Windows systems. It manages software installation, web server configuration, database schema creation, SSL certificate setup, and file permissions through a sequential, resumable step-based process.

For prerequisite information before running the AutoInstaller, see [Prerequisites](#2.1). For details on the configuration file format and available settings, see [Configuration](#2.3). For Apache-specific setup details, see [Web Server Setup](#2.4).

## Script Overview

The AutoInstaller is implemented as a single Perl script located at [install/AutoInstaller.pl:1-2175](). It orchestrates 12 distinct installation steps, each responsible for a specific aspect of system setup. The script version is 2.6.4.28 and requires root privileges to execute.

**Sources:** [install/AutoInstaller.pl:1-119]()

## Installation Step Pipeline

The AutoInstaller executes steps sequentially, tracking progress in the configuration file to enable resumption after interruptions.

```mermaid
graph TB
    Start["AutoInstaller.pl Execution"] --> CheckRoot{"Root User?"}
    CheckRoot -->|No| Error["Exit: Must run as root"]
    CheckRoot -->|Yes| GetFQDN["Prompt for FQDN"]
    
    GetFQDN --> Step1["FIRSTRUN (1)<br/>Load/Create Config"]
    Step1 --> Step2["SOFTWARE (2)<br/>Install Packages"]
    Step2 --> Step3["PHP (3)<br/>Configure php.ini"]
    Step3 --> Step4["SERVICES (4)<br/>Start Apache/MariaDB"]
    Step4 --> Step5["SQL (5)<br/>Import Schema"]
    Step5 --> Step6["OPENAI (6)<br/>Configure API Key"]
    Step6 --> Step7["TEMPLATES (7)<br/>Generate Configs"]
    Step7 --> Step8["APACHE (8)<br/>Enable Modules/Sites"]
    Step8 --> Step9["PERMS (9)<br/>Fix Permissions"]
    Step9 --> Step10["COMPOSER (10)<br/>Install Dependencies"]
    Step10 --> Step11["CLEANUP (11)<br/>Remove Temp Files"]
    Step11 --> Step12["HOSTS (12)<br/>Update /etc/hosts"]
    Step12 --> Complete["Installation Complete"]
    
    Step1 -.->|Resume| Step2
    Step2 -.->|Resume| Step3
    Step3 -.->|Resume| Step4
    Step4 -.->|Resume| Step5
    Step5 -.->|Resume| Step6
    Step6 -.->|Resume| Step7
    Step7 -.->|Resume| Step8
    Step8 -.->|Resume| Step9
    Step9 -.->|Resume| Step10
    Step10 -.->|Resume| Step11
    Step11 -.->|Resume| Step12
```

**Step Constants and Flow**

| Step Number | Constant Name | Function | Description |
|-------------|---------------|----------|-------------|
| 1 | `FIRSTRUN` | `step_firstrun()` | Load existing config or create new |
| 2 | `SOFTWARE` | `step_install_software()` | Install Apache, MariaDB, PHP packages |
| 3 | `PHP` | `step_php_configure()` | Modify php.ini with security settings |
| 4 | `SERVICES` | `step_start_services()` | Start web server and database |
| 5 | `SQL` | `step_sql_configure()` | Prompt for DB credentials |
| 6 | `OPENAI` | N/A (inline) | Configure OpenAI API integration |
| 7 | `TEMPLATES` | `step_generate_templates()` | Process template files |
| 8 | `APACHE` | `step_apache_enables()` | Enable Apache modules and sites |
| 9 | `PERMS` | `step_fix_permissions()` | Set file/directory permissions |
| 10 | `COMPOSER` | `step_composer_pull()` | Run composer install/update |
| 11 | `CLEANUP` | `clean_up()` | Remove temporary files |
| 12 | `HOSTS` | `step_update_hosts()` | Update /etc/hosts entries |

**Sources:** [install/AutoInstaller.pl:45-58](), [install/AutoInstaller.pl:187-299]()

## Command Line Interface

The AutoInstaller supports command-line arguments for automated or partial installations:

```mermaid
graph LR
    CLI["perl AutoInstaller.pl"] --> FqdnOpt["-f|--fqdn DOMAIN"]
    CLI --> StepOpt["-s|--step NUMBER"]
    CLI --> OnlyOpt["-o|--only"]
    CLI --> ConfigOpt["-c|--config FILE"]
    CLI --> ListOpt["-l|--list-steps"]
    CLI --> HelpOpt["-h|--help"]
    CLI --> VersionOpt["-v|--version"]
    
    FqdnOpt --> Execute["Execute Installation"]
    StepOpt --> Execute
    OnlyOpt --> Execute
    ConfigOpt --> Execute
    
    ListOpt --> Display["Display Steps<br/>and Exit"]
    HelpOpt --> Display
    VersionOpt --> Display
```

**Option Details**

| Option | Description | Example |
|--------|-------------|---------|
| `-f`, `--fqdn` | Specify fully qualified domain name | `--fqdn loa.example.com` |
| `-s`, `--step` | Start from specific step (name or number) | `--step SOFTWARE` or `--step 2` |
| `-o`, `--only` | Execute only specified step, don't continue | `--only --step APACHE` |
| `-c`, `--config` | Use alternate config file | `--config custom.ini` |
| `-l`, `--list-steps` | Display all step names and numbers | N/A |
| `-h`, `--help` | Show help message | N/A |
| `-v`, `--version` | Display version (2.6.4.28) | N/A |

**Sources:** [install/AutoInstaller.pl:20-39]()

## Configuration Management System

The AutoInstaller uses `Config::IniFiles` to manage persistent configuration across installation steps. Configuration is stored in `config.ini` with per-FQDN sections.

```mermaid
graph TB
    ConfigFile["config.ini"] --> TiedHash["Config::IniFiles<br/>Tied Hash %ini"]
    TiedHash --> AllSection["[all] Section<br/>Default Values"]
    TiedHash --> FqdnSection["[loa.example.com]<br/>Domain-Specific Config"]
    
    FqdnSection --> CfgHash["%cfg Hash<br/>Current FQDN Config"]
    CfgHash --> WebRoot["web_root"]
    CfgHash --> PHPVer["php_version"]
    CfgHash --> SQLCreds["sql_username<br/>sql_password<br/>sql_database"]
    CfgHash --> ApacheConfig["apache_directory<br/>virthost_conf_file"]
    CfgHash --> SSLConfig["ssl_enabled<br/>ssl_fullcer<br/>ssl_privkey"]
    CfgHash --> StepTracker["step (current progress)"]
    
    CfgHash --> HandleCfg["handle_cfg()"]
    HandleCfg --> WriteConfig["Write Back to config.ini"]
```

**Configuration Hash Structure**

The `%cfg` hash contains the current FQDN's configuration:

| Key | Type | Description | Example |
|-----|------|-------------|---------|
| `fqdn` | string | Fully qualified domain name | `loa.example.com` |
| `step` | integer | Current installation step | `5` |
| `web_root` | path | Web server document root | `/var/www/loa` |
| `php_version` | string | PHP major.minor version | `8.4` |
| `php_ini` | path | PHP configuration file | `/etc/php/8.4/apache2/php.ini` |
| `php_binary` | path | PHP executable | `/usr/bin/php8.4` |
| `php_fpm` | boolean | Use PHP-FPM instead of mod_php | `1` |
| `sql_username` | string | Database user | `user_loa` |
| `sql_password` | string | Database password | (generated) |
| `sql_database` | string | Database name | `db_loa` |
| `sql_host` | string | Database host | `127.0.0.1` |
| `sql_port` | integer | Database port | `3306` |
| `apache_directory` | path | Apache config directory | `/etc/apache2` |
| `apache_http_port` | integer | HTTP port | `80` |
| `apache_https_port` | integer | HTTPS port | `443` |
| `apache_runas` | string | Apache user | `www-data` |
| `ssl_enabled` | boolean | Enable SSL | `1` |
| `ssl_fullcer` | path | SSL certificate path | `/etc/ssl/certs/loa.example.com.crt` |
| `ssl_privkey` | path | SSL private key path | `/etc/ssl/private/loa.example.com.key` |
| `openai_enable` | boolean | Enable OpenAI features | `0` |
| `openai_apikey` | string | OpenAI API key | (user provided) |

**Sources:** [install/AutoInstaller.pl:75-112](), [install/AutoInstaller.pl:184-193]()

## Template Processing System

The AutoInstaller uses a template-based system to generate configuration files with environment-specific values. Templates contain placeholders that are replaced with actual configuration data.

```mermaid
graph TB
    Templates["Template Files<br/>install/templates/*.template"]
    
    Templates --> EnvTemplate[".env.template"]
    Templates --> HtaccessTemplate[".htaccess.template"]
    Templates --> SQLTemplate["sql.template"]
    Templates --> VhostTemplate["virthost.template"]
    Templates --> VhostSSLTemplate["virthost_ssl.template"]
    Templates --> CronTemplate["crontab.template"]
    Templates --> ConstTemplate["constants.template"]
    
    Replacements["@replacements Array<br/>Search/Replace Pairs"]
    Replacements --> ParseRep["parse_replacements()"]
    
    ParseRep --> FqdnRepl["###REPL_FQDN###<br/>→ loa.example.com"]
    ParseRep --> SQLRepl["###REPL_SQL_*###<br/>→ Database credentials"]
    ParseRep --> WebRootRepl["###REPL_WEB_ROOT###<br/>→ /var/www/loa"]
    ParseRep --> SSLRepl["###REPL_SSL_*###<br/>→ Certificate paths"]
    ParseRep --> ProtoRepl["###REPL_PROTOCOL###<br/>→ http/https"]
    
    EnvTemplate --> Generate["step_generate_templates()"]
    HtaccessTemplate --> Generate
    SQLTemplate --> Generate
    VhostTemplate --> Generate
    VhostSSLTemplate --> Generate
    CronTemplate --> Generate
    ConstTemplate --> Generate
    
    FqdnRepl --> Generate
    SQLRepl --> Generate
    WebRootRepl --> Generate
    SSLRepl --> Generate
    ProtoRepl --> Generate
    
    Generate --> ReadyFiles["*.ready Files<br/>Generated Output"]
    
    ReadyFiles --> EnvReady[".env.ready"]
    ReadyFiles --> HtaccessReady[".htaccess.ready"]
    ReadyFiles --> SQLReady["sql.ready"]
    ReadyFiles --> VhostReady["virthost.ready"]
    ReadyFiles --> VhostSSLReady["virthost_ssl.ready"]
    ReadyFiles --> CronReady["crontab.ready"]
    ReadyFiles --> ConstReady["constants.ready"]
    
    ReadyFiles --> Process["step_process_templates()"]
    
    Process --> FinalEnv["$web_root/.env"]
    Process --> FinalHtaccess["$web_root/.htaccess"]
    Process --> ImportSQL["mysql < sql.ready"]
    Process --> FinalVhost["$apache_directory/sites-available/$fqdn.conf"]
    Process --> FinalVhostSSL["$apache_directory/sites-available/ssl-$fqdn.conf"]
    Process --> InstallCron["crontab -"]
    Process --> FinalConst["$web_root/system/constants.php"]
```

**Template Replacement Syntax**

Template files use `###REPL_*###` placeholders that are replaced during `step_generate_templates()`:

| Placeholder | Replacement Source | Example Value |
|-------------|-------------------|---------------|
| `###REPL_FQDN###` | `$cfg{fqdn}` | `loa.example.com` |
| `###REPL_WEB_ROOT###` | `$cfg{web_root}` | `/var/www/loa` |
| `###REPL_SQL_DB###` | `$cfg{sql_database}` | `db_loa` |
| `###REPL_SQL_USER###` | `$cfg{sql_username}` | `user_loa` |
| `###REPL_SQL_PASS###` | `$cfg{sql_password}` | (generated password) |
| `###REPL_SQL_HOST###` | `$cfg{sql_host}` | `127.0.0.1` |
| `###REPL_SQL_PORT###` | `$cfg{sql_port}` | `3306` |
| `###REPL_SSL_CERT###` | `$cfg{ssl_fullcer}` | `/etc/ssl/certs/loa.example.com.crt` |
| `###REPL_SSL_KEY###` | `$cfg{ssl_privkey}` | `/etc/ssl/private/loa.example.com.key` |
| `###REPL_PROTOCOL###` | `http` or `https` | `https` |
| `###REPL_SQL_TBL_*###` | `%sql` hash | `loa_accounts` |

**Sources:** [install/AutoInstaller.pl:236-263](), [install/AutoInstaller.pl:861-950]()

## Software Installation Step

The `SOFTWARE` step installs all required packages using the system's package manager. It detects the operating system and uses either `apt` (Debian/Ubuntu) or `apk` (Alpine).

```mermaid
graph TB
    StepSoftware["step_install_software()"] --> CheckSury{"Sury PHP Repo<br/>Exists?"}
    
    CheckSury -->|No| DetectDistro{"Detect Distribution"}
    DetectDistro -->|Ubuntu| RunSuryUbuntu["Execute sury_setup_ubnt.sh"]
    DetectDistro -->|Debian/Kali| RunSuryDeb["Execute sury_setup_deb.sh"]
    
    CheckSury -->|Yes| DetectPHP["Detect PHP Version"]
    RunSuryUbuntu --> DetectPHP
    RunSuryDeb --> DetectPHP
    
    DetectPHP --> PromptFPM{"Use PHP-FPM?"}
    PromptFPM -->|Yes| SetFPM["cfg{php_fpm} = 1<br/>Add php-fpm package"]
    PromptFPM -->|No| SetModPHP["cfg{php_fpm} = 0"]
    
    SetFPM --> BuildPackageList["Build Package List"]
    SetModPHP --> BuildPackageList
    
    BuildPackageList --> PHPPackages["PHP Packages:<br/>php8.4, php8.4-cli,<br/>php8.4-curl, php8.4-dev,<br/>php8.4-mysql, php8.4-xml,<br/>php8.4-intl, php8.4-mbstring"]
    BuildPackageList --> ServerPackages["Server Packages:<br/>apache2, mariadb-server,<br/>letsencrypt, certbot,<br/>python3-certbot-apache"]
    BuildPackageList --> ToolPackages["Tool Packages:<br/>composer, openssl, cron"]
    
    PHPPackages --> ExecutePM["Execute Package Manager<br/>$pm_cmd install ..."]
    ServerPackages --> ExecutePM
    ToolPackages --> ExecutePM
    
    ExecutePM --> CheckStatus{"Exit Code<br/>$? == 0?"}
    CheckStatus -->|Yes| Success["Tell User: SUCCESS"]
    CheckStatus -->|No| Error["Tell User: ERROR"]
    
    Success --> ConfigWebserver["step_webserver_configure()"]
    ConfigWebserver --> PromptApacheDir["Prompt: apache_directory<br/>Default: /etc/apache2"]
    ConfigWebserver --> PromptPorts["Prompt: HTTP/HTTPS Ports<br/>Default: 80/443"]
    ConfigWebserver --> PromptEmail["Prompt: admin_email<br/>Default: webmaster@fqdn"]
```

**Package List by Distribution**

The script installs distribution-specific packages prefixed with `deb:` or `alp:`:

| Package | Debian/Ubuntu | Alpine | Purpose |
|---------|---------------|--------|---------|
| PHP | `php8.4` | `php84` | PHP runtime |
| PHP CLI | `php8.4-cli` | `php84-cli` | Command-line interface |
| PHP MySQL | `php8.4-mysql` | N/A | MySQL extension |
| PHP Extensions | `php8.4-{curl,xml,intl,mbstring}` | `php84-{curl,xml,intl,mbstring}` | Required extensions |
| Apache | `apache2` | `apache2` | Web server |
| MariaDB | `mariadb-server` | `mariadb` | Database server |
| Certbot | `python3-certbot-apache` | `certbot-apache` | SSL automation |
| PHP-FPM | `php8.4-fpm` | `php84-fpm` | FastCGI Process Manager (optional) |

**Sources:** [install/AutoInstaller.pl:374-507](), [install/AutoInstaller.pl:435-463]()

## PHP Configuration Step

The `PHP` step modifies the `php.ini` file to apply security hardening and session management settings.

```mermaid
graph TB
    StepPHP["step_php_configure()"] --> DetectBinary["Detect PHP Binary<br/>which php8.4"]
    DetectPHP --> DetectIni{"PHP-FPM Enabled?"}
    
    DetectIni -->|Yes| FPMIni["php.ini =<br/>/etc/php/8.4/fpm/php.ini"]
    DetectIni -->|No| ApacheIni["php.ini =<br/>/etc/php/8.4/apache2/php.ini"]
    
    FPMIni --> ConfirmPath{"Confirm<br/>php.ini Path?"}
    ApacheIni --> ConfirmPath
    
    ConfirmPath --> ReadINI["Read php.ini Contents"]
    ReadINI --> ApplyPatches["Apply ini_patch Hash"]
    
    ApplyPatches --> SecSettings["Security Settings:<br/>expose_php=Off<br/>allow_url_fopen=Off<br/>allow_url_include=Off"]
    ApplyPatches --> ErrorSettings["Error Settings:<br/>display_errors=Off<br/>error_reporting=E_NONE"]
    ApplyPatches --> SessionSettings["Session Settings:<br/>session.use_strict_mode=1<br/>session.cookie_secure=1<br/>session.cookie_httponly=1<br/>session.cookie_samesite=Strict"]
    ApplyPatches --> DisabledFuncs["Disabled Functions:<br/>exec, eval, system,<br/>shell_exec, passthru,<br/>proc_open, phpinfo, etc."]
    
    SecSettings --> ReplaceOrAdd["For Each Setting:<br/>Replace if exists,<br/>Append if not"]
    ErrorSettings --> ReplaceOrAdd
    SessionSettings --> ReplaceOrAdd
    DisabledFuncs --> ReplaceOrAdd
    
    ReplaceOrAdd --> WriteINI["file_write(php.ini)"]
```

**Key PHP Configuration Changes**

| Setting | Value | Purpose |
|---------|-------|---------|
| `expose_php` | `Off` | Hide PHP version from headers |
| `error_reporting` | `E_NONE` | Disable error reporting |
| `display_errors` | `Off` | Don't display errors to users |
| `allow_url_fopen` | `Off` | Prevent remote file inclusion |
| `allow_url_include` | `Off` | Prevent remote code inclusion |
| `session.use_strict_mode` | `1` | Reject uninitialized session IDs |
| `session.cookie_domain` | `$fqdn` | Restrict cookies to domain |
| `session.cookie_secure` | `1` | HTTPS-only cookies |
| `session.cookie_httponly` | `1` | Prevent JavaScript cookie access |
| `session.cookie_samesite` | `Strict` | CSRF protection |
| `session.gc_maxlifetime` | `600` | Session timeout (10 minutes) |
| `disable_functions` | (extensive list) | Disable dangerous functions |

**Disabled PHP Functions**

The AutoInstaller disables 80+ dangerous PHP functions including: `exec`, `eval`, `system`, `shell_exec`, `passthru`, `proc_open`, `proc_close`, `popen`, `phpinfo`, `chmod`, `chdir`, `mkdir`, `rmdir`, `rename`, `move_uploaded_file`, and many others.

**Sources:** [install/AutoInstaller.pl:709-849](), [install/AutoInstaller.pl:721-782]()

## Database Setup Step

The `SQL` step creates the database schema, user account, and grants privileges.

```mermaid
graph TB
    StepSQL["step_sql_configure()"] --> GenCreds["Generate Credentials:<br/>sql_username='user_loa'<br/>sql_password=gen_random(15)<br/>sql_database='db_loa'"]
    
    GenCreds --> PromptConfig["Prompt for:<br/>sql_config_file<br/>sql_username<br/>sql_password<br/>sql_database<br/>sql_host (127.0.0.1)<br/>sql_port (3306)"]
    
    PromptConfig --> Templates["TEMPLATES Step"]
    
    Templates --> GenSQL["step_generate_templates():<br/>Process sql.template"]
    
    GenSQL --> ReplacePlaceholders["Replace in sql.template:<br/>###REPL_SQL_DB### → db_loa<br/>###REPL_SQL_USER### → user_loa<br/>###REPL_SQL_PASS### → (password)<br/>###REPL_SQL_TBL_*### → table names"]
    
    ReplacePlaceholders --> WriteReady["Write sql.ready"]
    
    WriteReady --> ProcessTemplates["step_process_templates()"]
    
    ProcessTemplates --> PromptRootPW["Prompt for MySQL<br/>root password"]
    
    PromptRootPW --> ImportSchema["Execute:<br/>mysql -u root -p$rootpw<br/>< sql.ready"]
    
    ImportSchema --> CreateDB["CREATE DATABASE db_loa"]
    CreateDB --> CreateUser["CREATE USER user_loa"]
    CreateUser --> GrantPrivs["GRANT SELECT, INSERT,<br/>UPDATE, DELETE"]
    GrantPrivs --> FlushPrivs["FLUSH PRIVILEGES"]
    
    FlushPrivs --> CreateTables["Create Tables:<br/>accounts, characters,<br/>monsters, familiars,<br/>friends, mail, bank,<br/>statistics, logs, etc."]
```

**Database Schema Overview**

The SQL template creates the following tables:

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `loa_accounts` | User accounts | `id`, `email`, `password`, `privileges`, `char_slot1-3` |
| `loa_characters` | Player characters | `id`, `account_id`, `name`, `race`, `level`, `stats`, `inventory` |
| `loa_monsters` | Monster encounters | `id`, `character_id`, `level`, `scope`, `stats` |
| `loa_familiars` | Pet companions | `id`, `character_id`, `name`, `rarity`, `hatched` |
| `loa_friends` | Friend relationships | `id`, `sender_id`, `recipient_id`, `friend_status` |
| `loa_mail` | In-game mail | `id`, `s_cid`, `r_cid`, `subject`, `message`, `folder` |
| `loa_bank` | Bank accounts | `id`, `character_id`, `gold_amount`, `interest_rate` |
| `loa_statistics` | Player statistics | `id`, `character_id`, `critical_hits`, `deaths`, etc. |
| `loa_globalchat` | Chat messages | `id`, `character_id`, `message`, `room` |
| `loa_logs` | System logs | `id`, `date`, `type`, `message` |
| `loa_banned` | Ban records | `id`, `account_id`, `expires`, `reason` |
| `loa_globals` | Global settings | `id`, `name`, `value` |

**Sources:** [install/AutoInstaller.pl:509-534](), [install/templates/sql.template:1-312]()

## SSL Certificate Configuration

The AutoInstaller supports three SSL certificate options configured during the `TEMPLATES` step.

```mermaid
graph TB
    StepSSL["step_vhost_ssl()"] --> DisplayCurrent["Display Current:<br/>ssl_fullcer<br/>ssl_privkey"]
    
    DisplayCurrent --> Menu["Show Menu:<br/>1. Self-signed<br/>2. Manual paths<br/>3. Let's Encrypt<br/>4. No SSL<br/>5. Skip<br/>6. Next<br/>7. Toggle HTTP→HTTPS"]
    
    Menu --> Choice1{"User Choice"}
    
    Choice1 -->|1| GenSelfSigned["Generate Self-Signed:<br/>openssl req -x509 -nodes<br/>-days 365 -newkey rsa:2048"]
    GenSelfSigned --> SaveCert["Save to:<br/>/etc/ssl/certs/$fqdn.crt<br/>/etc/ssl/private/$fqdn.key"]
    SaveCert --> EnableSSL1["ssl_enabled = 1"]
    
    Choice1 -->|2| PromptPaths["Prompt for:<br/>Certificate path<br/>Private key path"]
    PromptPaths --> ValidatePaths{"Files Exist?"}
    ValidatePaths -->|Yes| SetPaths["ssl_fullcer = $cert<br/>ssl_privkey = $pkey"]
    SetPaths --> EnableSSL2["ssl_enabled = 1"]
    ValidatePaths -->|No| PromptPaths
    
    Choice1 -->|3| ScheduleCertbot["Set run_certbot = 1<br/>(runs at end)"]
    ScheduleCertbot --> EnableSSL3["ssl_enabled = 1"]
    
    Choice1 -->|4| NoSSL["ssl_enabled = 0"]
    
    Choice1 -->|7| ToggleRedir{"redir_status"}
    ToggleRedir -->|ON| SetRedirOff["redir_status = 0"]
    ToggleRedir -->|OFF| SetRedirOn["redir_status = 1"]
    
    EnableSSL1 --> UpdateTemplates["Add to @replacements:<br/>###REPL_SSL_CERT###<br/>###REPL_SSL_KEY###<br/>###REPL_PROTOCOL###→https"]
    EnableSSL2 --> UpdateTemplates
    EnableSSL3 --> UpdateTemplates
    NoSSL --> UpdateTemplates
```

**SSL Configuration Variables**

| Variable | Description | Example |
|----------|-------------|---------|
| `ssl_enabled` | Whether SSL is enabled | `1` or `0` |
| `ssl_fullcer` | Full certificate chain path | `/etc/ssl/certs/loa.example.com.crt` |
| `ssl_privkey` | Private key path | `/etc/ssl/private/loa.example.com.key` |
| `redir_status` | HTTP to HTTPS redirect | `1` (enabled) or `0` (disabled) |
| `run_certbot` | Run certbot at end of install | `1` or `0` |

**Self-Signed Certificate Command**

```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/$fqdn.key \
  -out /etc/ssl/certs/$fqdn.crt \
  -subj "/CN=$fqdn/O=$fqdn/C=ZA" -batch
```

**Sources:** [install/AutoInstaller.pl:536-603]()

## Apache Module and Site Enablement

The `APACHE` step enables required Apache modules and virtual host configurations.

```mermaid
graph TB
    StepApache["step_apache_enables()"] --> CheckFPM{"php_fpm == 1?"}
    
    CheckFPM -->|Yes| EnableFPMConf["a2enconf php8.4-fpm"]
    EnableFPMConf --> DisableModPHP["a2dismod php*<br/>a2dismod mpm_prefork"]
    DisableModPHP --> EnableFPMMods["a2enmod proxy_fcgi<br/>a2enmod setenvif<br/>a2enmod mpm_event"]
    
    CheckFPM -->|No| EnableModPHP["a2enmod php8.4"]
    
    EnableFPMMods --> EnableCommon["a2enmod rewrite<br/>a2enmod ssl"]
    EnableModPHP --> EnableCommon
    
    EnableCommon --> EnableSite["a2ensite $fqdn"]
    
    EnableSite --> CheckSSL{"ssl_enabled?"}
    CheckSSL -->|Yes| EnableSSLSite["a2ensite ssl-$fqdn.conf"]
    CheckSSL -->|No| CheckSuccess
    
    EnableSSLSite --> CheckSuccess{"All Commands<br/>Exit 0?"}
    CheckSuccess -->|Yes| Success["SUCCESS: Apache configured"]
    CheckSuccess -->|No| Error["ERROR: See output"]
    
    Error --> PromptContinue{"Continue?"}
    PromptContinue -->|No| Exit["Die"]
```

**Apache Modules Enabled**

| Module | Purpose | Condition |
|--------|---------|-----------|
| `rewrite` | URL rewriting for clean URLs | Always |
| `ssl` | HTTPS support | Always |
| `php8.4` | PHP as Apache module | When `php_fpm=0` |
| `proxy_fcgi` | FastCGI proxy | When `php_fpm=1` |
| `setenvif` | Set environment variables | When `php_fpm=1` |
| `mpm_event` | Event-driven worker MPM | When `php_fpm=1` |

**Virtual Host Files**

- Non-SSL: `$apache_directory/sites-available/$fqdn.conf`
- SSL: `$apache_directory/sites-available/ssl-$fqdn.conf`

Both are generated from templates and enabled via `a2ensite`.

**Sources:** [install/AutoInstaller.pl:659-707]()

## File Permission Management

The `PERMS` step sets appropriate Unix permissions on all files and directories.

```mermaid
graph TB
    StepPerms["step_fix_permissions()"] --> SetBaseline["Baseline Permissions:<br/>find -type f -exec chmod 644<br/>find -type d -exec chmod 755"]
    
    SetBaseline --> ConfigFiles["Configuration Files (600):<br/>.env<br/>config.ini<br/>config.ini.default"]
    
    ConfigFiles --> ScriptFiles["Script Files (600):<br/>AutoInstaller.pl<br/>scripts/*"]
    
    ScriptFiles --> LogFiles["Log Files (640):<br/>system/logs/setup.log<br/>system/logs/gamelog.txt"]
    
    LogFiles --> ApacheConf["Apache Configs (644):<br/>$fqdn.conf<br/>ssl-$fqdn.conf"]
    
    ApacheConf --> Templates["Templates (600):<br/>templates/*"]
    
    Templates --> SetOwner["chown -R www-data:www-data<br/>$web_root"]
```

**Permission Scheme**

| File Type | Owner | Permissions | Octal | Description |
|-----------|-------|-------------|-------|-------------|
| Regular files | `www-data:www-data` | `rw-r--r--` | `644` | Web-readable files |
| Directories | `www-data:www-data` | `rwxr-xr-x` | `755` | Executable directories |
| Config files | `www-data:www-data` | `rw-------` | `600` | Sensitive configurations |
| Script files | `www-data:www-data` | `rw-------` | `600` | Installation scripts |
| Log files | `www-data:www-data` | `rw-r-----` | `640` | Log files |
| Apache configs | `root:root` | `rw-r--r--` | `644` | Server configurations |
| Templates | `www-data:www-data` | `rw-------` | `600` | Template files |

**Sources:** [install/AutoInstaller.pl:605-657]()

## Composer Dependency Installation

The `COMPOSER` step installs PHP dependencies defined in `composer.json`.

```mermaid
graph TB
    StepComposer["step_composer_pull()"] --> Confirm{"Confirm Run<br/>as www-data?"}
    
    Confirm -->|Yes| BuildCmd["Build Command:<br/>sudo -u www-data composer<br/>--working-dir=$web_root"]
    
    BuildCmd --> Install["composer install 2>/dev/null"]
    Install --> Update["composer update 2>/dev/null"]
    
    Update --> InstallPkgs["Install Packages:<br/>vlucas/phpdotenv<br/>monolog/monolog<br/>phpmailer/phpmailer<br/>composer/semver<br/>phpunit/phpunit<br/>symfony/serializer"]
    
    InstallPkgs --> GenerateAutoload["Generate Autoloader:<br/>vendor/autoload.php"]
    
    GenerateAutoload --> RestartServices["step_start_services():<br/>Restart Apache/MariaDB/PHP-FPM"]
```

**Installed Composer Packages**

From [composer.json:1-30]():

| Package | Version | Purpose |
|---------|---------|---------|
| `vlucas/phpdotenv` | ^5.6.1 | Environment variable loading |
| `monolog/monolog` | ^3.9 | Logging framework |
| `phpmailer/phpmailer` | ^7.0.0 | Email sending |
| `composer/semver` | ^3.4 | Semantic versioning |
| `phpunit/phpunit` | ^12.1 | Unit testing framework |
| `symfony/serializer` | ^7.2 | Object serialization |
| `contributte/monolog` | ^0.5.2 | Monolog integration |

**Sources:** [install/AutoInstaller.pl:851-859](), [composer.json:1-30]()

## Cleanup and Finalization

The `CLEANUP` step removes temporary files generated during installation.

```mermaid
graph TB
    Cleanup["clean_up()"] --> SearchWebroot["File::Find::find()<br/>Search $web_root"]
    
    SearchWebroot --> FindTemp["find_temp() Callback"]
    
    FindTemp --> MatchPatterns["Match Patterns:<br/>.env$<br/>^.loa-step<br/>.ready$<br/>ssl-$fqdn.conf<br/>$fqdn.conf<br/>php.list"]
    
    MatchPatterns --> RemoveWeb["unlink() Matched Files<br/>from $web_root"]
    
    RemoveWeb --> SearchApache["Search $apache_directory"]
    SearchApache --> RemoveApache["Remove temp Apache files"]
    
    RemoveApache --> SearchSources["Search /etc/apt/sources.list.d"]
    SearchSources --> RemoveSury["Remove Sury repo files:<br/>php.list"]
    
    RemoveSury --> RevertMode{"mode == 'revert'?"}
    
    RevertMode -->|Yes| DisableSites["a2dissite $fqdn<br/>a2dissite ssl-$fqdn"]
    DisableSites --> DropDB["mysql: DROP DATABASE<br/>DROP USER"]
    DropDB --> RemoveCron["Remove cron entries"]
    
    RevertMode -->|No| Success["SUCCESS: Cleaned up"]
    RemoveCron --> Success
```

**Files Removed by Cleanup**

| Pattern | Description | Location |
|---------|-------------|----------|
| `*.ready` | Generated template output files | `install/templates/` |
| `.env` | Environment file (moved to web root) | `install/` |
| `.loa-step*` | Step tracking files | `$web_root/` |
| `php.list` | Sury repository source file | `/etc/apt/sources.list.d/` |
| `ssl-$fqdn.conf` | Duplicate SSL config | `install/` |
| `$fqdn.conf` | Duplicate vhost config | `install/` |

**Sources:** [install/AutoInstaller.pl:1007-1041](), [install/AutoInstaller.pl:1043-1060]()

## Platform-Specific Bootstrap Scripts

For initial dependency installation, platform-specific bootstrap scripts prepare the system before running AutoInstaller.pl.

### Linux Bootstrap

The Linux bootstrap script detects the distribution and installs Perl dependencies:

```bash
#!/bin/bash
# bootstrap.sh

# Detect OS
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$ID
fi

# Install Perl and CPAN modules
case $OS in
    debian|ubuntu|kali)
        apt-get update
        apt-get install -y perl cpanminus
        cpanm Config::IniFiles Term::ReadKey File::Path Getopt::Long
        ;;
    alpine)
        apk add perl perl-dev
        cpan Config::IniFiles Term::ReadKey
        ;;
esac

# Create config.ini if needed
if [ ! -f config.ini ] && [ -f config.ini.default ]; then
    cp config.ini.default config.ini
fi

# Run AutoInstaller
perl AutoInstaller.pl
```

### Windows Bootstrap

The Windows PowerShell bootstrap handles XAMPP or individual component installation:

```mermaid
graph TB
    Bootstrap["bootstrap.ps1"] --> CheckMover{"mover.success<br/>exists?"}
    
    CheckMover -->|Yes| Resume["Resume after move"]
    CheckMover -->|No| PromptMethod["Prompt:<br/>1. XAMPP<br/>2. Individual Components"]
    
    PromptMethod -->|1| DownloadXAMPP["Download XAMPP installer<br/>xampp-8.2.12-0-VS16.exe"]
    DownloadXAMPP --> InstallXAMPP["Run installer:<br/>--mode unattended<br/>--disable-components mercury,tomcat"]
    
    PromptMethod -->|2| DownloadComponents["Download:<br/>PHP 8.4 (zip)<br/>Composer installer<br/>Strawberry Perl (msi)"]
    DownloadComponents --> ExtractPHP["Extract PHP to temp/php"]
    ExtractPHP --> InstallComposer["Install composer.exe"]
    InstallComposer --> InstallPerl["Silent install Perl:<br/>msiexec /i /quiet"]
    
    InstallXAMPP --> CreateMover["Create mover.ps1"]
    InstallPerl --> CreateMover
    
    CreateMover --> MoveFork["Fork process to run mover"]
    MoveFork --> MoveXAMPP["Move C:\xampp to webParent"]
    MoveXAMPP --> RemoveDefaultHtdocs["Remove default htdocs"]
    RemoveDefaultHtdocs --> MoveWebRoot["Move $webRoot to xampp/htdocs"]
    MoveWebRoot --> CreateMarker["Create mover.success"]
    
    CreateMarker --> Relaunch["Relaunch bootstrap.ps1"]
    Resume --> ContinueInstall["Continue to AutoInstaller.pl"]
```

**Sources:** [install/scripts/windows/bootstrap.ps1:1-8](), [install/scripts/windows/main.ps1:1-133]()

## Error Handling and Resumption

The AutoInstaller implements robust error handling and step resumption:

```mermaid
graph TB
    Start["Start Installation"] --> LoadConfig["Load config.ini"]
    
    LoadConfig --> CheckFQDN{"FQDN Config<br/>Exists?"}
    CheckFQDN -->|Yes| CheckStep{"step > 1?"}
    CheckFQDN -->|No| CreateNew["Create new config"]
    
    CheckStep -->|Yes| PromptResume["Prompt:<br/>1. Continue from step X<br/>2. Restart from beginning"]
    PromptResume -->|1| ResumeStep["Resume from cfg{step}"]
    PromptResume -->|2| ResetStep["Set step = SOFTWARE"]
    
    CheckStep -->|No| FirstRun["Run FIRSTRUN"]
    CreateNew --> FirstRun
    
    FirstRun --> RunStep["Execute Current Step"]
    ResumeStep --> RunStep
    ResetStep --> RunStep
    
    RunStep --> StepFunc["Call step_*() function"]
    StepFunc --> AskUser{"ask_user()<br/>Confirm action?"}
    
    AskUser -->|Yes| Execute["Execute step logic"]
    AskUser -->|No| NextStep["next_step():<br/>Increment cfg{step}"]
    
    Execute --> CheckError{"Exit Code<br/>== 0?"}
    CheckError -->|Yes| TellSuccess["tell_user('SUCCESS')"]
    CheckError -->|No| TellError["tell_user('ERROR')"]
    
    TellSuccess --> SaveProgress["handle_cfg():<br/>Write step to config.ini"]
    TellError --> SaveProgress
    
    SaveProgress --> NextStep
    NextStep --> StepComplete{"Step > HOSTS?"}
    
    StepComplete -->|No| RunStep
    StepComplete -->|Yes| Complete["Installation Complete"]
```

**Resumption Mechanism**

1. **Step Tracking**: Current step stored in `cfg{step}` and persisted to config.ini via `handle_cfg()`
2. **Config Preservation**: All user choices saved between steps
3. **Idempotent Operations**: Most steps check for existing configuration before modifying
4. **Manual Resume**: Use `--step` flag to jump to specific step: `perl AutoInstaller.pl --step APACHE`
5. **Partial Execution**: Use `--only` flag to run single step without continuing

**User Interaction Functions**

| Function | Purpose | Return Type |
|----------|---------|-------------|
| `ask_user($question, $default, $type)` | Prompt user for input | string or boolean |
| `tell_user($level, $message)` | Display colored status message | void |
| `handle_cfg(\%cfg, $mode, $fqdn)` | Save/load configuration | void |
| `next_step()` | Increment step counter and save | void |
| `const_to_name($step)` | Convert step number to name | string |
| `name_to_const($name)` | Convert step name to number | integer |

**Sources:** [install/AutoInstaller.pl:306-372](), [install/AutoInstaller.pl:1355-1395]()

## Post-Installation State

Upon successful completion, the AutoInstaller has configured a fully operational web application environment:

**Generated Files**

| File | Location | Purpose |
|------|----------|---------|
| `.env` | `$web_root/.env` | Environment variables for PHP |
| `.htaccess` | `$web_root/.htaccess` | URL rewriting rules |
| `constants.php` | `$web_root/system/constants.php` | PHP constants |
| `$fqdn.conf` | `$apache_directory/sites-available/` | HTTP virtual host |
| `ssl-$fqdn.conf` | `$apache_directory/sites-available/` | HTTPS virtual host |

**System State**

- Apache2 running with enabled modules: `rewrite`, `ssl`, `php8.4` or `proxy_fcgi`
- MariaDB running with database `db_loa` and user `user_loa`
- PHP-FPM running (if enabled)
- Cron jobs installed for `www-data` user
- SSL certificates configured
- File permissions set correctly
- Composer dependencies installed in `vendor/`
- Database schema populated with 12+ tables
- Application accessible at `http(s)://fqdn/`

**Next Steps**

After installation completion:

1. Test application access: `curl https://$fqdn/`
2. Verify database: `mysql -u user_loa -p db_loa`
3. Check Apache status: `systemctl status apache2`
4. Review logs: `tail -f /var/log/apache2/error.log`
5. Create first account at `https://$fqdn/register`

**Sources:** [install/AutoInstaller.pl:301-302]()