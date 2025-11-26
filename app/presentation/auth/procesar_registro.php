<?php
session_start();
require_once __DIR__ . '/../../business/UsuarioService.php';
require_once __DIR__ . '/../../utils/MessageHandler.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?view=registro");
    exit;
}

// Obtener datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// Validar que las contraseñas coincidan
if ($password !== $password_confirm) {
    MessageHandler::setError('Las contraseñas no coinciden.');
    header("Location: index.php?view=registro");
    exit;
}

// Procesar registro
$usuarioService = new UsuarioService();
$resultado = $usuarioService->registrar($nombre, $email, $password);

if ($resultado['success']) {
    // Registro exitoso
    MessageHandler::setSuccess($resultado['mensaje']);
    header("Location: index.php?view=login");
    exit;
} else {
    // Registro fallido - mostrar errores
    $errores = $resultado['errores'] ?? [];
    $mensajeError = !empty($errores) ? implode(' ', $errores) : $resultado['mensaje'];
    MessageHandler::setError($mensajeError);
    
    // Mantener datos del formulario en sesión para no perderlos
    $_SESSION['form_data'] = [
        'nombre' => $nombre,
        'email' => $email
    ];
    
    header("Location: index.php?view=registro");
    exit;
}
?>

