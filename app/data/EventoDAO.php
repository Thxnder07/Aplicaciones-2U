<?php
require_once 'Database.php';

class EventoDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // ====================================================================
    // 1. MÉTODOS DE LECTURA (READ)
    // ====================================================================

    // Listar todos los eventos
    public function obtenerTodos() {
        // Ordenamos por ID descendente para ver los últimos creados primero
        $query = "SELECT * FROM eventos ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un evento por ID
    public function obtenerPorId($id) {
        $query = "SELECT * FROM eventos WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ====================================================================
    // 2. MÉTODOS DE ESCRITURA (CREATE, UPDATE, DELETE)
    // ====================================================================

    // Crear nuevo evento (Actualizado con nuevos campos)
    public function crear($titulo, $fecha_texto, $lugar, $precio, $imagen, $descripcion, $horario) {
        $query = "INSERT INTO eventos (titulo, fecha_texto, lugar, precio, imagen, descripcion_breve, horario) 
                  VALUES (:titulo, :fecha, :lugar, :precio, :imagen, :desc, :hora)";
        
        $stmt = $this->conn->prepare($query);
        
        // Ejecutamos pasando el array de datos (es más limpio que hacer bindParam uno por uno)
        return $stmt->execute([
            ':titulo' => $titulo,
            ':fecha'  => $fecha_texto,
            ':lugar'  => $lugar,
            ':precio' => $precio,
            ':imagen' => $imagen,
            ':desc'   => $descripcion,
            ':hora'   => $horario
        ]);
    }

    // Actualizar evento existente (Nuevo método que faltaba)
    public function actualizar($id, $titulo, $fecha_texto, $lugar, $precio, $imagen, $descripcion, $horario) {
        $query = "UPDATE eventos SET 
                    titulo = :titulo, 
                    fecha_texto = :fecha, 
                    lugar = :lugar, 
                    precio = :precio, 
                    imagen = :imagen, 
                    descripcion_breve = :desc, 
                    horario = :hora 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':id'     => $id,
            ':titulo' => $titulo,
            ':fecha'  => $fecha_texto,
            ':lugar'  => $lugar,
            ':precio' => $precio,
            ':imagen' => $imagen,
            ':desc'   => $descripcion,
            ':hora'   => $horario
        ]);
    }

    // Eliminar evento (Nuevo método que faltaba)
    public function eliminar($id) {
        $query = "DELETE FROM eventos WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>