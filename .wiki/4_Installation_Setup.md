# Installation & Setup

<details>
<summary>Relevant source files</summary>

The following files were used as context for generating this wiki page:

- [.gitignore](.gitignore)
- [CONTRIBUTING.md](CONTRIBUTING.md)
- [README.md](README.md)
- [composer.json](composer.json)
- [composer.lock](composer.lock)
- [install/AutoInstaller.pl](install/AutoInstaller.pl)
- [install/scripts/windows/bootstrap.ps1](install/scripts/windows/bootstrap.ps1)
- [install/scripts/windows/main.ps1](install/scripts/windows/main.ps1)
- [install/templates/sql.template](install/templates/sql.template)
- [tbl_explore/table_explore.pl](tbl_explore/table_explore.pl)

</details>



This page provides comprehensive documentation for installing and configuring Legend of Aetheria on Linux and Windows platforms. The installation process is primarily automated through `AutoInstaller.pl`, which orchestrates software installation, database setup, web server configuration, and security hardening.

For information about configuring the application after installation, see [Configuration](#2.3). For web server-specific setup details, see [Web Server Setup](#2.4).

## Overview

Legend of Aetheria provides two installation methods:

| Method | Description | Use Case |
|--------|-------------|----------|
| **Automated** | Bootstrap scripts + `AutoInstaller.pl` | Recommended for fresh installations and development environments |
| **Manual** | Step-by-step configuration | Production servers with existing services, custom configurations |

The automated installation handles all aspects from repository clone to SSL-enabled web application, including database schema creation, dependency installation, and permission management.

Sources: [install/AutoInstaller.pl:1-303](), [README.md:5-67]()

## Installation Architecture

```mermaid
graph TB
    subgraph "Entry Point"
        User["Administrator"]
        Clone["git clone repository"]
    end
    
    subgraph "OS Detection & Bootstrap"
        Bootstrap{{"OS Detection"}}
        LinuxBoot["install/scripts/bootstrap.sh"]
        WinBoot["install/scripts/windows/bootstrap.ps1"]
        WinMain["install/scripts/windows/main.ps1"]
    end
    
    subgraph "Dependency Setup"
        CPAN["CPAN Modules<br/>Config::IniFiles<br/>Term::ReadKey<br/>File::Path<br/>Getopt::Long"]
        SuryRepo["Sury Repository<br/>PHP 8.4 packages"]
        BuildTools["Build Tools<br/>gcc, make"]
    end
    
    subgraph "AutoInstaller.pl"
        ConfigINI["config.ini"]
        MainScript["AutoInstaller.pl<br/>version 2.6.4.28"]
        
        StepConstants["Constants:<br/>FIRSTRUN=1<br/>SOFTWARE=2<br/>PHP=3<br/>SERVICES=4<br/>SQL=5<br/>OPENAI=6<br/>TEMPLATES=7<br/>APACHE=8<br/>PERMS=9<br/>COMPOSER=10<br/>CLEANUP=11<br/>HOSTS=12"]
    end
    
    subgraph "Execution Flow"
        StepFIRSTRUN["step_firstrun()"]
        StepSOFTWARE["step_install_software()"]
        StepPHP["step_php_configure()"]
        StepSERVICES["step_start_services()"]
        StepSQL["step_sql_configure()"]
        StepOPENAI["OpenAI API setup"]
        StepTEMPLATES["step_generate_templates()<br/>step_process_templates()"]
        StepAPACHE["step_apache_enables()"]
        StepPERMS["step_fix_permissions()"]
        StepCOMPOSER["step_composer_pull()"]
        StepCLEANUP["clean_up()"]
        StepHOSTS["step_update_hosts()"]
    end
    
    User --> Clone
    Clone --> Bootstrap
    Bootstrap -->|Linux| LinuxBoot
    Bootstrap -->|Windows| WinBoot
    
    LinuxBoot --> CPAN
    LinuxBoot --> SuryRepo
    LinuxBoot --> BuildTools
    
    WinBoot --> WinMain
    WinMain --> CPAN
    
    CPAN --> ConfigINI
    SuryRepo --> ConfigINI
    BuildTools --> ConfigINI
    
    ConfigINI --> MainScript
    StepConstants --> MainScript
    
    MainScript --> StepFIRSTRUN
    StepFIRSTRUN --> StepSOFTWARE
    StepSOFTWARE --> StepPHP
    StepPHP --> StepSERVICES
    StepSERVICES --> StepSQL
    StepSQL --> StepOPENAI
    StepOPENAI --> StepTEMPLATES
    StepTEMPLATES --> StepAPACHE
    StepAPACHE --> StepPERMS
    StepPERMS --> StepCOMPOSER
    StepCOMPOSER --> StepCLEANUP
    StepCLEANUP --> StepHOSTS
```

**Installation Execution Flow**: The installation process begins with OS-specific bootstrap scripts that prepare the environment, then hands control to `AutoInstaller.pl` which executes 12 sequential steps defined as Perl constants. Each step is resumable if interrupted, with state tracked in `config.ini`.

Sources: [install/AutoInstaller.pl:45-58](), [install/AutoInstaller.pl:115-303](), [install/scripts/windows/bootstrap.ps1:1-8]()

## Prerequisites

### System Requirements

| Component | Requirement | Notes |
|-----------|-------------|-------|
| **Operating System** | Linux (Debian/Ubuntu/Alpine) or Windows 10+ | Tested on Debian 12, Ubuntu 24.04 |
| **Root/Admin Access** | Required | Installation modifies system packages and services |
| **Web Server** | Apache 2.4+ | Configured automatically by installer |
| **Database** | MariaDB 10.3+ or MySQL 8.0+ | MariaDB recommended |
| **PHP** | 8.4 | Installed via Sury repository on Linux |
| **Perl** | 5.10+ | Required for AutoInstaller.pl |
| **Disk Space** | 2GB minimum | Includes dependencies and database |
| **Network** | Internet connection | For package downloads and Composer |

### Required Perl Modules

The bootstrap scripts install these CPAN modules automatically:

- `Config::IniFiles` - Configuration file parsing
- `Term::ReadKey` - Secure password input
- `File::Path` - Directory operations
- `Getopt::Long` - Command-line argument parsing
- `Data::Dumper` - Debugging output
- `File::Find` - File tree traversal
- `File::Copy` - File operations

Sources: [install/AutoInstaller.pl:9-18](), [README.md:69-77]()

### PHP Extensions

```mermaid
graph LR
    subgraph "PHP 8.4 Core"
        PHPCore["php8.4"]
        PHPCli["php8.4-cli"]
        PHPCommon["php8.4-common"]
    end
    
    subgraph "Database & Network"
        PHPMySQL["php8.4-mysql"]
        PHPCurl["php8.4-curl"]
    end
    
    subgraph "Data Processing"
        PHPXML["php8.4-xml"]
        PHPIntl["php8.4-intl"]
        PHPMbstring["php8.4-mbstring"]
    end
    
    subgraph "Optional Performance"
        PHPFPM["php8.4-fpm"]
        ApachePHP["libapache2-mod-php8.4"]
    end
    
    PHPCore --> PHPCli
    PHPCore --> PHPCommon
    PHPCommon --> PHPMySQL
    PHPCommon --> PHPCurl
    PHPCommon --> PHPXML
    PHPCommon --> PHPIntl
    PHPCommon --> PHPMbstring
    PHPCommon --> PHPFPM
    PHPCommon --> ApachePHP
```

**PHP Extension Dependencies**: The installer configures either PHP-FPM (with `mpm_event`) or mod_php (with `mpm_prefork`) based on user selection.

Sources: [install/AutoInstaller.pl:435-470](), [README.md:74-77]()

## Bootstrap Process

### Linux Bootstrap

The `bootstrap.sh` script prepares the system for AutoInstaller execution:

```bash
cd install/scripts
sudo bash bootstrap.sh
```

**Operations performed:**
1. Detects Linux distribution (Debian/Ubuntu/Alpine)
2. Adds Sury PHP repository for PHP 8.4
3. Installs build tools (`gcc`, `make`, `g++`)
4. Installs CPAN and required Perl modules
5. Creates `config.ini` from `config.ini.default` if not present

Sources: [README.md:39-43](), [install/AutoInstaller.pl:375-391]()

### Windows Bootstrap

Windows installation uses PowerShell scripts:

```powershell
cd install\scripts\windows
.\bootstrap.ps1
```

The Windows bootstrap provides two installation methods:

| Method | Script Flow | Components |
|--------|-------------|------------|
| **XAMPP** | `bootstrap.ps1` → `main.ps1` → installs XAMPP bundle | Apache, PHP, MySQL, Perl in single package |
| **Individual** | `bootstrap.ps1` → `main.ps1` → downloads separate installers | PHP 8.4, Composer, Strawberry Perl MSI |

**Mover Script**: After XAMPP installation, `mover.ps1` relocates files to the correct web root structure:
1. Moves `C:\xampp\` to target web parent directory
2. Removes default `htdocs\`
3. Moves LoA files into new `htdocs\`
4. Creates `mover.success` marker file

Sources: [install/scripts/windows/bootstrap.ps1:1-8](), [install/scripts/windows/main.ps1:1-133]()

## AutoInstaller Execution

### Command-Line Interface

```bash
cd install
sudo ./AutoInstaller.pl [OPTIONS]
```

**Command-line Options:**

| Option | Description | Example |
|--------|-------------|---------|
| `-f, --fqdn` | Specify fully qualified domain name | `--fqdn loa.example.com` |
| `-s, --step` | Resume from specific step (name or number) | `--step SQL` or `--step 5` |
| `-o, --only` | Execute only specified step, skip others | `--only --step TEMPLATES` |
| `-c, --config` | Use alternate config file | `--config myconfig.ini` |
| `-l, --list-steps` | Display all step names and numbers | |
| `-h, --help` | Show help message | |
| `-v, --version` | Show version (currently 2.6.4.28) | |

Sources: [install/AutoInstaller.pl:20-28](), [README.md:46-49]()

### Configuration File Structure

```mermaid
graph TB
    subgraph "config.ini Structure"
        ConfigFile["config.ini"]
        
        SectionColors["[colors]<br/>Terminal color codes"]
        SectionSQL["[sql_tables]<br/>Table name constants"]
        SectionAll["[all]<br/>Default values"]
        SectionFQDN["[fqdn.example.com]<br/>Domain-specific config"]
    end
    
    subgraph "Config Access"
        TiedHash["%ini tied hash<br/>Config::IniFiles"]
        CFGHash["%cfg focused domain"]
        GLBHash["%glb global values"]
        SQLHash["%sql table names"]
        CLRHash["%clr color codes"]
    end
    
    subgraph "Config Operations"
        HandleCFG["handle_cfg()<br/>CFG_R_MAIN=1<br/>CFG_R_DOMAIN=3<br/>CFG_W_MAIN=4<br/>CFG_W_DOMAIN=6"]
    end
    
    ConfigFile --> SectionColors
    ConfigFile --> SectionSQL
    ConfigFile --> SectionAll
    ConfigFile --> SectionFQDN
    
    SectionColors --> CLRHash
    SectionSQL --> SQLHash
    SectionAll --> TiedHash
    SectionFQDN --> CFGHash
    
    TiedHash --> HandleCFG
    CFGHash --> HandleCFG
```

**Configuration Management**: The `config.ini` file uses INI format with domain-specific sections. The `Config::IniFiles` module provides tied hash access, while `handle_cfg()` manages read/write operations with mode constants.

Sources: [install/AutoInstaller.pl:60-113](), [install/AutoInstaller.pl:89-109]()

### Step Constants and State Management

The installer defines 12 steps as Perl constants:

```perl
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
    HOSTS     => 12,
};
```

**State Tracking**: Current step stored in `$cfg{step}`, persisted to `config.ini` after each successful step completion. This enables resumption from interruption points.

Sources: [install/AutoInstaller.pl:45-58]()

## Installation Steps

### Step 1: FIRSTRUN - Initial Configuration

```mermaid
sequenceDiagram
    participant User
    participant AutoInstaller as "AutoInstaller.pl"
    participant ConfigINI as "config.ini"
    participant FileSystem as "File System"
    
    User->>AutoInstaller: Execute with/without --fqdn
    AutoInstaller->>AutoInstaller: Check $ENV{USER} == 'root'
    
    alt FQDN not provided
        AutoInstaller->>User: Prompt for FQDN
        User->>AutoInstaller: Enter fqdn (e.g. loa.example.com)
    end
    
    AutoInstaller->>AutoInstaller: $fqdn = lc $fqdn
    AutoInstaller->>User: ask_user("web_root location")
    User->>AutoInstaller: /var/www/html/loa
    
    AutoInstaller->>FileSystem: Check if web_root exists
    
    alt web_root does not exist
        AutoInstaller->>User: ask_user("Create and move files?")
        User->>AutoInstaller: yes
        AutoInstaller->>FileSystem: make_path($cfg{web_root})
        AutoInstaller->>FileSystem: Create /tmp/mover.pl
        AutoInstaller->>AutoInstaller: fork() and exec mover script
        AutoInstaller->>AutoInstaller: exit parent process
    end
    
    AutoInstaller->>AutoInstaller: populate_hashdata()
    AutoInstaller->>AutoInstaller: get_sysinfo()
    
    AutoInstaller->>ConfigINI: Check if $ini{$fqdn} exists
    
    alt Config exists
        AutoInstaller->>User: ask_user("Load configuration?")
        alt User confirms
            AutoInstaller->>ConfigINI: Load %cfg from $ini{$fqdn}
            alt $cfg{step} > 1
                AutoInstaller->>User: Continue from step X or restart?
            end
        else User declines
            AutoInstaller->>ConfigINI: delete $ini{$fqdn}
            AutoInstaller->>ConfigINI: Create new empty section
        end
    else Config does not exist
        AutoInstaller->>ConfigINI: Create $ini{$fqdn} = {}
        AutoInstaller->>ConfigINI: Set $ini{$fqdn}{step} = SOFTWARE
    end
    
    AutoInstaller->>ConfigINI: $cfg{fqdn} = $fqdn
    AutoInstaller->>AutoInstaller: Verify location matches web_root
    AutoInstaller->>AutoInstaller: next_step()
