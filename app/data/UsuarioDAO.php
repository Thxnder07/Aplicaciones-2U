<?php
require_once 'Database.php';

class UsuarioDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Buscar usuario por email para el login
    public function obtenerPorEmail($email) {
        $query = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Registrar nuevo usuario (hash de password se hace en el Service)
    public function registrar($nombre, $email, $passwordHash) {
        $query = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :pass)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':pass' => $passwordHash
        ]);
    }

    // Obtener usuario por ID (para sesiones)
    public function obtenerPorId($id) {
        $query = "SELECT id, nombre, email, rol, created_at FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar si un email ya existe
    public function verificarEmail($email) {
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    // Obtener total de usuarios registrados (para dashboard)
    public function contarUsuarios() {
        $query = "SELECT COUNT(*) as total FROM usuarios";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    // Actualizar datos de usuario (para perfil - opcional)
    public function actualizarUsuario($id, $nombre, $email) {
        $query = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $nombre,
            ':email' => $email
        ]);
    }
}
?>