<?php
/**
 * Configuración de la aplicación
 * 
 * IMPORTANTE: En producción, estas variables deben estar en variables de entorno
 * o en un archivo .env que NO se suba al repositorio.
 */

// Modo de la aplicación: 'development' o 'production'
define('APP_MODE', 'development'); // Cambiar a 'production' en producción

// Configuración de base de datos
// En producción, usar variables de entorno:
// $_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'eventhub_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Configuración de errores
if (APP_MODE === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Configuración de sesión
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', APP_MODE === 'production' ? '1' : '0');
ini_set('session.use_strict_mode', '1');

// Timezone
date_default_timezone_set('America/Lima');
?>

