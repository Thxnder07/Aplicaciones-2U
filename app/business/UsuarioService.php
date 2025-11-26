<?php
require_once __DIR__ . '/../data/UsuarioDAO.php';

class UsuarioService {
    private $usuarioDAO;

    public function __construct() {
        $this->usuarioDAO = new UsuarioDAO();
    }

    /**
     * Autenticar usuario (login)
     * @param string $email
     * @param string $password
     * @return array ['success' => bool, 'usuario' => array|null, 'mensaje' => string]
     */
    public function login($email, $password) {
        // Validaciones básicas
        if (empty($email) || empty($password)) {
            return [
                'success' => false,
                'usuario' => null,
                'mensaje' => 'El correo y la contraseña son obligatorios.'
            ];
        }

        // Buscar usuario por email
        $usuario = $this->usuarioDAO->obtenerPorEmail($email);

        if (!$usuario) {
            return [
                'success' => false,
                'usuario' => null,
                'mensaje' => 'Credenciales incorrectas.'
            ];
        }

        // Verificar contraseña
        if (!password_verify($password, $usuario['password'])) {
            return [
                'success' => false,
                'usuario' => null,
                'mensaje' => 'Credenciales incorrectas.'
            ];
        }

        // Login exitoso - retornar datos del usuario (sin password)
        unset($usuario['password']);
        return [
            'success' => true,
            'usuario' => $usuario,
            'mensaje' => 'Inicio de sesión exitoso.'
        ];
    }

    /**
     * Registrar nuevo usuario
     * @param string $nombre
     * @param string $email
     * @param string $password
     * @return array ['success' => bool, 'mensaje' => string, 'errores' => array]
     */
    public function registrar($nombre, $email, $password) {
        $errores = [];

        // Validaciones
        if (empty($nombre) || strlen(trim($nombre)) < 3) {
            $errores[] = 'El nombre debe tener al menos 3 caracteres.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido.';
        }

        if (empty($password) || strlen($password) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
        }

        // Verificar si el email ya existe
        if (!empty($email) && $this->usuarioDAO->verificarEmail($email)) {
            $errores[] = 'Este correo electrónico ya está registrado.';
        }

        // Si hay errores, retornarlos
        if (!empty($errores)) {
            return [
                'success' => false,
                'mensaje' => 'Error en la validación de datos.',
                'errores' => $errores
            ];
        }

        // Hash de la contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Registrar usuario
        $registrado = $this->usuarioDAO->registrar(
            trim($nombre),
            trim(strtolower($email)),
            $passwordHash
        );

        if ($registrado) {
            return [
                'success' => true,
                'mensaje' => 'Usuario registrado exitosamente. Ya puedes iniciar sesión.',
                'errores' => []
            ];
        } else {
            return [
                'success' => false,
                'mensaje' => 'Error al registrar el usuario. Intenta nuevamente.',
                'errores' => ['Error en la base de datos.']
            ];
        }
    }

    /**
     * Verificar si un email existe
     * @param string $email
     * @return bool
     */
    public function verificarEmailExistente($email) {
        return $this->usuarioDAO->verificarEmail($email);
    }

    /**
     * Obtener usuario por ID (para sesiones)
     * @param int $id
     * @return array|null
     */
    public function obtenerUsuario($id) {
        return $this->usuarioDAO->obtenerPorId($id);
    }

    /**
     * Contar total de usuarios (para dashboard)
     * @return int
     */
    public function contarUsuarios() {
        return $this->usuarioDAO->contarUsuarios();
    }
}
?>

