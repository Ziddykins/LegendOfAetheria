$mainScript = "$PSScriptRoot\main.ps1"
$moverScript = "$env:LOCALAPPDATA\loatemp\mover.ps1"

Start-Process powershell.exe -ArgumentList "-ExecutionPolicy Bypass -File `"$mainScript`"" -Wait
Start-Sleep -Seconds 3
Start-Process powershell.exe -ArgumentList "-ExecutionPolicy Bypass -File `"$moverScript`"" -Wait
Start-Process powershell.exe -ArgumentList "-ExecutionPolicy Bypass -File `"$mainScript`"" -Wait
