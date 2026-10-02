@echo off
REM Backup database LedgerNusa
set TIMESTAMP=%date:~-4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%
set TIMESTAMP=%TIMESTAMP: =0%
set BACKUP_DIR=D:\backup\ledgernusa
set DB_NAME=ledgernusa
set DB_USER=root
set DB_PASS=
set MYSQL_BIN=D:\laragon\bin\mysql\mysql-8.0.30-winx64\bin

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

"%MYSQL_BIN%\mysqldump.exe" -u %DB_USER% %DB_NAME% > "%BACKUP_DIR%\ledgernusa_%TIMESTAMP%.sql"

REM Hapus backup lebih dari 30 hari
forfiles /P "%BACKUP_DIR%" /M *.sql /D -30 /C "cmd /c del @path" 2>nul

echo Backup selesai: ledgernusa_%TIMESTAMP%.sql
pause