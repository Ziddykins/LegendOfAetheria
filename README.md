![Legend of Aetheria logo](https://github.com/Ziddykins/LegendOfAetheria/blob/master/img/logos/logo-banner-no-bg.png)

A browser-based RPG game written in PHP/JS using the Bootstrap 5 framework


# Getting Started

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

You can provide the flag `--interactive` if you want to be prompted at each step.

```sh
cd install
chmod +x AutoInstaller.pl
sudo perl AutoInstaller.pl
```


## Manual / Steps

| Step             | Explanation/Manual Help     |
| ---------------- | --------------------------- |
| Hosts            | [Hosts](#Hosts)             |
| Software         | [Software](#Software)       |
| Hostname         | [Hostname](#Hostname)       |
| Apache           | [Apache](#Apache)           |
| Certificates/SSL | [SSL](#SSL)                 |
| PHP Config       | [PHP](#PHP)                 | 
| Composer         | [Composer](#Composer)       |
| .ENV Template    | [EnvTemplate](#EnvTemplate) |
| SQL Generation   | [SQLGen](#SQLGen)           |
| SQL Importing    | [SQLImport](#SQLImport)     |
| CRON Jobs        | [CRONJobs](#CRONJobs)       |
 







## EnvTemplate







## CRONJobs











## Development

Want to contribute? Check out [CONTRIBUTING.md](CONTRIBUTING.md)


## Need Help?

For manual setup, general troubleshooting and more
in-depth explanations, head on over to the [Wiki](https://github.com/Ziddykins/LegendOfAetheria/wiki/Home/)

