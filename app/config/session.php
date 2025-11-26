<?php
/**
 * Archivo de inicialización de sesión
 * Incluir este archivo al inicio de cada página que requiera sesión
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de seguridad de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS

// Regenerar ID de sesión periódicamente para seguridad
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    // Regenerar cada 30 minutos
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
?>

