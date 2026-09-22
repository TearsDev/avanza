@echo off
tasklist /FI "IMAGENAME eq httpd.exe" | find /I "httpd.exe" >nul
if not errorlevel 1 (
  echo Apache ya esta corriendo en http://127.0.0.1:8080/
  pause
  exit /b 0
)
start "Apache" "C:\Users\fran_\AppData\Local\Apache24\bin\httpd.exe" -d "C:\Users\fran_\AppData\Local\Apache24"
echo Apache en http://127.0.0.1:8080/
