<?php
session_start();
require_once __DIR__ . '/../../business/UsuarioService.php';
require_once __DIR__ . '/../../utils/MessageHandler.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?view=login");
    exit;
}

// Obtener datos del formulario
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$redirect = $_POST['redirect'] ?? '';

// Validación básica
if (empty($email) || empty($password)) {
    MessageHandler::setError('El correo y la contraseña son obligatorios.');
    $url = "index.php?view=login";
    if ($redirect) {
        $url .= "&redirect=" . urlencode($redirect);
    }
    header("Location: " . $url);
    exit;
}

// Procesar login
$usuarioService = new UsuarioService();
$resultado = $usuarioService->login($email, $password);

if ($resultado['success']) {
    // Login exitoso - crear sesión
    $_SESSION['usuario_id'] = $resultado['usuario']['id'];
    $_SESSION['nombre'] = $resultado['usuario']['nombre'];
    $_SESSION['email'] = $resultado['usuario']['email'];
    $_SESSION['rol'] = $resultado['usuario']['rol'];
    
    // Mensaje de éxito
    MessageHandler::setSuccess('¡Bienvenido, ' . $resultado['usuario']['nombre'] . '!');
    
    // Redirigir según rol o URL de redirección
    if (!empty($redirect)) {
        header("Location: " . $redirect);
    } else if ($resultado['usuario']['rol'] === 'admin') {
        header("Location: index.php?view=admin/dashboard");
    } else {
        header("Location: index.php?view=usuario/dashboard");
    }
    exit;
} else {
    // Login fallido
    MessageHandler::setError($resultado['mensaje']);
    $url = "index.php?view=login";
    if ($redirect) {
        $url .= "&redirect=" . urlencode($redirect);
    }
    header("Location: " . $url);
    exit;
}
?>

