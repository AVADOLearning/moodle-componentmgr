param(
    [string] $BaseName,
    [string] $Version,
    [string] $XdebugUri
)

$baseDir = "C:\tools\php$($BaseName)"
$phpIni = Join-Path $baseDir 'php.ini'

$xdebugBaseDir = "$($baseDir)-xdebug"
$xdebugExtBaseName = ([System.Uri] $XdebugUri).Segments[-1]
$xdebugExtPath = Join-Path $xdebugBaseDir $xdebugExtBaseName

# Enable the Windows Update service during PHP installation as we may need to
# install Windows updates.
Set-Service -Name wuauserv -StartupType Automatic
Start-Service -Name wuauserv
& choco install -y php --version $Version
Set-Service -Name wuauserv -StartupType Disabled
Stop-Service -Name wuauserv
Copy-Item "$($phpIni)-production" $phpIni
(Get-Content -Path $phpIni | Out-String) `
        -replace '; extension_dir = "ext"',     'extension_dir = "ext"'      `
        -replace ';extension=php_curl.dll',     'extension=php_curl.dll'     `
        -replace ';extension=php_mbstring.dll', 'extension=php_mbstring.dll' `
        -replace ';extension=php_openssl.dll',  'extension=php_openssl.dll'  `
        | Set-Content -Path $phpIni

New-Item -Path $xdebugBaseDir -ItemType Directory
$client = New-Object System.Net.WebClient
$client.DownloadFile($XdebugUri, $xdebugExtPath)
Add-Content -Path $phpIni -Value "zend_extension=$($xdebugExtPath)"
