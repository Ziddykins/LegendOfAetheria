![Legend of Aetheria logo](https://github.com/Ziddykins/LegendOfAetheria/blob/master/img/logos/logo-banner-no-bg.webp)

A browser-based RPG game written in PHP/JS using the Bootstrap 5 framework


# Getting Started

The recommended method to install and configure LoA is by using the auto-installer, however, the auto installer is pretty hefty and a work-in-progress. You may find it easier to go through the manual steps provided below if you find yourself running into issues with the autoinstaller. Please report any bugs found and they will be addressed.

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
> The autoinstaller has a bunch of variables which will > need your attention before it works.
> This will be made interactive eventually, but for now, > please go through and any section
> which has the # CONFIG flag, you should adjust to suit  your needs - These will be found at the top of the file and won't > be scattered throughout the code.

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

| Step             | Explanation/Manual Setup    |
| ---------------: | :-------------------------: |
| Software         | [Jump To](#Software)        |
| Templates        | [Jump To](#Templates)       |
| Hostname         | [Jump To](#Hostname)        |
| Apache           | [Jump To](#Apache)          |
| Certificates/SSL | [Jump To](#SSL)             |
| PHP Config       | [Jump To](#PHP)             | 
| Composer         | [Jump To](#Composer)        |
| System Services  | [Jump To](#Composer)        |
| Permissions      | [Jump To](#Permissions)     |
| CRON Jobs        | [Jump To](#CRONJobs)        |
| OpenAI           | [Jump To](#OpenAI)          |
| Clean-Up         | [Jump To](#CleanUp)         |
 
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

apt install -y php8.3 php8.3-cli php8.3-common php8.3-curl php8.3-dev php8.3-fpm php8.3-mbstring php8.3-mysql mariadb-server apache2 libapache2-mod-php8.3 composer letsencrypt python-is-python3 python3-certbot-apache
```


### Templates

LoA comes packaged with a bunch of template files, which get their values from the AutoInstaller script. Make sure the template values are all filled in, and suit your system and software. The entire SQL schema will be generated and imported. A random password is chosen for the SQL user, 16 characters long.

### Hostname

Setup your host to have a FQDN with `hostnamectl set-hostname <fqdn>` and 
`hostnamectl set-hostname <fqdn> --pretty`. Make sure to update your `/etc/hosts` file.

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

If you've SSL'd up the fqdn the game will be running at, add these lines to the
virtual host above, just before the </VirtualHost> tag, to force http -> https redirection

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
a2enmod php8.3 headers setenvif http2 ssl
a2enconf php8.3-fpm
```
### SSL

### PHP

### Composer

### Templates

### CRONJobs








## CRONJobs











## Development

Want to contribute? Check out [CONTRIBUTING.md](CONTRIBUTING.md)
