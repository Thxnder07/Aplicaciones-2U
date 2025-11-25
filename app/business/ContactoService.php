<?php
require_once __DIR__ . '/../data/Database.php'; // O MensajeDAO si Aquino lo creó

class ContactoService {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function procesarFormulario() {
        $nombre = $_POST['nombre'] ?? '';
        $email  = $_POST['email'] ?? '';
        $mensaje = $_POST['mensaje'] ?? '';

        // 1. Validar
        if (empty($nombre) || empty($email) || empty($mensaje)) {
            return ["status" => "error", "msg" => "Por favor, completa todos los campos."];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["status" => "error", "msg" => "El correo electrónico no es válido."];
        }

        // 2. Guardar en BD (Lógica directa o vía DAO)
        try {
            $sql = "INSERT INTO mensajes (nombre, email, mensaje) VALUES (:nom, :email, :msj)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nom', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':msj', $mensaje);
            $stmt->execute();
            
            return ["status" => "success", "msg" => "¡Mensaje enviado! Nos pondremos en contacto pronto."];
        } catch (PDOException $e) {
            return ["status" => "error", "msg" => "Error del sistema: " . $e->getMessage()];
        }
    }
}
?>
