@Echo off
:: Check WMIC is available
WMIC.EXE Alias /? >NUL 2>&1 || GOTO s_error

:: Use WMIC to retrieve date and time
FOR /F "skip=1 tokens=1-6" %%G IN ('WMIC Path Win32_LocalTime Get Day^,Hour^,Minute^,Month^,Second^,Year /Format:table') DO (
   IF "%%~L"=="" goto s_done
      Set _yyyy=%%L
      Set _mm=00%%J
      Set _dd=00%%G
      Set _hour=00%%H
      SET _minute=00%%I
)
:s_done

:: Pad digits with leading zeros
      Set _mm=%_mm:~-2%
      Set _dd=%_dd:~-2%
      Set _hour=%_hour:~-2%
      Set _minute=%_minute:~-2%

:: Display the date/time in ISO 8601 format:
Set _isodate=%_yyyy%%_mm%%_dd%

::LocalDB Setting
Set _LocalDBHost=localhost
Set _LocalDBUser=root
Set _LocalDBPass= 
Set _LocalPORT=3306

::SQL Service path
Set _mysqlPath=mysql
Set _mysqldumpPath=mysqldump

:: Client DB Selections
echo. 
echo ***********************************************
echo 999. localhost
echo ***********************************************
echo.

SET /P AREYOUSURE=Please select a client by number - 
IF /I "%AREYOUSURE%"=="999" GOTO LOCALHOST

echo "Invalid client info"
GOTO END_FILE

:LOCALHOST
SET _DBhost=localhost
SET _DBuser=root
SET _DBpass=
SET _DBname=cleanDb
SET _PORT=3306
GOTO PROMPT

:PROMPT
Set _backupName=%_isodate%_%_DBname%_backup.sql
echo You are dumping :-
echo DBName : %_DBname%
echo DBHost : %_DBhost%
echo DBUser : %_DBuser%
echo PORT : %_PORT%
SET /P AREYOUSURE=Are you sure (Y/N)?
SET /P IMPORTLOCAL=Do you want to import into your local db (Y/N)?
IF /I "%AREYOUSURE%" NEQ "Y" GOTO END_FILE

:DUMP
echo Dumping Data from %_DBname%...
%_mysqldumpPath% --port=%_PORT% -h %_DBhost% -u %_DBuser% -p%_DBpass% %_DBname% > %_backupName%

IF /I "%IMPORTLOCAL%" NEQ "Y" GOTO END_FILE
Set _LocalDbName=%_isodate%_%_DBname%
echo Creating local database (%_LocalDbName%)...
%_mysqlPath% --port=%_LocalPORT% -h %_LocalDBhost% -u %_LocalDBuser% -p%_LocalDBpass% -e"CREATE DATABASE IF NOT EXISTS %_LocalDbName%"
echo Importing local database (%_LocalDbName%)...
%_mysqlPath% --port=%_LocalPORT% -h %_LocalDBhost% -u %_LocalDBuser% -p%_LocalDBpass% %_LocalDbName% < %_backupName%


:END_FILE
echo Bye.
PAUSE;