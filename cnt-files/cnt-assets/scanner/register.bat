set location=%~dp0
echo %location%
reg ADD "HKEY_CLASSES_ROOT\scanner" /ve /d "Scanner"
reg ADD "HKEY_CLASSES_ROOT\scanner" /v "Url Protocol" /t REG_SZ /d ""
reg ADD "HKEY_CLASSES_ROOT\scanner\shell\open\command" /ve /d "\"%location%scanner.bat\"