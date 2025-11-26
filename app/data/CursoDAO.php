<?php
require_once 'Database.php';

class CursoDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // ====================================================================
    // 1. MÉTODOS DE LECTURA (READ)
    // ====================================================================

    /**
     * Obtener todos los cursos
     */
    public function obtenerTodos() {
        $query = "SELECT c.*, e.titulo as evento_titulo, p.nombre as ponente_nombre 
                  FROM cursos c
                  LEFT JOIN eventos e ON c.evento_id = e.id
                  LEFT JOIN ponentes p ON c.ponente_id = p.id
                  ORDER BY c.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un curso por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT c.*, e.titulo as evento_titulo, p.nombre as ponente_nombre 
                  FROM cursos c
                  LEFT JOIN eventos e ON c.evento_id = e.id
                  LEFT JOIN ponentes p ON c.ponente_id = p.id
                  WHERE c.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener el último ID insertado
     */
    public function obtenerUltimoId() {
        return $this->conn->lastInsertId();
    }

    /**
     * Obtener cursos por evento
     */
    public function obtenerPorEvento($evento_id) {
        $query = "SELECT c.*, p.nombre as ponente_nombre 
                  FROM cursos c
                  LEFT JOIN ponentes p ON c.ponente_id = p.id
                  WHERE c.evento_id = :evento_id
                  ORDER BY c.fecha, c.horario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":evento_id", $evento_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar cursos por evento
     */
    public function contarPorEvento($evento_id) {
        $query = "SELECT COUNT(*) as total FROM cursos WHERE evento_id = :evento_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":evento_id", $evento_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    // ====================================================================
    // 2. MÉTODOS DE ESCRITURA (CREATE, UPDATE, DELETE)
    // ====================================================================

    /**
     * Crear nuevo curso
     */
    public function crear($evento_id, $nombre, $descripcion, $fecha, $horario, $ponente_id, $cupos, $precio) {
        $query = "INSERT INTO cursos (evento_id, nombre, descripcion, fecha, horario, ponente_id, cupos, cupos_disponibles, precio) 
                  VALUES (:evento_id, :nombre, :descripcion, :fecha, :horario, :ponente_id, :cupos, :cupos_disponibles, :precio)";
        
        $stmt = $this->conn->prepare($query);
        
        // cupos_disponibles inicia igual a cupos
        return $stmt->execute([
            ':evento_id' => $evento_id,
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':fecha' => $fecha,
            ':horario' => $horario,
            ':ponente_id' => $ponente_id ? $ponente_id : null,
            ':cupos' => $cupos,
            ':cupos_disponibles' => $cupos,
            ':precio' => $precio ? $precio : null
        ]);
    }

    /**
     * Actualizar curso existente
     */
    public function actualizar($id, $nombre, $descripcion, $fecha, $horario, $ponente_id, $cupos, $precio) {
        $query = "UPDATE cursos SET 
                    nombre = :nombre, 
                    descripcion = :descripcion,
                    fecha = :fecha,
                    horario = :horario,
                    ponente_id = :ponente_id,
                    cupos = :cupos,
                    precio = :precio
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':fecha' => $fecha,
            ':horario' => $horario,
            ':ponente_id' => $ponente_id ? $ponente_id : null,
            ':cupos' => $cupos,
            ':precio' => $precio ? $precio : null
        ]);
    }

    /**
     * Decrementar cupos disponibles
     */
    public function decrementarCupos($id) {
        $query = "UPDATE cursos SET cupos_disponibles = cupos_disponibles - 1 
                  WHERE id = :id AND cupos_disponibles > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    /**
     * Incrementar cupos disponibles
     */
    public function incrementarCupos($id) {
        $query = "UPDATE cursos SET cupos_disponibles = cupos_disponibles + 1 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    /**
     * Eliminar curso
     */
    public function eliminar($id) {
        $query = "DELETE FROM cursos WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
