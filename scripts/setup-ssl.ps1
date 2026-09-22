<#
setup-ssl.ps1
Genera un certificado autofirmado con SAN para localhost, lo instala en
`C:\xampp\apache\conf\ssl.crt` y `...\conf\ssl.key`, lo importa al
almacén de certificados raíz del equipo y (opcional) intenta reiniciar Apache.

EJECUCIÓN: Abrir PowerShell como Administrador y ejecutar:
  .\scripts\setup-ssl.ps1

#>

$ErrorActionPreference = 'Stop'

function Log {
    param([string]$msg)
    $time = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
    $line = "[$time] $msg"
    Write-Output $line
    Add-Content -Path $global:LogFile -Value $line
}

$apacheRoot = 'C:\xampp\apache'
$sslCrtDir = Join-Path $apacheRoot 'conf\ssl.crt'
$sslKeyDir = Join-Path $apacheRoot 'conf\ssl.key'
$sslCrt = Join-Path $sslCrtDir 'server.crt'
$sslKey = Join-Path $sslKeyDir 'server.key'
$openssl = Join-Path $apacheRoot 'bin\openssl.exe'
$sanCnf = Join-Path $apacheRoot 'conf\san.cnf'
$global:LogFile = Join-Path $apacheRoot 'setup-ssl.log'

Remove-Item -Path $global:LogFile -ErrorAction SilentlyContinue -Force
Log "Inicio del script setup-ssl.ps1"

if (-not (Test-Path $apacheRoot)) {
    Log "No se encontró la ruta $apacheRoot. Abortando."
    exit 1
}

Log "Creando carpetas de destino"
New-Item -ItemType Directory -Force -Path $sslCrtDir | Out-Null
New-Item -ItemType Directory -Force -Path $sslKeyDir | Out-Null

if (-not (Test-Path $openssl)) {
    Log "No se encontró OpenSSL en $openssl. Asegurate de tener XAMPP correctamente instalado.";
    exit 1
}

Log "Generando archivo de configuración SAN en $sanCnf"
$sanLines = @(
    '[ req ]'
    'default_bits       = 2048'
    'prompt             = no'
    'default_md         = sha256'
    'distinguished_name = dn'
    'req_extensions     = v3_req'
    ''
    '[ dn ]'
    'CN = localhost'
    ''
    '[ v3_req ]'
    'subjectAltName = @alt_names'
    ''
    '[ alt_names ]'
    'DNS.1 = localhost'
    'IP.1 = 127.0.0.1'
)
$sanLines | Set-Content -Path $sanCnf -Encoding ASCII

try {
    Log "Generando certificado y clave (válido 365 días)..."
    & $openssl req -x509 -nodes -days 365 -newkey rsa:2048 `
        -keyout $sslKey `
        -out $sslCrt `
        -config $sanCnf `
        -extensions v3_req
    Log "Certificado creado: $sslCrt"
    Log "Clave creada: $sslKey"
} catch {
    Log "Error generando certificado: $_"
    exit 1
}

try {
    Log "Importando certificado al almacén de entidades raíz de confianza (LocalMachine)"
    Import-Certificate -FilePath $sslCrt -CertStoreLocation Cert:\LocalMachine\Root | Out-Null
    Log "Importación completada (si el script se ejecutó como Administrador)."
} catch {
    Log "No se pudo importar el certificado automáticamente: $_"
    Log "Si no ejecutaste PowerShell como Administrador, volvé a ejecutar el script con permisos elevados."
}

Log "Deteniendo posibles procesos httpd en ejecución"
Get-Process httpd -ErrorAction SilentlyContinue | ForEach-Object {
    try { Stop-Process -Id $_.Id -Force -ErrorAction Stop; Log "Detenido httpd PID=$($_.Id)" } catch { Log "No se pudo detener PID $($_.Id): $_" }
}

try {
    Log "Intentando iniciar Apache (background)"
    & (Join-Path $apacheRoot 'bin\httpd.exe') -k start -f (Join-Path $apacheRoot 'conf\httpd.conf')
    Start-Sleep -Seconds 2
    Log "Comando de inicio enviado."
} catch {
    Log "Error arrancando Apache: $_"
}

Log "Comprobando qué escucha en el puerto 443"
netstat -ano | findstr :443 | ForEach-Object { Log $_ }

Log "Solicitando certificado desde localhost:443 (openssl s_client)"
try {
    & $openssl s_client -connect localhost:443 -servername localhost -showcerts 2>$null | Out-String | ForEach-Object { Log $_ }
} catch {
    Log "Error consultando el servidor SSL: $_"
}

Log "Fin del script. Revisa $global:LogFile para más detalles."
