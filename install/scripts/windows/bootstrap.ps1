$mainScript = "$PSScriptRoot\main.ps1"
$moverScript = "$env:TEMP\mover.ps1"

Start-Process powershell.exe -ArgumentList "-ExecutionPolicy Bypass -File `"$mainScript`"" -Wait
Start-Process powershell.exe -ArgumentList "-ExecutionPolicy Bypass -File `"$moverScript`"" -Wait
Start-Process powershell.exe -ArgumentList "-ExecutionPolicy Bypass -File `"$mainScript`"" -Wait