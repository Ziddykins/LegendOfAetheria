$BINDIR="$env:SystemDrive$env:HOMEPATH\Downloads";
$links = @('https://github.com/git-for-windows/git/releases/download/v2.54.0.windows.1/PortableGit-2.54.0-64-bit.7z.exe', 'https://code.visualstudio.com/sha/download?build=stable&os=win32-x64-user', 'https://nodejs.org/dist/v25.9.0/node-v25.9.0-win-x64.zip', 'https://downloads.php.net/~windows/releases/archives/php-8.5.5-Win32-vs17-x64.zip', 'https://getcomposer.org/installer');
cd $BINDIR;
$links | ForEach-Object { $fn = Split-Path -Path $_ -Leaf; curl -L $_ --output $fn; }


.\$BINDIR\VSCodeUserSetup-x64-1.117.0.exe /DIR:$BINDIR /SP- /VERYSILENT MSIINSTALLPERUSER=1 /MERGETASKS=!runcode
.\$BINDIR\PortableGit-2.54.0-64-bit.7z.exe -y -gm2 -o"$BINDIR"
Expand-Archive -Path $BINDIR\node-v25.9.0-win-x64.zip -Destination $BINDIR\node
Expand-Archive -Path $BINDIR\php-8.5.5-Win32-vs17-x64.zip -Destination $BINDIR\php

Move-Item -Path node-v25.9.0-win-x64 -Destination node
Move-Item -Path PortableGit -Destination git
Move-Item -Path php-8.5.5 -Destination php

$env:Path="$env:Path;$BINDIR\PortableGit\bin;$BINDIR\node;$BINDIR\php"

Copy-Item -Path $BINDIR\php\php.ini-development -Destination $BINDIR\php\php.ini
php installer --filename=composer

git clone https://github.com/ziddykins/legendofaetheria