```

**First Run Logic**: The `step_firstrun()` function handles initial configuration including FQDN setup, web root validation, and configuration persistence. If files are in wrong location, creates a mover script that forks to relocate files and relaunch installer.

Sources: [install/AutoInstaller.pl:116-182](), [install/AutoInstaller.pl:306-372]()

### Step 2: SOFTWARE - Package Installation

**Function**: `step_install_software()` and `step_webserver_configure()`

```mermaid
graph TB
    subgraph "Package Detection"
        CheckSury{"Sury repo<br/>exists?"}
        DetectDistro["Detect distro:<br/>$cfg{distro}"]
        DetectPM["Detect package<br/>manager: $cfg{pm_cmd}"]
    end
    
    subgraph "Sury Setup"
        SuryScript["scripts/sury_setup_ubnt.sh<br/>or<br/>scripts/sury_setup_deb.sh"]
    end
    
    subgraph "PHP Version Detection"
        FindPHP["php --version | head -n1"]
        ParseVer["Extract version via regex:<br/>/PHP (\d+\.\d+)/"]
        DefaultVer["Default to 8.4<br/>(or 8.3 for Alpine)"]
    end
    
    subgraph "Package Array"
        DebPackages["deb:php8.4<br/>deb:php8.4-cli<br/>deb:php8.4-mysql<br/>deb:mariadb-server<br/>deb:apache2<br/>deb:letsencrypt<br/>deb:composer"]
        AlpPackages["alp:php83<br/>alp:mariadb<br/>alp:apache2<br/>alp:certbot"]
        FPMChoice{"Use PHP-FPM?"}
        AddFPM["Add php8.4-fpm<br/>to @packages"]
    end
    
    subgraph "Installation"
        FilterPackages["grep /$cfg{software}/<br/>from @packages"]
        BuildCommand["$cfg{pm_cmd} + filtered packages"]
        Execute["Execute: $sw_cmd<br/>Redirect to /dev/null"]
    end
    
    CheckSury -->|No| SuryScript
    CheckSury -->|Yes| DetectDistro
    SuryScript --> DetectDistro
    DetectDistro --> DetectPM
    DetectPM --> FindPHP
    
    FindPHP --> ParseVer
    ParseVer --> DefaultVer
    DefaultVer --> DebPackages
    DefaultVer --> AlpPackages
    
    DebPackages --> FPMChoice
    AlpPackages --> FPMChoice
    FPMChoice -->|Yes| AddFPM
    FPMChoice -->|No| FilterPackages
    AddFPM --> FilterPackages
    
    FilterPackages --> BuildCommand
    BuildCommand --> Execute
