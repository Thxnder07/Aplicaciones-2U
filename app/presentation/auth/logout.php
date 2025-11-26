<?php
session_start();
require_once __DIR__ . '/../../utils/MessageHandler.php';

// Obtener nombre del usuario antes de destruir la sesión
$nombreUsuario = $_SESSION['nombre'] ?? 'Usuario';

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, también borrar la cookie de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();

// Mensaje de despedida
MessageHandler::setInfo('Sesión cerrada correctamente. ¡Hasta pronto, ' . $nombreUsuario . '!');

// Redirigir al inicio
header("Location: index.php?view=home");
exit;
?>

