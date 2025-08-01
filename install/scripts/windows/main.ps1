function file_exists {
    param (
        [string]$filePath
    )
    return Test-Path -Path $filePath
}

function do_mover_script {
    Set-Content -Path $env:TEMP\mover.ps1 -Value @"
    Move-Item -Path 'C:\xampp\' -Destination '$webParent' -Force
    Move-Item -Path '$webRoot' -Destination '$webParent\htdocs' -Force
    Set-Content -Path Env:\Temp\mover.success -Value 'true'
    Start-Process powershell.exe -ArgumentList "-ExecutionPolicy Bypass -File `$MyInvocation.MyCommand.Path`" -Wait
"@
}


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
$webRoot = (((Get-Item (Get-Location)).Parent).Parent).Parent.FullName
$webParent = ((((Get-Item (Get-Location)).Parent).Parent).Parent).Parent.FullName
New-Item -ItemType Directory -Force -Path $downloadFolder

if ($choice -eq "1") {
    $xamppInstaller = "$downloadFolder\xampp.exe"

    if (-Not (file_exists $xamppInstaller)) {
        Write-Host "Downloading XAMPP installer to $downloadFolder"
        Invoke-WebRequest -Uri "https://1007.filemail.com/api/file/get?filekey=8LSq8Jrg8I3ML8_rugX0xnkwdGf2LvB5vg-aVEx0KhhPoOa1OljjBJC6zmfhA1_VbJeLG6b_cmxSAJ8hVGu9L_0ejf8KkIqRWreFurHbyA&pk_vid=e84a89bdc9232818175359830893e286" -Outfile $xamppInstaller
    } else {
        Write-Host "XAMPP installer already exists, skipping download."
    }

    if (Get-Item -Path "HKLM:Software\Microsoft\Windows\CurrentVersion\Uninstall\xampp" -ErrorAction SilentlyContinue) {
         Write-Host "XAMPP Installed already"
    } else {
        Start-Process -FilePath $xamppInstaller -ArgumentList "--unattendedmodeui none --mode unattended --enable-components xampp_mysql,xampp_perl --prefix $webParent" -Wait
        Write-Host "Downloads and installations complete."
    }
} else {
    $files | ForEach-Object {
        if (-Not (file_exists $_)) {
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

if (-Not (file_exists $env:TEMP\mover.success)) {
    Write-Host "Creating mover script and executing"
    do_mover_script
} else {
    Write-Host "Mover script has completed and now we will continue the bootstrap process"
    continue_script
}