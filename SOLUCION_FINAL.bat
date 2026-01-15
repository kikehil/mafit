@echo off
echo ========================================
echo Instalacion Completa de MAFIT
echo ========================================
echo.

cd /d "%~dp0"

REM Verificar PHP
if exist "C:\xampp\php85\php.exe" (
    set PHP=C:\xampp\php85\php.exe
    set PHPINI=C:\xampp\php85\php.ini
) else if exist "C:\xampp\php\php.exe" (
    set PHP=C:\xampp\php\php.exe
    set PHPINI=C:\xampp\php\php.ini
) else (
    echo ERROR: PHP no encontrado
    pause
    exit
)

echo PHP encontrado: %PHP%
echo.

REM Verificar extensiones
echo Verificando extensiones PHP...
%PHP% -r "if (!extension_loaded('fileinfo')) { echo 'ERROR: extension fileinfo NO esta habilitada\n'; echo 'Abre: %PHPINI%\n'; echo 'Busca: ;extension=fileinfo\n'; echo 'Cambia a: extension=fileinfo\n'; echo 'Luego reinicia Apache\n'; exit(1); }"
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ========================================
    echo ACCION REQUERIDA: Habilitar extension fileinfo
    echo ========================================
    echo.
    echo 1. Abre: %PHPINI%
    echo 2. Busca: ;extension=fileinfo
    echo 3. Quita el punto y coma: extension=fileinfo
    echo 4. Guarda y cierra
    echo 5. Reinicia Apache en XAMPP Control Panel
    echo 6. Ejecuta este script de nuevo
    echo.
    pause
    exit
)

echo Extension fileinfo: OK
echo.

REM Verificar Composer
if exist "C:\xampp\php85\composer.phar" (
    set COMPOSER=C:\xampp\php85\composer.phar
) else if exist "C:\xampp\php\composer.phar" (
    set COMPOSER=C:\xampp\php\composer.phar
) else (
    echo ERROR: composer.phar no encontrado
    echo Ejecuta primero: descargar_composer.bat
    pause
    exit
)

echo Composer encontrado: %COMPOSER%
echo.

REM Instalar dependencias
echo ========================================
echo Paso 1: Instalando dependencias PHP...
echo ========================================
echo Esto puede tardar varios minutos...
echo.

%PHP% %COMPOSER% install

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR al instalar dependencias
    pause
    exit
)

echo.
echo ========================================
echo Paso 2: Generando clave de aplicacion...
echo ========================================
echo.

%PHP% artisan key:generate

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR al generar clave
    pause
    exit
)

echo.
echo ========================================
echo Paso 3: Verificando base de datos...
echo ========================================
echo.
echo IMPORTANTE: Asegurate de que:
echo   1. MySQL este corriendo en XAMPP
echo   2. La base de datos 'mafit' exista
echo   3. El archivo .env tenga la configuracion correcta
echo.
pause

echo.
echo ========================================
echo Paso 4: Ejecutando migraciones...
echo ========================================
echo.

%PHP% artisan migrate --seed

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR al ejecutar migraciones
    echo Verifica la configuracion de la base de datos en .env
    pause
    exit
)

echo.
echo ========================================
echo Instalacion PHP completada!
echo ========================================
echo.
echo Siguiente paso manual:
echo   1. Instalar Node.js si no lo tienes: https://nodejs.org/
echo   2. Ejecutar: npm install
echo   3. Ejecutar: npm run build
echo.
echo Luego accede a: http://localhost/MAFIT/public
echo.
pause






