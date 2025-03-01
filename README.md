![Legend of Aetheria logo](https://github.com/Ziddykins/LegendOfAetheria/blob/master/img/logos/logo-banner-no-bg.webp)

A browser-based RPG game written in PHP/JS using the [Bootstrap 5.3 framework](https://github.com/twbs), with a heavily modified version of [AdminLTE](https://github.com/ColorlibHQ/AdminLTE) for the administrative panel, which I've tailored to work with PHP and MySQL in a more modular fashion. See [Credits](#Credits) for more information on the technology used.

> [!CAUTION]
> The current state of the AutoInstaller is: **Not Working** -- Take care when cloning this until this banner is gone.

# Getting Started

The recommended method to install and configure LoA is by using the auto-installer, however, the auto installer is pretty hefty and a work-in-progress. You may find it easier to go through the manual steps provided below if you find yourself running into issues with the autoinstaller.

Please report any bugs found and they will be addressed.

## Download

Clone the project to your webroot (e.g. `/var/www/html`) set the permissions accordingly

```sh
cd /var/www/html
git clone https://github.com/Ziddykins/LegendOfAetheria
cd LegendOfAetheria
sudo chown -R www-data:www-data .
find . -type f -exec chmod 0644 {} \+
find . -type d -exec chmod 0755 {} \+
```

## AutoInstaller

The AutoInstaller script works best on a fresh install, but will work with existing setups with a bit of configuration. It will take care of just about every aspect of work which needs to be
created/imported/modified/configured - from fork/clone, right to SSL-enforced,
web-accessible browser game (provided your A/CNAME records are set up of course!)

> [!IMPORTANT]
> ~~The autoinstaller has a bunch of variables which will > need your attention before it works.~~
> ~~This will be made interactive eventually, but for now, > please go through and any section~~
> ~~which has the # CONFIG flag, you should adjust to suit  your needs - These will be found at the top of the file and won't > be scattered throughout the code.~~
> Interactive now :D

The script must be ran as root, so again, be aware of what is going on if you are installing this
on a machine with existing services (PHP configs, SQL configs, Apache, etc).

```sh
cd install
chmod +x AutoInstaller.pl
sudo ./AutoInstaller.pl
```

# Manual Configuration
> [!TIP]
> It's best to do these in order

| Step             | Explanation/Manual Setup           |
| ---------------: | :----------------------------------:   
| Software         | [Jump To Software](Software)       |
| Templates        | [Jump To Templates](Templates)     |
| Apache           | [Jump To Apache](Apache)           |
| Certificates/SSL | [Jump To SSL](SSL)                 |
| PHP Config       | [Jump To PHP](PHP)                 | 
| Composer         | [Jump To Composer](Composer)       |
| System Services  | [Jump To Composer](Composer)       |
| Permissions      | [Jump To Permissions](Permissions) |
| CRON Jobs        | [Jump To CRONJobs](CRONJobs)       |
| OpenAI           | [Jump To OpenAI](OpenAI)           |
| Clean-Up         | [Jump To CleanUp](CleanUp)         |
 
## Software

LoA requires a bunch of packages, which can be installed with the commands below. You'll also need the Sury repository (https://deb.sury.org/).

#### Sury
```bash
apt-get update

apt-get -y install lsb-release ca-certificates curl

curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg

sh -c 'echo "deb [trusted=yes signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'

apt-get update
```

```bash
apt update && apt upgrade -y

apt install -y php8.4 php8.4-cli php8.4-common php8.4-curl php8.4-dev php8.4-fpm php8.4-mysql mariadb-server apache2 libapache2-mod-php8.4 composer letsencrypt python-is-python3 python3-certbot-apache
```


### Templates

LoA comes packaged with a bunch of template files, which get their values from the AutoInstaller script. Make sure the template values are all filled in, and suit your system and software. The entire SQL schema will be generated and imported. A random password is chosen for the SQL user, 16 characters long. If you're setting this up manually, I've included a --flag for the utoInstaller which will only process templates, which will make your life a lot easier, especially for importing SQL schema.

```sh
sudo perl AutoInstaller.pl --templates
```
### Apache

Virtual Hosts, for non-SSL:

```apacheconf
    <VirtualHost <FQDN here>:80>
        ServerName <FQDN here>
        ServerAlias <FQDN here>
        ServerAdmin <Your Email>
        DocumentRoot <Docroot, e.g. /var/www/html/fqdn>

        LogLevel info ssl:warn
        ErrorLog ${APACHE_LOG_DIR}/<FQDN here>-error.log
        CustomLog ${APACHE_LOG_DIR}/<FQDN here>-access.log combined
    </VirtualHost>
```

If you have a valid SSL certificate, add these lines to the virtual host above, just before the </VirtualHost> tag, to force http -> https redirection.

```apacheconf
    RewriteEngine on
    RewriteCond %{SERVER_NAME} =<FQDN here>
    RewriteRule ^ https://%{SERVER_NAME}%{REQUEST_URI} [END,NE,R=permanent]
```

For the SSL-enabled virtual host, use:

```apacheconf
<IfModule mod_ssl.c>
    <VirtualHost <FQDN here>:443>
        ServerName <FQDN here>
        ServerAlias <FQDN here>
        ServerAdmin <Your Email>
        DocumentRoot /path/to/docroot

        # If you want http2, uncomment
        # Protocols h2 http/1.1

        LogLevel info ssl:warn
        ErrorLog ${APACHE_LOG_DIR}/<FQDN here>-error.log
        CustomLog ${APACHE_LOG_DIR}/<FQDN here>-access.log combined

        SSLCertificateFile /path/to/ssl/cert.pem
        SSLCertificateKeyFile /path/to/ssl/key.pem
    
        # If you used certbot or similar, you can uncomment this (provided the file exists)
        # Include /etc/letsencrypt/options-ssl-apache.conf
        
        Header always set Strict-Transport-Security "max-age=63072000"
    </VirtualHost>
</IfModule>
```

Here are working examples:

##### Non-SSL VirtualHost, saved as `/etc/apache2/sites-available/loa.example.com.conf`

```apacheconf
<VirtualHost loa.example.com:80>
    ServerName loa.example.com
    ServerAdmin ziddy@example.com
    DocumentRoot /var/www/html/example/loa

    LogLevel info ssl:warn
    ErrorLog ${APACHE_LOG_DIR}/loa.example.com-error.log
    CustomLog ${APACHE_LOG_DIR}/loa.example.com-access.log combined

    RewriteEngine on
    RewriteCond %{SERVER_NAME} =loa.example.com
    RewriteRule ^ https://%{SERVER_NAME}%{REQUEST_URI} [END,NE,R=permanent]
</VirtualHost>
```

##### SSL VirtualHost, saved as `/etc/apache2/sites-available/ssl-loa.example.com.conf`

```apacheconf
<IfModule mod_ssl.c>
    <VirtualHost loa.example.com:443>
        ServerName loa.example.com
        ServerAlias loa.example.com
        ServerAdmin ziddy@example.com
        DocumentRoot /var/www/html/example.com/loa
        Protocols h2 http/1.1

        LogLevel info ssl:warn
        ErrorLog ${APACHE_LOG_DIR}/loa.example.com-error.log
        CustomLog ${APACHE_LOG_DIR}/loa.example.com-access.log combined

        SSLCertificateFile /etc/letsencrypt/live/example.com/fullchain.pem
        SSLCertificateKeyFile /etc/letsencrypt/live/example.com/privkey.pem
        Include /etc/letsencrypt/options-ssl-apache.conf
        Header always set Strict-Transport-Security "max-age=63072000"
    </VirtualHost>
</IfModule>
```

Once those are setup:

```sh
a2ensite loa.example.com
a2ensite ssl-loa.example.com
a2enmod php8.4 headers setenvif http2 ssl
a2enconf php8.4-fpm
```

> [!IMPORTANT]
> The following lines should only be added to the http-version .conf file **AFTER** you've applied a valid SSL to the FQDN of your domain or subdomain, otherwise, you will not be able to get a certificate from certbot/letsencrypt.
>```conf
> RewriteEngine on
> RewriteCond %{SERVER_NAME} =loa.example.com
> RewriteRule ^ https://%{SERVER_NAME}%{REQUEST_URI} [END,NE,R=permanent]
> ```

Then go ahead and get your certificate:
```bash
certbot -d <fqdn>
```

The certificate should be activated and downloaded after that

### PHP

The main PHP section is mostly updating some of your php.ini variables
Locate the below variables and make the appropriate replacements:

```ini
expose_php = off
error_reporting = E_NONE
display_errors = Off
display_startup_errors = Off
allow_url_fopen = Off
allow_url_include = Off
session.gc_maxlifetime = 600
disable_functions = apache_child_terminate, apache_setenv, chdir, chmod, dbase_open, dbmopen, define_syslog_variables, escapeshellarg, escapeshellcmd, eval, exec, filepro, filepro_retrieve, filepro_rowcount, fopen_with_path, fp, fput, ftp_connect, ftp_exec, ftp_get, ftp_login, ftp_nb_fput, ftp_put, ftp_raw, ftp_rawlist, highlight_file, ini_alter, ini_get_all, ini_restore, inject_code, mkdir, move_uploaded_file, mysql_pconnect, openlog, passthru, phpAds_XmlRpc, phpAds_remoteInfo, phpAds_xmlrpcDecode, phpAds_xmlrpcEncode, php_uname, phpinfo, popen, posix_getpwuid, posix_kill, posix_mkfifo posix_mkfifo, posix_setpgid, posix_setsid, posix_setuid, posix_uname, proc_close, proc_get_status, proc_nice, proc_open, proc_terminate, putenv, rename, rmdir, shell_exec, show_source, syslog, system, xmlrpc_entity_decode
session.cookie_domain = <Your FQDN Here>
session.use_strict_mode = 1
session.use_cookies = 1
session.cookie_lifetime = 14400
session.cookie_secure = 1
session.cookie_httponly = 1
session.cookie_samesite = Strict
session.cache_expire = 30
```

> [!NOTE]
> Don't forget to change the session.cookie_domain to your own information

### Composer

Open a terminal and navigate to your webroot, then just issue `sudo -u www-data composer --working-dir <GAME_WEB_ROOT> install`

### Templates

### CRONJobs

### Credits
    [Bootstrap 5.3](https://github.com/twbs)
    [AdminLTE4](https://github.com/ColorlibHQ/AdminLTE)
    [Tabulator](https://tabulator.info)











## Development

Want to contribute? Check out [CONTRIBUTING.md](CONTRIBUTING.md)
