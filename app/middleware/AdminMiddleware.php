<?php
require_once __DIR__ . '/AuthMiddleware.php';

/**
 * Middleware para verificar si el usuario es administrador
 * Redirige al dashboard de usuario o login si no es admin
 */
class AdminMiddleware {
    
    /**
     * Verificar si el usuario es administrador
     * @return bool true si es admin, false si no
     */
    public static function verificar() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        return AuthMiddleware::verificar() && AuthMiddleware::tieneRol('admin');
    }

    /**
     * Requerir rol de administrador - redirige si no es admin
     */
    public static function requerirAdmin() {
        // Primero verificar si está autenticado
        if (!AuthMiddleware::verificar()) {
            header("Location: index.php?view=login&msg=requiere_login");
            exit;
        }
        
        // Luego verificar si es admin
        if (!self::verificar()) {
            // Si es usuario normal, redirigir a su dashboard
            if (AuthMiddleware::tieneRol('usuario')) {
                header("Location: index.php?view=usuario/dashboard&msg=sin_permisos");
            } else {
                header("Location: index.php?view=login&msg=requiere_admin");
            }
            exit;
        }
    }
}
?>

