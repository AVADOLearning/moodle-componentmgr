@ECHO off

REM
REM Moodle component manager.
REM
REM @author Luke Carrier <luke@carrier.im>
REM @copyright 2015 Luke Carrier
REM @license GPL v3
REM

SETLOCAL EnableDelayedExpansion

SET binDir=%~dp0
CALL :resolvePath !binDir!\.. rootDir

php -d error_reporting=-1 -d display_errors=On !rootDir!\libexec\componentmgr.php -- %*
SET errno=%errorlevel%
EXIT /B !errno!

:resolvePath
SET %2=%~f1
GOTO :eof