```

**Software Installation Process**: Detects distribution, adds Sury repository if needed, determines PHP version, builds package array with distribution prefixes (`deb:` or `alp:`), filters by current OS, and executes installation command.

**Web Server Configuration** (`step_webserver_configure()`):
- Prompts for Apache directory (default: `/etc/apache2`)
- Sets virtual host file paths: `$cfg{virthost_conf_file}` and `$cfg{virthost_conf_file_ssl}`
- Configures Apache user (`www-data` on Linux)
- Sets HTTPS port (default: 443) and HTTP port (default: 80)
- Prompts for admin email (default: `webmaster@$fqdn`)

Sources: [install/AutoInstaller.pl:374-487](), [install/AutoInstaller.pl:489-507]()

### Step 3: PHP - Runtime Configuration

**Function**: `step_php_configure()`

```mermaid
graph TB
    subgraph "Binary Detection"
        DetectBinary["Detect PHP binary:<br/>which php$cfg{php_version}"]
        ConfirmBinary{"User confirms<br/>binary path?"}
        ManualBinary["ask_user()<br/>manual path entry"]
    end
    
    subgraph "INI File Location"
        DetermineINI["Determine php.ini path"]
        FPMCheck{"php_fpm == 1?"}
        FPMPath["/etc/php/8.4/fpm/php.ini"]
        ApachePath["/etc/php/8.4/apache2/php.ini"]
        ConfirmINI{"User confirms<br/>INI path?"}
        ManualINI["ask_user()<br/>manual INI path"]
    end
    
    subgraph "Security Hardening"
        INIPatch["%ini_patch hash:<br/>expose_php=off<br/>error_reporting=E_NONE<br/>display_errors=Off<br/>allow_url_fopen=Off<br/>allow_url_include=Off<br/>disable_functions=..."]
        
        SessionSec["Session Security:<br/>use_strict_mode=1<br/>cookie_secure=1<br/>cookie_httponly=1<br/>cookie_samesite=Strict<br/>cookie_domain=$cfg{fqdn}"]
        
        DisabledFuncs["Disabled Functions:<br/>exec, eval, shell_exec<br/>system, passthru, proc_open<br/>phpinfo, show_source<br/>...and 50+ others"]
    end
    
    subgraph "INI Patching"
        LoadINI["Open $cfg{php_ini}"]
        IteratePatch["foreach %ini_patch"]
        SearchReplace["Regex: s/;?$key ?=.*/$key = $value/g"]
        WriteINI["file_write($cfg{php_ini})"]
    end
    
    DetectBinary --> ConfirmBinary
    ConfirmBinary -->|No| ManualBinary
    ConfirmBinary -->|Yes| DetermineINI
    ManualBinary --> DetermineINI
    
    DetermineINI --> FPMCheck
    FPMCheck -->|Yes| FPMPath
    FPMCheck -->|No| ApachePath
    FPMPath --> ConfirmINI
    ApachePath --> ConfirmINI
    
    ConfirmINI -->|Yes| INIPatch
    ConfirmINI -->|No| ManualINI
    ManualINI --> INIPatch
    
    INIPatch --> SessionSec
    SessionSec --> DisabledFuncs
    DisabledFuncs --> LoadINI
    
    LoadINI --> IteratePatch
    IteratePatch --> SearchReplace
    SearchReplace --> WriteINI
