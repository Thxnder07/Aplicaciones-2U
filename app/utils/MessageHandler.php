<?php
/**
 * Clase para manejar mensajes flash (mensajes temporales)
 * Los mensajes se guardan en sesión y se muestran una sola vez
 */
class MessageHandler {
    
    /**
     * Establecer un mensaje de éxito
     * @param string $mensaje
     */
    public static function setSuccess($mensaje) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['flash_message'] = [
            'tipo' => 'success',
            'mensaje' => $mensaje
        ];
    }

    /**
     * Establecer un mensaje de error
     * @param string $mensaje
     */
    public static function setError($mensaje) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['flash_message'] = [
            'tipo' => 'error',
            'mensaje' => $mensaje
        ];
    }

    /**
     * Establecer un mensaje de advertencia
     * @param string $mensaje
     */
    public static function setWarning($mensaje) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['flash_message'] = [
            'tipo' => 'warning',
            'mensaje' => $mensaje
        ];
    }

    /**
     * Establecer un mensaje de información
     * @param string $mensaje
     */
    public static function setInfo($mensaje) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['flash_message'] = [
            'tipo' => 'info',
            'mensaje' => $mensaje
        ];
    }

    /**
     * Obtener y eliminar el mensaje flash (se muestra una sola vez)
     * @return array|null ['tipo' => string, 'mensaje' => string] o null
     */
    public static function getFlash() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (isset($_SESSION['flash_message'])) {
            $mensaje = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']); // Eliminar después de leer
            return $mensaje;
        }
        
        return null;
    }

    /**
     * Mostrar el mensaje flash si existe (HTML)
     * @return string HTML del mensaje o string vacío
     */
    public static function showFlash() {
        $mensaje = self::getFlash();
        
        if ($mensaje) {
            $tipo = $mensaje['tipo'];
            $texto = htmlspecialchars($mensaje['mensaje']);
            
            $iconos = [
                'success' => 'check-circle',
                'error' => 'exclamation-circle',
                'warning' => 'exclamation-triangle',
                'info' => 'info-circle'
            ];
            
            $icono = $iconos[$tipo] ?? 'info-circle';
            
            return "
            <div class='alert alert-{$tipo} alert-dismissible fade show' role='alert'>
                <i class='fas fa-{$icono}'></i> {$texto}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        }
        
        return '';
    }
}
?>

