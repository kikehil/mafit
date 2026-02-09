<?php
// Script para obtener el Refresh Token de Google Drive (Consola)
// Basado en el flujo OAuth 2.0 para aplicaciones web

require 'vendor/autoload.php';

// --- CONFIGURACIÓN ---
// Reemplaza estos valores con los tuyos
$clientId = 'TU_CLIENT_ID_AQUI';
$clientSecret = 'TU_CLIENT_SECRET_AQUI';
$redirectUri = 'https://mafit.regiontamaulipas.com.mx'; // DEBE coincidir EXACTAMENTE con la URI autorizada en Google Cloud Console

// Si estás probando localmente, Google a veces pide http://localhost o http://127.0.0.1
// Pero si ya configuraste el dominio en la consola, usa ese.

$client = new Google\Client();
// Desactivar verificación SSL para evitar error de certificado local en Windows
$client->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->setAccessType('offline'); // CRUCIAL para obtener el refresh_token
$client->setPrompt('consent'); // Forzar pantalla de consentimiento para asegurar refresh_token
$client->addScope(Google\Service\Drive::DRIVE_FILE); 

// --- FLUJO ---

// 1. Generar URL de autorización
$authUrl = $client->createAuthUrl();

echo "------------------------------------------------------------------\n";
echo "PARA OBTENER EL REFRESH TOKEN:\n";
echo "1. Abre la siguiente URL en tu navegador (donde estés logueado con la cuenta de Google propietaria del Drive):\n\n";
echo $authUrl . "\n\n";
echo "2. Acepta los permisos.\n";
echo "3. Google te redirigirá a una URL que empieza con '$redirectUri' y tiene un parámetro '?code=...'\n";
echo "4. Copia TODO el valor del parámetro 'code' (sin '&scope=...' ni nada extra) y pégalo aquí abajo.\n";
echo "------------------------------------------------------------------\n";
echo "Ingresa el código de autorización: ";

// Leer input de consola
$handle = fopen ("php://stdin","r");
$authCode = trim(fgets($handle));

if (empty($authCode)) {
    die("Error: No ingresaste ningún código.\n");
}

try {
    // 2. Intercambiar código por tokens
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

    // Verificar errores
    if (array_key_exists('error', $accessToken)) {
        throw new Exception(join(', ', $accessToken));
    }

    echo "\n------------------------------------------------------------------\n";
    echo "¡ÉXITO! Aquí están tus credenciales:\n";
    echo "------------------------------------------------------------------\n\n";

    if (isset($accessToken['refresh_token'])) {
        echo "GOOGLE_DRIVE_REFRESH_TOKEN=" . $accessToken['refresh_token'] . "\n\n";
        echo "Copia este valor en tu archivo .env\n";
    } else {
        echo "ADVERTENCIA: No se recibió un refresh_token.\n";
        echo "Esto suele pasar si ya autorizaste la app antes y no usaste prompt='consent'.\n";
        echo "Intenta revocar el acceso de la app en tu cuenta de Google y vuelve a intentar, o asegúrate que el script usa setPrompt('consent').\n";
    }

    echo "Access Token (temporal): " . $accessToken['access_token'] . "\n";
    
} catch (Exception $e) {
    echo "Ocurrió un error: " . $e->getMessage() . "\n";
}
