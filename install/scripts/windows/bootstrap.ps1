$mainScript =
Start-Process powershell.exe -ArgumentList "-ExecutionPolicy Bypass -File `"$scriptA`"" -Wait