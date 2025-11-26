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

    // Obtener el último ID insertado
    public function obtenerUltimoId() {
        return $this->conn->lastInsertId();
    }

    // ====================================================================
    // 2. MÉTODOS DE ESCRITURA (CREATE, UPDATE, DELETE)
    // ====================================================================

    // Crear nuevo evento (Actualizado con nuevos campos)
    public function crear($titulo, $fecha_texto, $lugar, $precio, $imagen, $descripcion, $horario, $estado = 'activo', $cupos = 100, $fecha_inicio = null, $destacado = 1) {
        $query = "INSERT INTO eventos (titulo, fecha_texto, fecha_inicio, lugar, precio, imagen, descripcion_breve, horario, estado, cupos, cupos_disponibles, destacado) 
                  VALUES (:titulo, :fecha, :fecha_inicio, :lugar, :precio, :imagen, :desc, :hora, :estado, :cupos, :cupos_disponibles, :destacado)";
        
        $stmt = $this->conn->prepare($query);
        
        // Ejecutamos pasando el array de datos (es más limpio que hacer bindParam uno por uno)
        return $stmt->execute([
            ':titulo' => $titulo,
            ':fecha'  => $fecha_texto,
            ':fecha_inicio' => $fecha_inicio,
            ':lugar'  => $lugar,
            ':precio' => $precio,
            ':imagen' => $imagen,
            ':desc'   => $descripcion,
            ':hora'   => $horario,
            ':estado' => $estado,
            ':cupos' => $cupos,
            ':cupos_disponibles' => $cupos,
            ':destacado' => $destacado ? 1 : 0
        ]);
    }

    // Actualizar evento existente (Nuevo método que faltaba)
    public function actualizar($id, $titulo, $fecha_texto, $lugar, $precio, $imagen, $descripcion, $horario, $estado = null, $cupos = null, $fecha_inicio = null, $destacado = null) {
        $query = "UPDATE eventos SET 
                    titulo = :titulo, 
                    fecha_texto = :fecha, 
                    lugar = :lugar, 
                    precio = :precio, 
                    imagen = :imagen, 
                    descripcion_breve = :desc, 
                    horario = :hora";
        
        $params = [
            ':id'     => $id,
            ':titulo' => $titulo,
            ':fecha'  => $fecha_texto,
            ':lugar'  => $lugar,
            ':precio' => $precio,
            ':imagen' => $imagen,
            ':desc'   => $descripcion,
            ':hora'   => $horario
        ];
        
        if ($fecha_inicio !== null) {
            $query .= ", fecha_inicio = :fecha_inicio";
            $params[':fecha_inicio'] = $fecha_inicio;
        }
        
        if ($estado !== null) {
            $query .= ", estado = :estado";
            $params[':estado'] = $estado;
        }
        
        if ($cupos !== null) {
            $query .= ", cupos = :cupos";
            $params[':cupos'] = $cupos;
        }
        
        if ($destacado !== null) {
            $query .= ", destacado = :destacado";
            $params[':destacado'] = $destacado ? 1 : 0;
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }
    
    /**
     * Cambiar estado del evento
     */
    public function cambiarEstado($id, $estado) {
        $query = "UPDATE eventos SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':estado' => $estado
        ]);
    }
    
    /**
     * Decrementar cupos disponibles
     */
    public function decrementarCupos($id) {
        $query = "UPDATE eventos SET cupos_disponibles = cupos_disponibles - 1 
                  WHERE id = :id AND cupos_disponibles > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    /**
     * Incrementar cupos disponibles
     */
    public function incrementarCupos($id) {
        $query = "UPDATE eventos SET cupos_disponibles = cupos_disponibles + 1 
                  WHERE id = :id AND cupos_disponibles < cupos";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
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