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
SET caBundle=!rootDir!\vendor\kdyby\curl-ca-bundle\src\ca-bundle.crt

php -d variables_order=EGPCS ^
    -d error_reporting=-1 ^
    -d display_errors=On ^
    -d curl.cainfo=!caBundle! ^
    !rootDir!\libexec\componentmgr.php -- %*
SET errno=%errorlevel%
EXIT /B !errno!

:resolvePath
SET %2=%~f1
GOTO :eof
