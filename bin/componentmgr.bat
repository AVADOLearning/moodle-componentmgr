@ECHO off

REM
REM Moodle component manager.
REM
REM @author Luke Carrier <luke@carrier.im>
REM @copyright 2016 Luke Carrier
REM @license GPL-3.0+
REM

SETLOCAL EnableDelayedExpansion
SETLOCAL EnableExtensions

IF NOT DEFINED PHP (
    SET PHP=php
)

SET binDir=%~dp0
CALL :resolvePath !binDir!\.. rootDir

%PHP% -d variables_order=EGPCS ^
    -d error_reporting=-1 ^
    -d display_errors=On ^
    !rootDir!\libexec\componentmgr-local %*
SET errno=%errorlevel%
EXIT /B !errno!

:resolvePath
SET %2=%~f1
GOTO :eof