```

**PHP Security Hardening**: The installer patches `php.ini` with 12 security-focused directives. The `%ini_patch` hash contains key-value pairs that are applied via regex substitution, uncommenting disabled lines and adding missing directives.

**Disabled Functions** (50+ total): `apache_child_terminate`, `apache_setenv`, `chdir`, `chmod`, `eval`, `exec`, `passthru`, `phpinfo`, `popen`, `proc_open`, `shell_exec`, `system`, and many others to prevent shell access and information disclosure.

Sources: [install/AutoInstaller.pl:709-849](), [README.md:237-258]()

### Step 4: SERVICES - Daemon Management

**Function**: `step_start_services()`

```mermaid
graph LR
    subgraph "Service Array"
        Services["@services = ('mariadb', 'apache2')"]
        CheckFPM{"$cfg{php_fpm} == 1?"}
        AddFPM["push @services,<br/>'php8.4-fpm'"]
    end
    
    subgraph "Service Commands"
        DetectSVC["$cfg{svc_cmd}:<br/>'systemctl' or 'service'"]
        BuildOrder["Build command:<br/>systemctl: 'start $service'<br/>service: '$service start'"]
    end
    
    subgraph "Execution"
        LoopServices["foreach @services"]
        StartService["Execute:<br/>`$cfg{svc_cmd} $order`"]
    end
    
    Services --> CheckFPM
    CheckFPM -->|Yes| AddFPM
    CheckFPM -->|No| DetectSVC
    AddFPM --> DetectSVC
    
    DetectSVC --> BuildOrder
    BuildOrder --> LoopServices
    LoopServices --> StartService
```

**Service Startup**: Starts core services in order: MariaDB, Apache2, and optionally PHP-FPM. The service command syntax is adapted based on init system (`systemctl` vs `service`).

Sources: [install/AutoInstaller.pl:952-970]()

### Step 5: SQL - Database Initialization

**Function**: `step_sql_configure()`

**Configuration Variables:**

| Variable | Default Value | Purpose |
|----------|---------------|---------|
| `$cfg{sql_username}` | `user_loa` | Database user account |
| `$cfg{sql_password}` | `gen_random(15)` | 15-character random password |
| `$cfg{sql_database}` | `db_loa` | Database name |
| `$cfg{sql_host}` | `127.0.0.1` | Database server address |
| `$cfg{sql_port}` | `3306` | MySQL/MariaDB port |
| `$cfg{sql_config_file}` | `/etc/mysql/mariadb/mariadb.conf.d/50-server.cnf` | Server configuration file |

**Schema Import** (executed in `step_process_templates()`):
1. Prompts for root MySQL password
2. Constructs command: `mysql -u root -p$password < sql.template.ready`
3. Executes schema import
4. Creates user with `GRANT SELECT, INSERT, UPDATE, DELETE` privileges

Sources: [install/AutoInstaller.pl:509-534](), [install/AutoInstaller.pl:896-926]()

### Step 6: OPENAI - API Configuration

```mermaid
graph LR
    AskEnable["ask_user('Enable OpenAI features?')"]
    EnableCheck{{"$cfg{openai_enable}?"}}
    AskKey["ask_user('Enter OpenAI API key')"]
    SetKey["$cfg{openai_apikey} = user_input"]
    SkipKey["$cfg{openai_apikey} = 'unset'"]
    NextStep["next_step()"]
    
    AskEnable --> EnableCheck
    EnableCheck -->|Yes| AskKey
    EnableCheck -->|No| SkipKey
    AskKey --> SetKey
    SetKey --> NextStep
    SkipKey --> NextStep
