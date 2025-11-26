<?php
require_once 'Database.php';

class InscripcionDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // ====================================================================
    // 1. MÉTODOS DE LECTURA (READ)
    // ====================================================================

    /**
     * Obtener todas las inscripciones con información relacionada
     */
    public function obtenerTodas() {
        $query = "SELECT i.*, 
                         u.nombre as usuario_nombre, 
                         u.email as usuario_email,
                         e.titulo as evento_titulo,
                         c.nombre as curso_nombre
                  FROM inscripciones i
                  INNER JOIN usuarios u ON i.usuario_id = u.id
                  INNER JOIN eventos e ON i.evento_id = e.id
                  LEFT JOIN cursos c ON i.curso_id = c.id
                  ORDER BY i.fecha_inscripcion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener inscripciones por evento
     */
    public function obtenerPorEvento($evento_id) {
        $query = "SELECT i.*, 
                         u.nombre as usuario_nombre, 
                         u.email as usuario_email,
                         e.titulo as evento_titulo,
                         c.nombre as curso_nombre
                  FROM inscripciones i
                  INNER JOIN usuarios u ON i.usuario_id = u.id
                  INNER JOIN eventos e ON i.evento_id = e.id
                  LEFT JOIN cursos c ON i.curso_id = c.id
                  WHERE i.evento_id = :evento_id AND i.estado = 'confirmada'
                  ORDER BY i.fecha_inscripcion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":evento_id", $evento_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener inscripciones por curso
     */
    public function obtenerPorCurso($curso_id) {
        $query = "SELECT i.*, 
                         u.nombre as usuario_nombre, 
                         u.email as usuario_email,
                         e.titulo as evento_titulo,
                         c.nombre as curso_nombre
                  FROM inscripciones i
                  INNER JOIN usuarios u ON i.usuario_id = u.id
                  INNER JOIN eventos e ON i.evento_id = e.id
                  LEFT JOIN cursos c ON i.curso_id = c.id
                  WHERE i.curso_id = :curso_id AND i.estado = 'confirmada'
                  ORDER BY i.fecha_inscripcion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":curso_id", $curso_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener inscripciones por usuario
     */
    public function obtenerPorUsuario($usuario_id) {
        $query = "SELECT i.*, 
                         e.titulo as evento_titulo,
                         e.imagen as evento_imagen,
                         e.fecha_texto as evento_fecha,
                         c.nombre as curso_nombre
                  FROM inscripciones i
                  INNER JOIN eventos e ON i.evento_id = e.id
                  LEFT JOIN cursos c ON i.curso_id = c.id
                  WHERE i.usuario_id = :usuario_id AND i.estado = 'confirmada'
                  ORDER BY i.fecha_inscripcion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si un usuario está inscrito a un evento
     */
    public function verificarInscripcionEvento($usuario_id, $evento_id) {
        $query = "SELECT id FROM inscripciones 
                  WHERE usuario_id = :usuario_id 
                  AND evento_id = :evento_id 
                  AND curso_id IS NULL 
                  AND estado = 'confirmada'
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':evento_id' => $evento_id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    /**
     * Verificar si un usuario está inscrito a un curso
     */
    public function verificarInscripcionCurso($usuario_id, $evento_id, $curso_id) {
        $query = "SELECT id FROM inscripciones 
                  WHERE usuario_id = :usuario_id 
                  AND evento_id = :evento_id 
                  AND curso_id = :curso_id 
                  AND estado = 'confirmada'
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':evento_id' => $evento_id,
            ':curso_id' => $curso_id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    /**
     * Contar inscripciones por evento
     */
    public function contarPorEvento($evento_id) {
        $query = "SELECT COUNT(*) as total 
                  FROM inscripciones 
                  WHERE evento_id = :evento_id 
                  AND estado = 'confirmada'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":evento_id", $evento_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    /**
     * Contar inscripciones por curso
     */
    public function contarPorCurso($curso_id) {
        $query = "SELECT COUNT(*) as total 
                  FROM inscripciones 
                  WHERE curso_id = :curso_id 
                  AND estado = 'confirmada'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":curso_id", $curso_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    /**
     * Contar total de inscripciones
     */
    public function contarTotal() {
        $query = "SELECT COUNT(*) as total FROM inscripciones WHERE estado = 'confirmada'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    // ====================================================================
    // 2. MÉTODOS DE ESCRITURA (CREATE, UPDATE, DELETE)
    // ====================================================================

    /**
     * Crear nueva inscripción
     */
    public function crear($usuario_id, $evento_id, $curso_id = null) {
        $query = "INSERT INTO inscripciones (usuario_id, evento_id, curso_id, estado) 
                  VALUES (:usuario_id, :evento_id, :curso_id, 'confirmada')";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':evento_id' => $evento_id,
            ':curso_id' => $curso_id
        ]);
    }

    /**
     * Cancelar inscripción (cambiar estado)
     */
    public function cancelar($id) {
        $query = "UPDATE inscripciones SET estado = 'cancelada' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    /**
     * Eliminar inscripción
     */
    public function eliminar($id) {
        $query = "DELETE FROM inscripciones WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    /**
     * Obtener inscripción por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT i.*, 
                         u.nombre as usuario_nombre, 
                         u.email as usuario_email,
                         e.titulo as evento_titulo,
                         c.nombre as curso_nombre
                  FROM inscripciones i
                  INNER JOIN usuarios u ON i.usuario_id = u.id
                  INNER JOIN eventos e ON i.evento_id = e.id
                  LEFT JOIN cursos c ON i.curso_id = c.id
                  WHERE i.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
