<?php
/**
 * Utilidad para protección CSRF (Cross-Site Request Forgery)
 * Genera y valida tokens CSRF para formularios
 */
class CSRFProtection {
    
    /**
     * Generar token CSRF
     * @return string Token generado
     */
    public static function generarToken() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validar token CSRF
     * @param string $token Token a validar
     * @return bool true si es válido, false si no
     */
    public static function validarToken($token) {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Obtener campo hidden con token para formularios
     * @return string HTML del input hidden
     */
    public static function obtenerCampoHidden() {
        $token = self::generarToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Regenerar token (útil después de usar)
     */
    public static function regenerarToken() {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
?>
