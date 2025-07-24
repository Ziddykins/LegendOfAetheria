$checkWinget = Get-Command -Name "winget" -ErrorAction SilentlyContinue

$choices = @'
Choose method of setup
1) XAMPP
    - Comes with Apache, PHP, MySQL and perl all in one package - quick and easy, great for development
    - Possible version lag/lack of hardening
2) Individual Components
    - Each package is installed separately with their own installers
    - Geared more towards production servers
'@

$choice = Read-Host -Prompt $choices

# Define download URLs — adjust as needed once official PHP 8.4 is released
$files = @(
    "https://windows.php.net/downloads/releases/php-8.4.10-Win32-vs17-x64.zip",
    "https://getcomposer.org/installer",
    "https://github.com/StrawberryPerl/Perl-Dist-Strawberry/releases/download/SP_54021_64bit_UCRT/strawberry-perl-5.40.2.1-64bit.msi"
);

# Define save paths
$downloadFolder = Get-Location | Select-Object -ExpandProperty Path | Join-Path -ChildPath "\temp"
New-Item -ItemType Directory -Force -Path $downloadFolder

if ($choice -eq "1") {
    $xamppInstaller = "$downloadFolder\xampp.exe"
    Invoke-WebRequest -Uri "https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.12/xampp-windows-x64-8.2.12-0-VS16-installer.exe" -Outfile $xamppInstaller
    Start-Process -FilePath $xamppInstaller -ArgumentList "--mode unattended --unattendedmodeui minimal --components apache,mysql,perl,php" -Wait
    Write-Host "Downloads and installations complete."
} else {
    $files | ForEach-Object {
        if (-Not (Test-Path -Path $_)) {
            Write-Host "Downloading $_ to $downloadFolder"
            Invoke-WebRequest -Uri $_ -OutFile (Join-Path -Path $downloadFolder -ChildPath (Split-Path -Path $_ -Leaf))
        } else {
            Write-Host "$_ already exists, skipping download."
            return
        }
    }

    Write-Host "Extracting PHP to $downloadFolder"
    Expand-Archive -LiteralPath $phpZip -DestinationPath "$downloadFolder\php" -Force
    Write-Host "Done!"

    Write-Host "Installing composer to $downloadFolder\php"
    php "$composerInstaller" --install-dir="$downloadFolder\php" --filename=composer.exe
    Write-Host "Done!"

    Write-Host "Silently installing perl"
    Start-Process msiexec.exe -ArgumentList "/i `"$perlInstaller`" /quiet" -Wait
    Write-Host "Done!"
}