```

**OpenAI Integration**: Prompts user to enable OpenAI features and provide API key. If disabled, sets key to `'unset'`. The key is later written to `.env` template.

Sources: [install/AutoInstaller.pl:224-234]()

### Step 7: TEMPLATES - File Generation

**Functions**: `step_vhost_ssl()`, `parse_replacements()`, `step_generate_templates()`, `step_process_templates()`

```mermaid
graph TB
    subgraph "SSL Certificate Setup"
        SSLPrompt["step_vhost_ssl()<br/>7 options menu"]
        Opt1["1. Generate self-signed<br/>openssl req -x509"]
        Opt2["2. Manual cert paths"]
        Opt3["3. Let's Encrypt<br/>$cfg{run_certbot}=1"]
        Opt4["4. No SSL"]
        Opt7["7. Toggle HTTP→HTTPS redirect<br/>$cfg{redir_status}"]
    end
    
    subgraph "Template Files"
        EnvTemplate["install/templates/env.template"]
        HtaccessTemplate["install/templates/htaccess.template"]
        SQLTemplate["install/templates/sql.template"]
        CrontabTemplate["install/templates/cron.template"]
        VhostTemplate["install/templates/virthost.template"]
        VhostSSLTemplate["install/templates/virthost_ssl.template"]
        ConstantsTemplate["install/templates/constants.template"]
    end
    
    subgraph "Replacement Tokens"
        Replacements["@replacements array:<br/>###REPL_FQDN###<br/>###REPL_WEB_ROOT###<br/>###REPL_SQL_USER###<br/>###REPL_SQL_PASS###<br/>###REPL_SQL_DB###<br/>###REPL_SQL_HOST###<br/>###REPL_PROTOCOL###<br/>###REPL_OPENAI_KEY###"]
    end
    
    subgraph "Generation Process"
        OpenTemplate["open template file"]
        ReadContents["$contents = read file"]
        IterateReplacements["foreach @replacements"]
        SplitToken["($search, $replace) = split '%%%'"]
        RegexReplace["$contents =~ s/$search/$replace/gm"]
        WriteReady["file_write($template.ready)"]
    end
    
    subgraph "Processing"
        CopyENV["cp env.template.ready → .env"]
        CopyHtaccess["cp htaccess.template.ready → .htaccess"]
        CopyVhost["cp virthost.template.ready → $cfg{virthost_conf_file}"]
        CopyVhostSSL["cp virthost_ssl.template.ready → $cfg{virthost_conf_file_ssl}"]
        ImportSQL["mysql -u root < sql.template.ready"]
        InstallCron["crontab -l + cron.template.ready | crontab -"]
    end
    
    SSLPrompt --> Opt1
    SSLPrompt --> Opt2
    SSLPrompt --> Opt3
    SSLPrompt --> Opt4
    SSLPrompt --> Opt7
    
    Opt1 --> Replacements
    Opt2 --> Replacements
    Opt3 --> Replacements
    
    EnvTemplate --> OpenTemplate
    HtaccessTemplate --> OpenTemplate
    SQLTemplate --> OpenTemplate
    CrontabTemplate --> OpenTemplate
    VhostTemplate --> OpenTemplate
    VhostSSLTemplate --> OpenTemplate
    ConstantsTemplate --> OpenTemplate
    
    Replacements --> OpenTemplate
    OpenTemplate --> ReadContents
    ReadContents --> IterateReplacements
    IterateReplacements --> SplitToken
    SplitToken --> RegexReplace
    RegexReplace --> WriteReady
    
    WriteReady --> CopyENV
    WriteReady --> CopyHtaccess
    WriteReady --> CopyVhost
    WriteReady --> CopyVhostSSL
    WriteReady --> ImportSQL
    WriteReady --> InstallCron
```

**Template System**: Uses search-and-replace tokens (format: `###REPL_VARIABLE###%%%(value)`) to generate configuration files. The `parse_replacements()` function builds the replacement array from `%cfg` variables, then `step_generate_templates()` performs regex substitution on all template files, creating `.ready` versions.

**Self-Signed Certificate Generation**:
```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/$fqdn.key \
  -out /etc/ssl/certs/$fqdn.crt \
  -subj '/CN=$fqdn/O=$fqdn/C=ZA' -batch
```

Sources: [install/AutoInstaller.pl:236-263](), [install/AutoInstaller.pl:536-603](), [install/AutoInstaller.pl:861-894](), [install/AutoInstaller.pl:896-950]()

### Step 8: APACHE - Module and Site Activation

**Function**: `step_apache_enables()`

