![Legend of Aetheria logo](https://github.com/Ziddykins/LegendOfAetheria/blob/master/img/logos/logo-banner-no-bg.png)

A browser-based RPG game written in PHP/JS using the Bootstrap 5 framework


# Getting Started

> [!CAUTION]
> The autoinstaller is a work-in-progress; don't run it until this banner is gone.
> Manual steps will be provided shortly.

> [!IMPORTANT]
> The recommended method for getting this up and running is using the [AutoInstaller](#AutoInstaller),
> however step-by-step Manual instructions will be covered later.

> [!NOTE]
> For best results, this should be run on a fresh install of Debian or Windows.

## Download

Clone the project to your webroot (e.g. `/var/www/html`) set the permissions accordingly

```sh
cd /var/www/html
git clone https://github.com/Ziddykins/LegendOfAetheria
cd LegendOfAetheria
sudo chown -R www-data:www-data .
```

## AutoInstaller

> [!WARNING]
> This script heavily modifies the target system.
> While this is the recommended method for setting
> Legend of Aetheria up, be sure to read and
> understand how each step affects your system and setup.

The AutoInstaller script was designed to be ran on a fresh install.
It will take care of just about every aspect of work which needs to be
created/imported/modified/configured; from fork/clone, right to SSL-enforced,
web-accessible browser game (provided your A/CNAME records are set up of course!)

> [!TODO]
> You can provide the flag `--interactive` if you want to be prompted at each step.

```sh
cd install
chmod +x AutoInstaller.pl
sudo perl AutoInstaller.pl
```


## Manual / Steps

| Step             | Explanation/Manual Setup    |
| ---------------- | --------------------------- |
| Software         | [Software](#Software)       |
| Hostname         | [Hostname](#Hostname)       |
| Apache           | [Apache](#Apache)           |
| Certificates/SSL | [SSL](#SSL)                 |
| PHP Config       | [PHP](#PHP)                 | 
| Composer         | [Composer](#Composer)       |
| Templates        | [Templates](#Templates)     |
| CRON Jobs        | [CRONJobs](#CRONJobs)       |
 
## Steps

### Software
    LoA requires the following packages, which can be installed with:
    ```sh
    apt update
    apt upgrade -y
    apt install -y php8.3 php8.3-cli php8.3-common php8.3-curl php8.3-dev php8.3-fpm php8.3-mbstring php8.3-mysql mariadb-server apache2 libapache2-mod-php8.3 composer
    ```
### Hostname
    Setup your host to have a FQDN with `hostnamectl set-hostname <fqdn>` and `hostnamectl set-hostname <fqdn> --pretty`
    Make sure to update your `/etc/hosts` file

### Apache
    Virtual Hosts, for non-SSL
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


## Need Help?

For manual setup, general troubleshooting and more
in-depth explanations, head on over to the [Wiki](https://github.com/Ziddykins/LegendOfAetheria/wiki/Home/)

