@ECHO off

REM
REM Moodle component manager.
REM
REM @author Luke Carrier <luke@carrier.im>
REM @copyright 2015 Luke Carrier
REM @license GPL v3
REM

SETLOCAL EnableDelayedExpansion

php %~dp0\componentmgr -- %*
SET errno=%errorlevel%
EXIT /B !errno!