```mermaid
graph TB
    subgraph "Module Configuration"
        BaseMods["Base modules:<br/>'rewrite ssl'"]
        FPMCheck{"$cfg{php_fpm} == 1?"}
        FPMConf["a2enconf php8.4-fpm"]
        DisMod["a2dismod php* mpm_prefork"]
        FPMMods["Add: 'proxy_fcgi<br/>setenvif mpm_event'"]
        ModPHP["Add: 'php8.4'"]
        EnableMods["a2enmod $mods"]
    end
    
    subgraph "Site Activation"
        EnableSite["a2ensite $cfg{fqdn}"]
        SSLCheck{"$cfg{ssl_enabled}?"}
        EnableSSLSite["a2ensite ssl-$cfg{fqdn}.conf"]
    end
    
    subgraph "Result Tracking"
        SuccessVar["$success = 1000"]
        AddExitCodes["$success += $?<br/>for each command"]
        CheckTotal{"$success == 1000?"}
        ReportSuccess["tell_user('SUCCESS')"]
        ReportError["tell_user('ERROR')"]
    end
    
    BaseMods --> FPMCheck
    FPMCheck -->|Yes| FPMConf
    FPMCheck -->|No| ModPHP
    FPMConf --> DisMod
    DisMod --> FPMMods
    FPMMods --> EnableMods
    ModPHP --> EnableMods
    
    EnableMods --> EnableSite
    EnableSite --> SSLCheck
    SSLCheck -->|Yes| EnableSSLSite
    SSLCheck -->|No| SuccessVar
    EnableSSLSite --> SuccessVar
    
    SuccessVar --> AddExitCodes
    AddExitCodes --> CheckTotal
    CheckTotal -->|Yes| ReportSuccess
    CheckTotal -->|No| ReportError
```

**Apache Configuration**: Enables required modules and sites. For PHP-FPM, disables `mpm_prefork` and mod_php, enables `mpm_event` and FastCGI proxy. For standard mod_php, enables `php8.4` module. Always enables `rewrite` and `ssl` modules.

Sources: [install/AutoInstaller.pl:659-707](), [README.md:188-211]()

### Step 9: PERMS - Permission Management

**Function**: `step_fix_permissions()`

**Permission Strategy:**

| Target | Operation | Permissions | Owner:Group |
|--------|-----------|-------------|-------------|
| All files | `find -type f -exec chmod 644` | `rw-r--r--` | `www-data:www-data` |
| All directories | `find -type d -exec chmod 755` | `rwxr-xr-x` | `www-data:www-data` |
| `.env` | `chmod 0600` | `rw-------` | `www-data:www-data` |
| `config.ini` | `chmod 0600` | `rw-------` | `www-data:www-data` |
| `AutoInstaller.pl` | `chmod 0600` | `rw-------` | `www-data:www-data` |
| Scripts | `chmod 0600` | `rw-------` | `www-data:www-data` |
| Log files | `chmod 0640` | `rw-r-----` | `www-data:www-data` |
| Apache configs | `chmod 0644` | `rw-r--r--` | `www-data:www-data` |
| Templates | `chmod 0600` | `rw-------` | `www-data:www-data` |

**Execution Order:**
1. Baseline: `find $web_root -type f -exec chmod 644 {} +`
2. Directories: `find $web_root -type d -exec chmod 755 {} +`
3. Individual file hardening (config, scripts, logs)
4. Ownership: `chown -R www-data:www-data $web_root`

Sources: [install/AutoInstaller.pl:605-657](), [README.md:20-22]()

### Step 10: COMPOSER - Dependency Installation

**Function**: `step_composer_pull()`

```bash
sudo -u www-data composer --working-dir "$cfg{web_root}" install
sudo -u www-data composer --working-dir "$cfg{web_root}" update
```

**PHP Dependencies** (from `composer.json`):

| Package | Version | Purpose |
|---------|---------|---------|
| `vlucas/phpdotenv` | ^5.6.1 | Environment variable management |
| `monolog/monolog` | ^3.9 | Logging framework |
| `phpmailer/phpmailer` | ^7.0.0 | Email sending |
| `composer/semver` | ^3.4 | Semantic versioning |
| `phpunit/phpunit` | ^12.1 | Unit testing framework |
| `symfony/serializer` | ^7.2 | Object serialization |
| `contributte/monolog` | ^0.5.2 | Nette integration for Monolog |

**Installation Process**: Executes as `www-data` user to ensure correct file ownership. Runs both `install` (for initial setup) and `update` (for package updates).

Sources: [install/AutoInstaller.pl:851-859](), [composer.json:1-30](), [README.md:263-270]()

### Step 11: CLEANUP - Temporary File Removal

**Function**: `clean_up()`

**Cleanup Targets** (identified by `find_temp()`):

```perl
my @files_to_remove = (
    '\.env$',
    '^\.loa-step',
    '\.ready$',
    "ssl-$cfg{fqdn}.conf\$",
    "$cfg{fqdn}.conf\$",
    'php.list$'
);
```

**Cleanup Operations:**
- Removes `.ready` template files
- Removes `.loa-step` progress markers
- Optionally reverts installation (via `--revert` mode):
  - Disables Apache sites
  - Drops database and user
  - Removes Sury repository entries
  - Removes crontab entries

Sources: [install/AutoInstaller.pl:1007-1041](), [install/AutoInstaller.pl:1043-1060]()

### Step 12: HOSTS - Hosts File Update

**Function**: `step_update_hosts()`

```mermaid
graph LR
    ReadHosts["read_hosts()<br/>Parse /etc/hosts"]
    ChooseHost["choose_host()<br/>Select IP for FQDN"]
    WriteHosts["write_hosts()<br/>Update /etc/hosts"]
    
    ReadHosts --> ChooseHost
    ChooseHost --> WriteHosts
```

**Hosts File Management**: Updates `/etc/hosts` to include FQDN mapping for local testing. Useful for development environments where DNS is not configured.

Sources: [install/AutoInstaller.pl:972-979]()

## Template System Details

### Template File Locations

All templates reside in `install/templates/` directory:

