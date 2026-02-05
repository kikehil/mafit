<?php
/**
 * Script de diagnóstico simplificado para problemas de login en VPS
 * No requiere cargar Laravel, funciona con cualquier versión de PHP
 * Ejecutar desde la raíz del proyecto: php diagnosticar-login-vps-simple.php
 */

echo "=== DIAGNÓSTICO DE LOGIN EN VPS (Versión Simplificada) ===\n\n";

// 1. Verificar versión de PHP
echo "1. VERSIÓN DE PHP:\n";
$phpVersion = phpversion();
echo "   - Versión actual: $phpVersion\n";
$requiredVersion = '8.3.0';
if (version_compare($phpVersion, $requiredVersion, '<')) {
    echo "   ⚠️  ERROR CRÍTICO: Se requiere PHP >= $requiredVersion\n";
    echo "      La aplicación NO funcionará correctamente con esta versión.\n";
    echo "      Solución: Actualiza PHP a la versión 8.3 o superior.\n";
} else {
    echo "   ✓ Versión de PHP compatible\n";
}
echo "\n";

// 2. Verificar archivos de configuración
echo "2. ARCHIVOS DE CONFIGURACIÓN:\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "   - Archivo .env existe: SÍ\n";
    $envContent = file_get_contents($envFile);
    
    // Verificar configuraciones importantes
    $checks = [
        'APP_URL' => 'APP_URL',
        'SESSION_DRIVER' => 'SESSION_DRIVER',
        'SESSION_SECURE_COOKIE' => 'SESSION_SECURE_COOKIE',
        'DB_CONNECTION' => 'DB_CONNECTION',
        'DB_DATABASE' => 'DB_DATABASE',
    ];
    
    foreach ($checks as $key => $label) {
        if (preg_match("/^{$key}=(.*)$/m", $envContent, $matches)) {
            $value = trim($matches[1]);
            echo "   - $label: $value\n";
        } else {
            echo "   - $label: NO DEFINIDO\n";
        }
    }
} else {
    echo "   ⚠️  ERROR: El archivo .env no existe\n";
    echo "      Copia .env.example a .env y configúralo\n";
}
echo "\n";

// 3. Verificar configuración de sesiones en config/session.php
echo "3. CONFIGURACIÓN DE SESIONES:\n";
$sessionConfigFile = __DIR__ . '/config/session.php';
if (file_exists($sessionConfigFile)) {
    echo "   - Archivo config/session.php existe: SÍ\n";
    $sessionConfig = file_get_contents($sessionConfigFile);
    
    // Verificar driver
    if (preg_match("/'driver'\s*=>\s*env\('SESSION_DRIVER',\s*'([^']+)'\)/", $sessionConfig, $matches)) {
        echo "   - Driver por defecto: " . $matches[1] . "\n";
    }
    
    // Verificar si tiene detección automática de HTTPS
    if (strpos($sessionConfig, '$isSecure') !== false || strpos($sessionConfig, 'isSecure') !== false) {
        echo "   - Detección automática de HTTPS: SÍ (configuración actualizada)\n";
    } else {
        echo "   - Detección automática de HTTPS: NO (puede causar problemas)\n";
    }
} else {
    echo "   ⚠️  ERROR: El archivo config/session.php no existe\n";
}
echo "\n";

// 4. Verificar permisos de storage
echo "4. PERMISOS DE STORAGE:\n";
$storagePath = __DIR__ . '/storage';
$sessionsPath = __DIR__ . '/storage/framework/sessions';

if (is_dir($storagePath)) {
    echo "   - Directorio storage existe: SÍ\n";
    $writable = is_writable($storagePath);
    echo "   - Permisos de escritura en storage: " . ($writable ? 'OK' : 'NO') . "\n";
    
    if (is_dir($sessionsPath)) {
        echo "   - Directorio sessions existe: SÍ\n";
        $sessionsWritable = is_writable($sessionsPath);
        echo "   - Permisos de escritura en sessions: " . ($sessionsWritable ? 'OK' : 'NO') . "\n";
        if (!$sessionsWritable) {
            echo "   ⚠️  ERROR: Ejecuta: chmod -R 775 storage/framework/sessions\n";
        }
    } else {
        echo "   ⚠️  ERROR: El directorio sessions no existe\n";
        echo "      Ejecuta: mkdir -p storage/framework/sessions && chmod -R 775 storage/framework/sessions\n";
    }
} else {
    echo "   ⚠️  ERROR: El directorio storage no existe\n";
}
echo "\n";

