<?php
require_once 'Database.php';

class LogDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Registrar una acción en el log
     * @param int $usuario_id ID del usuario que realiza la acción
     * @param string $accion Tipo de acción (crear_evento, editar_evento, etc.)
     * @param string $entidad Tipo de entidad (evento, curso, inscripcion, etc.)
     * @param int|null $entidad_id ID de la entidad afectada
     * @param string|null $detalles Detalles adicionales de la acción
     * @param string|null $ip_address Dirección IP del usuario
     * @return bool true si se registró correctamente
     */
    public function registrar($usuario_id, $accion, $entidad, $entidad_id = null, $detalles = null, $ip_address = null) {
        $query = "INSERT INTO log_acciones (usuario_id, accion, entidad, entidad_id, detalles, ip_address) 
                  VALUES (:usuario_id, :accion, :entidad, :entidad_id, :detalles, :ip_address)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':accion' => $accion,
            ':entidad' => $entidad,
            ':entidad_id' => $entidad_id,
            ':detalles' => $detalles,
            ':ip_address' => $ip_address ?? $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }

    /**
     * Obtener todos los logs con información del usuario
     * @param int|null $limite Límite de resultados
     * @return array Lista de logs
     */
    public function obtenerTodos($limite = null) {
        $query = "SELECT l.*, u.nombre as usuario_nombre, u.email as usuario_email
                  FROM log_acciones l
                  INNER JOIN usuarios u ON l.usuario_id = u.id
                  ORDER BY l.fecha DESC";
        
        if ($limite !== null) {
            $query .= " LIMIT :limite";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($limite !== null) {
            $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener logs por usuario
     * @param int $usuario_id ID del usuario
     * @param int|null $limite Límite de resultados
     * @return array Lista de logs
     */
    public function obtenerPorUsuario($usuario_id, $limite = null) {
        $query = "SELECT l.*, u.nombre as usuario_nombre
                  FROM log_acciones l
                  INNER JOIN usuarios u ON l.usuario_id = u.id
                  WHERE l.usuario_id = :usuario_id
                  ORDER BY l.fecha DESC";
        
        if ($limite !== null) {
            $query .= " LIMIT :limite";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        if ($limite !== null) {
            $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener logs por entidad
     * @param string $entidad Tipo de entidad
     * @param int|null $entidad_id ID de la entidad
     * @return array Lista de logs
     */
    public function obtenerPorEntidad($entidad, $entidad_id = null) {
        $query = "SELECT l.*, u.nombre as usuario_nombre
                  FROM log_acciones l
                  INNER JOIN usuarios u ON l.usuario_id = u.id
                  WHERE l.entidad = :entidad";
        
        $params = [':entidad' => $entidad];
        
        if ($entidad_id !== null) {
            $query .= " AND l.entidad_id = :entidad_id";
            $params[':entidad_id'] = $entidad_id;
        }
        
        $query .= " ORDER BY l.fecha DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar total de logs
     * @return int Total de registros
     */
    public function contar() {
        $query = "SELECT COUNT(*) as total FROM log_acciones";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    /**
     * Limpiar logs antiguos (más de X días)
     * @param int $dias Días de antigüedad
     * @return int Número de registros eliminados
     */
    public function limpiarAntiguos($dias = 90) {
        $query = "DELETE FROM log_acciones WHERE fecha < DATE_SUB(NOW(), INTERVAL :dias DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':dias', (int) $dias, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
?>