| Template File | Output Location | Purpose |
|---------------|-----------------|---------|
| `env.template` | `$web_root/.env` | Database credentials, OpenAI key |
| `htaccess.template` | `$web_root/.htaccess` | URL rewriting, security headers |
| `sql.template` | Imported to database | Database schema (20+ tables) |
| `cron.template` | User crontab | Scheduled task configuration |
| `virthost.template` | `/etc/apache2/sites-available/$fqdn.conf` | HTTP virtual host |
| `virthost_ssl.template` | `/etc/apache2/sites-available/ssl-$fqdn.conf` | HTTPS virtual host |
| `constants.template` | `$web_root/system/constants.php` | PHP constants for paths/settings |

Sources: [install/AutoInstaller.pl:872-878]()

### Replacement Token Format

Tokens use the format `###REPL_VARIABLE###` and are defined in `parse_replacements()`:

```mermaid
graph LR
    subgraph "Configuration Variables"
        CFGFQDN["$cfg{fqdn}"]
        CFGWEBROOT["$cfg{web_root}"]
        CFGSQLUSER["$cfg{sql_username}"]
        CFGSQLPASS["$cfg{sql_password}"]
        CFGSQLDB["$cfg{sql_database}"]
        CFGSQLHOST["$cfg{sql_host}"]
        CFGSQLPORT["$cfg{sql_port}"]
        CFGOPENAI["$cfg{openai_apikey}"]
    end
    
    subgraph "Replacement Tokens"
        REPLFQDN["###REPL_FQDN###"]
        REPLWEBROOT["###REPL_WEB_ROOT###"]
        REPLSQLUSER["###REPL_SQL_USER###"]
        REPLSQLPASS["###REPL_SQL_PASS###"]
        REPLSQLDB["###REPL_SQL_DB###"]
        REPLSQLHOST["###REPL_SQL_HOST###"]
        REPLSQLPORT["###REPL_SQL_PORT###"]
        REPLOPENAI["###REPL_OPENAI_KEY###"]
    end
    
    subgraph "Generated Files"
        ENVREADY["env.template.ready"]
        SQLREADY["sql.template.ready"]
        VHOSTREADY["virthost.template.ready"]
    end
    
    CFGFQDN --> REPLFQDN
    CFGWEBROOT --> REPLWEBROOT
    CFGSQLUSER --> REPLSQLUSER
    CFGSQLPASS --> REPLSQLPASS
    CFGSQLDB --> REPLSQLDB
    CFGSQLHOST --> REPLSQLHOST
    CFGSQLPORT --> REPLSQLPORT
    CFGOPENAI --> REPLOPENAI
    
    REPLFQDN --> ENVREADY
    REPLSQLUSER --> SQLREADY
    REPLWEBROOT --> VHOSTREADY
```

**Token Processing**: The `step_generate_templates()` function iterates through `@replacements`, splits each entry on `%%%` delimiter to extract search/replace pairs, then applies regex `s/$search/$replace/gm` to template contents.

Sources: [install/AutoInstaller.pl:886-891]()

### Database Schema Template

The `sql.template` creates 12 tables:

| Table Name | Purpose | Key Columns |
|------------|---------|-------------|
| `###REPL_SQL_TBL_ACCOUNTS###` | User accounts | `id`, `email`, `password`, `privileges`, `char_slot1-3` |
| `###REPL_SQL_TBL_CHARACTERS###` | Character data | `id`, `account_id`, `name`, `race`, `stats`, `inventory` |
| `###REPL_SQL_TBL_MONSTERS###` | Monster instances | `id`, `character_id`, `level`, `stats`, `scope` |
| `###REPL_SQL_TBL_FAMILIARS###` | Pet companions | `id`, `character_id`, `name`, `rarity`, `hatched` |
| `###REPL_SQL_TBL_FRIENDS###` | Friend relationships | `sender_id`, `recipient_id`, `friend_status` |
| `###REPL_SQL_TBL_MAIL###` | In-game mail | `s_cid`, `r_cid`, `subject`, `message`, `folder` |
| `###REPL_SQL_TBL_BANK###` | Banking system | `account_id`, `character_id`, `gold_amount`, `interest_rate` |
| `###REPL_SQL_TBL_BANNED###` | Ban tracking | `account_id`, `date`, `expires`, `reason` |
| `###REPL_SQL_TBL_GLOBALCHAT###` | Chat messages | `character_id`, `message`, `room`, `when` |
| `###REPL_SQL_TBL_GLOBALS###` | Global variables | `name`, `value` |
| `###REPL_SQL_TBL_LOGS###` | System logs | `date`, `type`, `message`, `ip` |
| `###REPL_SQL_TBL_STATISTICS###` | Player stats | `character_id`, `critical_hits`, `deaths`, `monster_kills` |

**User Creation**: After table creation, the template creates the database user:
```sql
DROP USER IF EXISTS ###REPL_SQL_USER###;
CREATE USER ###REPL_SQL_USER###;
GRANT SELECT, INSERT, UPDATE, DELETE ON ###REPL_SQL_DB###.* 
  TO ###REPL_SQL_USER### IDENTIFIED BY '###REPL_SQL_PASS###';
FLUSH PRIVILEGES;
```

Sources: [install/templates/sql.template:1-312]()

## Manual Installation

For production environments or custom configurations, manual installation provides granular control.

### Manual Prerequisites

1. **Clone Repository**:
```bash
cd /var/www/html
git clone https://github.com/Ziddykins/LegendOfAetheria
cd LegendOfAetheria
```

2. **Set Base Permissions**:
```bash
sudo chown -R www-data:www-data .
find . -type f -exec chmod 0644 {} +
find . -type d -exec chmod 0755 {} +
```

Sources: [README.md:11-22]()

### Manual Software Installation

