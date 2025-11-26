<?php
// Cargar dependencias necesarias
require_once __DIR__ . '/../../utils/MessageHandler.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

// Si el usuario ya está autenticado, redirigir al dashboard correspondiente
if (AuthMiddleware::verificar()) {
    $rol = $_SESSION['rol'] ?? 'usuario';
    if ($rol === 'admin') {
        header("Location: index.php?view=admin/dashboard");
    } else {
        header("Location: index.php?view=usuario/dashboard");
    }
    exit;
}

$page = 'registro';
$title = 'Crear Cuenta - EventHub';

// Obtener mensaje flash si existe
$mensajeFlash = MessageHandler::getFlash();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>

    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body style="margin: 0; padding: 0; font-family: var(--font-body);">
    <div style="display: flex; min-height: 100vh; width: 100%;">

        <!-- Left Side - Form (White Background) -->
        <div
            style="flex: 1; display: flex; align-items: center; justify-content: center; background: #ffffff; padding: 2rem;">
            <div style="width: 100%; max-width: 400px;">

                <!-- Logo/Brand -->
                <a href="index.php?view=home"
                    style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; color: var(--primary-color); text-decoration: none; display: block; margin-bottom: 3rem;">
                    EventHub
                </a>

                <!-- Title -->
                <h1
                    style="font-family: var(--font-heading); font-size: 2rem; font-weight: 400; color: #333; margin-bottom: 2.5rem;">
                    Create your account,</h1>

                <!-- Alerts -->
                <?php if ($mensajeFlash): ?>
                    <div class="alert alert-<?php echo $mensajeFlash['tipo']; ?>">
                        <i
                            class="fas fa-<?php echo $mensajeFlash['tipo'] === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                        <?php echo htmlspecialchars($mensajeFlash['mensaje']); ?>
                    </div>
                <?php endif; ?>

                <!-- Registration Form -->
                <form method="POST" action="index.php?view=procesar_registro" id="registroForm">
                    
                    <!-- Name Input -->
                    <div style="margin-bottom: 1.5rem;">
                        <label
                            style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #999; margin-bottom: 0.5rem;">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" required minlength="3" autocomplete="name"
                            style="width: 100%; padding: 0.75rem 0; border: none; border-bottom: 1px solid #ddd; font-size: 1rem; outline: none; transition: border-color 0.3s; background: transparent;"
                            onfocus="this.style.borderBottomColor='var(--secondary-color)'"
                            onblur="this.style.borderBottomColor='#ddd'"
                            value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                    </div>

                    <!-- Email Input -->
                    <div style="margin-bottom: 1.5rem;">
                        <label
                            style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #999; margin-bottom: 0.5rem;">Email</label>
                        <input type="email" id="email" name="email" required autocomplete="email"
                            style="width: 100%; padding: 0.75rem 0; border: none; border-bottom: 1px solid #ddd; font-size: 1rem; outline: none; transition: border-color 0.3s; background: transparent;"
                            onfocus="this.style.borderBottomColor='var(--secondary-color)'"
                            onblur="this.style.borderBottomColor='#ddd'"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <!-- Password Input -->
                    <div style="margin-bottom: 1.5rem; position: relative;">
                        <label
                            style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #999; margin-bottom: 0.5rem;">Password</label>
                        <input type="password" id="password" name="password" required minlength="6"
                            autocomplete="new-password"
                            style="width: 100%; padding: 0.75rem 0; border: none; border-bottom: 1px solid #ddd; font-size: 1rem; outline: none; transition: border-color 0.3s; background: transparent;"
                            onfocus="this.style.borderBottomColor='var(--secondary-color)'"
                            onblur="this.style.borderBottomColor='#ddd'">
                        <small style="display: block; color: #999; font-size: 0.7rem; margin-top: 0.25rem;">Mínimo 6 caracteres</small>
                    </div>

                    <!-- Confirm Password Input -->
                    <div style="margin-bottom: 2rem; position: relative;">
                        <label
                            style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #999; margin-bottom: 0.5rem;">Confirm Password</label>
                        <input type="password" id="password_confirm" name="password_confirm" required minlength="6"
                            autocomplete="new-password"
                            style="width: 100%; padding: 0.75rem 0; border: none; border-bottom: 1px solid #ddd; font-size: 1rem; outline: none; transition: border-color 0.3s; background: transparent;"
                            onfocus="this.style.borderBottomColor='var(--secondary-color)'"
                            onblur="this.style.borderBottomColor='#ddd'">
                    </div>

                    <!-- Sign Up Button -->
                    <button type="submit"
                        style="width: 100%; padding: 1rem; background: #000; color: white; border: none; border-radius: 50px; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; cursor: pointer; transition: all 0.3s; margin-bottom: 1.5rem;"
                        onmouseover="this.style.background='#333'"
                        onmouseout="this.style.background='#000'">
                        SIGN UP
                    </button>

                    <!-- Social Login -->
                    <button type="button" onclick="alert('Facebook login no disponible')"
                        style="width: 100%; padding: 1rem; background: white; color: #666; border: 1px solid #ddd; border-radius: 50px; font-size: 0.9rem; cursor: pointer; transition: all 0.3s;">
                        Connect with <strong>facebook</strong>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Side - Image with CTA -->
        <div
            style="flex: 1; position: relative; background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(30, 41, 59, 0.85)), url('https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=2069&auto=format&fit=crop'); background-size: cover; background-position: center; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; padding: 3rem;">

            <h2
                style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 400; margin-bottom: 1rem; text-align: center;">
                Already have an account?</h2>
            <p style="font-size: 1rem; margin-bottom: 2.5rem; text-align: center; max-width: 400px; opacity: 0.95;">Sign
                in to access your events and continue your journey!</p>

            <a href="index.php?view=login"
                style="padding: 0.875rem 3rem; background: transparent; color: white; border: 2px solid white; border-radius: 50px; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; text-decoration: none; transition: all 0.3s;">
                SIGN IN
            </a>
        </div>
    </div>

    <script>
        // Validación de contraseñas coincidentes
        document.getElementById('registroForm').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;

            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
                return false;
            }
        });
    </script>
</body>

</html>