// 5. Verificar conexión a base de datos (sin Laravel)
echo "5. CONEXIÓN A BASE DE DATOS:\n";
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    // Extraer configuración de BD
    $dbConfig = [];
    $dbKeys = ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
    foreach ($dbKeys as $key) {
        if (preg_match("/^{$key}=(.*)$/m", $envContent, $matches)) {
            $dbConfig[$key] = trim($matches[1]);
        }
    }
    
    if (isset($dbConfig['DB_CONNECTION']) && isset($dbConfig['DB_HOST'])) {
        echo "   - Tipo: " . ($dbConfig['DB_CONNECTION'] ?? 'no definido') . "\n";
        echo "   - Host: " . ($dbConfig['DB_HOST'] ?? 'no definido') . "\n";
        echo "   - Base de datos: " . ($dbConfig['DB_DATABASE'] ?? 'no definido') . "\n";
        echo "   - Usuario: " . ($dbConfig['DB_USERNAME'] ?? 'no definido') . "\n";
        
        // Intentar conexión si es MySQL/MariaDB
        if (($dbConfig['DB_CONNECTION'] ?? '') === 'mysql') {
            $host = $dbConfig['DB_HOST'] ?? 'localhost';
            $port = $dbConfig['DB_PORT'] ?? '3306';
            $database = $dbConfig['DB_DATABASE'] ?? '';
            $username = $dbConfig['DB_USERNAME'] ?? '';
            $password = $dbConfig['DB_PASSWORD'] ?? '';
            
            if (function_exists('mysqli_connect')) {
                $conn = @mysqli_connect($host, $username, $password, $database, $port);
                if ($conn) {
                    echo "   - Conexión: OK\n";
                    
                    // Verificar tabla de sesiones
                    $result = mysqli_query($conn, "SHOW TABLES LIKE 'sessions'");
                    if ($result && mysqli_num_rows($result) > 0) {
                        echo "   - Tabla 'sessions' existe: SÍ\n";
                        
                        // Contar sesiones
                        $countResult = mysqli_query($conn, "SELECT COUNT(*) as count FROM sessions");
                        if ($countResult) {
                            $row = mysqli_fetch_assoc($countResult);
                            echo "   - Sesiones activas: " . $row['count'] . "\n";
                        }
                    } else {
                        echo "   ⚠️  ERROR: La tabla 'sessions' NO existe\n";
                        echo "      Ejecuta: php artisan migrate\n";
                    }
                    
                    // Verificar tabla de usuarios
                    $result = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
                    if ($result && mysqli_num_rows($result) > 0) {
                        echo "   - Tabla 'users' existe: SÍ\n";
                        
                        // Contar usuarios
                        $countResult = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
                        if ($countResult) {
                            $row = mysqli_fetch_assoc($countResult);
                            echo "   - Total de usuarios: " . $row['count'] . "\n";
                        }
                    } else {
                        echo "   ⚠️  ERROR: La tabla 'users' NO existe\n";
                        echo "      Ejecuta: php artisan migrate\n";
                    }
                    
                    mysqli_close($conn);
                } else {
                    echo "   ⚠️  ERROR: No se pudo conectar a la base de datos\n";
                    echo "      Verifica las credenciales en .env\n";
                }
            } else {
                echo "   - Extensión mysqli no disponible\n";
            }
        }
    } else {
        echo "   ⚠️  ERROR: Configuración de BD incompleta en .env\n";
    }
} else {
    echo "   ⚠️  ERROR: No se puede verificar sin archivo .env\n";
}
echo "\n";

// 6. Verificar detección de HTTPS
echo "6. DETECCIÓN DE HTTPS:\n";
$isSecure = false;
$detectionMethods = [];

if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1')) {
    $isSecure = true;
    $detectionMethods[] = '$_SERVER[\'HTTPS\']';
}
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $isSecure = true;
    $detectionMethods[] = 'X-Forwarded-Proto header';
}
if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
    $isSecure = true;
    $detectionMethods[] = 'SERVER_PORT = 443';
}

echo "   - Detectado como HTTPS: " . ($isSecure ? 'SÍ' : 'NO') . "\n";
if (!empty($detectionMethods)) {
    echo "   - Métodos de detección: " . implode(', ', $detectionMethods) . "\n";
}
echo "   - SERVER_PORT: " . ($_SERVER['SERVER_PORT'] ?? 'no definido') . "\n";
echo "   - HTTPS: " . ($_SERVER['HTTPS'] ?? 'no definido') . "\n";
echo "   - X-Forwarded-Proto: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'no definido') . "\n";
echo "\n";

// 7. Verificar extensiones PHP necesarias
echo "7. EXTENSIONES PHP:\n";
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'tokenizer', 'xml', 'ctype', 'fileinfo'];
foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "   - $ext: " . ($loaded ? 'OK' : 'NO') . "\n";
    if (!$loaded) {
        echo "     ⚠️  Instala: sudo apt-get install php-$ext\n";
    }
}
echo "\n";

// 8. Recomendaciones
echo "=== RECOMENDACIONES ===\n";

if (version_compare($phpVersion, $requiredVersion, '<')) {
    echo "⚠️  PRIORIDAD ALTA: Actualiza PHP a la versión 8.3 o superior\n";
    echo "   Comandos para Ubuntu/Debian:\n";
    echo "   sudo add-apt-repository ppa:ondrej/php\n";
    echo "   sudo apt update\n";
    echo "   sudo apt install php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip\n";
    echo "   sudo update-alternatives --set php /usr/bin/php8.3\n";
    echo "   sudo systemctl restart apache2  # o nginx\n";
}

if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    if (strpos($envContent, 'SESSION_SECURE_COOKIE') === false) {
        echo "✓ SESSION_SECURE_COOKIE no está definido - se detectará automáticamente (correcto)\n";
    } elseif (preg_match("/^SESSION_SECURE_COOKIE=(.*)$/m", $envContent, $matches)) {
        $value = trim($matches[1]);
        if ($value === 'true' && !$isSecure) {
            echo "⚠️  ADVERTENCIA: SESSION_SECURE_COOKIE=true pero no se detecta HTTPS\n";
            echo "   Esto impedirá que las cookies funcionen. Cambia a false o habilita HTTPS\n";
        }
    }
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";