```bash
# Debian/Ubuntu
sudo apt update && sudo apt upgrade -y
sudo apt install -y \
  php8.4 php8.4-cli php8.4-common php8.4-curl php8.4-dev \
  php8.4-mysql php8.4-xml php8.4-intl php8.4-mbstring \
  mariadb-server apache2 libapache2-mod-php8.4 \
  composer letsencrypt python-is-python3 python3-certbot-apache
```

For PHP-FPM setup:
```bash
sudo apt install -y php8.4-fpm
sudo a2dismod mpm_prefork php*
sudo a2enmod headers http2 ssl setenvif mpm_event proxy_fcgi
sudo a2enconf php8.4-fpm
```

Sources: [README.md:69-203]()

### Manual Template Processing

```bash
cd install
sudo perl AutoInstaller.pl --step TEMPLATES --fqdn your.domain.com --only
```

This generates all `.ready` template files without executing full installation. Import SQL schema:

```bash
mysql -u root -p < install/templates/sql.template.ready
```

Sources: [README.md:78-87](), [README.md:272-286]()

### Manual Apache Virtual Host

**Non-SSL Configuration** (`/etc/apache2/sites-available/loa.example.com.conf`):
```apache
<VirtualHost loa.example.com:80>
    ServerName loa.example.com
    ServerAdmin admin@example.com
    DocumentRoot /var/www/html/LegendOfAetheria

    LogLevel info ssl:warn
    ErrorLog ${APACHE_LOG_DIR}/loa.example.com-error.log
    CustomLog ${APACHE_LOG_DIR}/loa.example.com-access.log combined
</VirtualHost>
```

**SSL Configuration** (`/etc/apache2/sites-available/ssl-loa.example.com.conf`):
```apache
<IfModule mod_ssl.c>
    <VirtualHost loa.example.com:443>
        ServerName loa.example.com
        DocumentRoot /var/www/html/LegendOfAetheria
        
        SSLCertificateFile /etc/letsencrypt/live/example.com/fullchain.pem
        SSLCertificateKeyFile /etc/letsencrypt/live/example.com/privkey.pem
        Include /etc/letsencrypt/options-ssl-apache.conf
        
        Header always set Strict-Transport-Security "max-age=63072000"
    </VirtualHost>
</IfModule>
```

Enable sites:
```bash
sudo a2ensite loa.example.com ssl-loa.example.com
sudo systemctl reload apache2
```

Sources: [README.md:89-191]()

### Manual SSL Certificate

**Let's Encrypt**:
```bash
sudo certbot -d loa.example.com --apache
```

**Self-Signed**:
```bash
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/loa.key \
  -out /etc/ssl/certs/loa.crt \
  -subj '/CN=loa.local/O=LoA/C=US' -batch
```

Update `/etc/hosts` for local testing:
```
127.0.1.1    loa.local
```

Sources: [README.md:213-235]()

### Manual Cron Jobs

```bash
(sudo -u www-data crontab -l; \
 cat install/templates/cron.template.ready; \
 echo;) | sudo -u www-data crontab -
```

**Cron Job Functions**:
- Daily interest calculation for bank accounts
- Periodic stat regeneration for monsters
- Session cleanup
- Log rotation

Sources: [README.md:288-300]()

## Troubleshooting

### Common Installation Issues

| Issue | Symptom | Solution |
|-------|---------|----------|
| **Permission Denied** | `AutoInstaller.pl` won't execute | Ensure running as root: `sudo ./AutoInstaller.pl` |
| **Missing Perl Modules** | `Can't locate Config/IniFiles.pm` | Run bootstrap script first |
| **PHP Version Not Found** | `php8.4` not available | Install Sury repository before installation |
| **MySQL Connection Failed** | Can't import schema | Verify MariaDB is running: `systemctl status mariadb` |
| **Apache Won't Start** | Port 80/443 already in use | Check for conflicting services: `netstat -tulpn | grep :80` |
| **Composer Fails** | Permission errors during `composer install` | Ensure executed as `www-data` user |
| **Template Not Found** | Missing `.ready` files | Run TEMPLATES step: `--step TEMPLATES` |
| **Web Root Mismatch** | Files not in correct location | Use mover script or manual relocation |

### Log File Locations

| Log File | Purpose |
|----------|---------|
| `system/logs/setup.log` | Installation script output |
| `system/logs/gamelog.txt` | Application runtime logs |
| `/var/log/apache2/$fqdn-error.log` | Apache errors |
| `/var/log/apache2/$fqdn-access.log` | HTTP requests |
| `/var/log/mysql/error.log` | Database errors |

### Resuming Interrupted Installation

The installer automatically resumes from the last completed step:

```bash
sudo ./AutoInstaller.pl --fqdn loa.example.com
```

To force restart from specific step:
```bash
sudo ./AutoInstaller.pl --fqdn loa.example.com --step PHP
```

To re-run only one step without continuing:
```bash
sudo ./AutoInstaller.pl --fqdn loa.example.com --step SQL --only
```

Sources: [install/AutoInstaller.pl:187-193](), [install/AutoInstaller.pl:315-337]()

### Verification Steps

After installation completes:

1. **Check Services**:
```bash
systemctl status apache2 mariadb
```

2. **Verify Database**:
```bash
mysql -u user_loa -p
> SHOW DATABASES;
> USE db_loa;
> SHOW TABLES;
```

3. **Test Web Access**:
```bash
curl -I http://loa.example.com
curl -I https://loa.example.com
```

4. **Check Logs**:
```bash
tail -f /var/log/apache2/loa.example.com-error.log
```

5. **Verify Permissions**:
```bash
ls -la /var/www/html/LegendOfAetheria/.env
# Should show: -rw------- 1 www-data www-data
```

Sources: [install/AutoInstaller.pl:605-657]()