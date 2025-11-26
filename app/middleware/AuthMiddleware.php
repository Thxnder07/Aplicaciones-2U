<?php
/**
 * Middleware para verificar si el usuario está autenticado
 * Redirige al login si no hay sesión activa
 */
class AuthMiddleware {
    
    /**
     * Verificar si el usuario está autenticado
     * @return bool true si está autenticado, false si no
     */
    public static function verificar() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
    }

    /**
     * Requerir autenticación - redirige al login si no está autenticado
     * @param string $redirectUrl URL a la que redirigir después del login (opcional)
     */
    public static function requerirAutenticacion($redirectUrl = null) {
        if (!self::verificar()) {
            $url = 'index.php?view=login';
            if ($redirectUrl) {
                $url .= '&redirect=' . urlencode($redirectUrl);
            }
            header("Location: " . $url);
            exit;
        }
    }

    /**
     * Obtener datos del usuario autenticado
     * @return array|null Datos del usuario o null si no está autenticado
     */
    public static function obtenerUsuario() {
        if (!self::verificar()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['nombre'] ?? '',
            'email' => $_SESSION['email'] ?? '',
            'rol' => $_SESSION['rol'] ?? 'usuario'
        ];
    }

    /**
     * Verificar si el usuario tiene un rol específico
     * @param string $rol 'admin' o 'usuario'
     * @return bool
     */
    public static function tieneRol($rol) {
        if (!self::verificar()) {
            return false;
        }
        
        return ($_SESSION['rol'] ?? '') === $rol;
    }
}
?>

