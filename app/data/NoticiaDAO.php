<?php
require_once 'Database.php';

class NoticiaDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // ====================================================================
    // 1. MÉTODOS DE LECTURA (READ)
    // ====================================================================

    /**
     * Obtener todas las noticias
     */
    public function getAll() {
        $query = "SELECT * FROM noticias ORDER BY fecha_publicacion DESC, id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una noticia por ID
     */
    public function getById($id) {
        $query = "SELECT * FROM noticias WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener noticia destacada
     */
    public function obtenerDestacada() {
        $query = "SELECT * FROM noticias WHERE es_destacada = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener noticias recientes (no destacadas)
     */
    public function obtenerRecientes() {
        $query = "SELECT * FROM noticias WHERE es_destacada = 0 ORDER BY fecha_publicacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ====================================================================
    // 2. MÉTODOS DE ESCRITURA (CREATE, UPDATE, DELETE)
    // ====================================================================

    /**
     * Crear nueva noticia
     */
    public function insert($titulo, $resumen, $contenido, $rutaImagen = null, $es_destacada = 0) {
        $query = "INSERT INTO noticias (titulo, resumen, contenido_completo, imagen, es_destacada, fecha_publicacion) 
                  VALUES (:titulo, :resumen, :contenido, :imagen, :es_destacada, CURDATE())";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':titulo' => $titulo,
            ':resumen' => $resumen,
            ':contenido' => $contenido,
            ':imagen' => $rutaImagen,
            ':es_destacada' => $es_destacada ? 1 : 0
        ]);
    }

    /**
     * Actualizar noticia existente
     */
    public function update($id, $titulo, $resumen, $contenido, $rutaImagen = null, $es_destacada = null) {
        $query = "UPDATE noticias SET 
                    titulo = :titulo, 
                    resumen = :resumen, 
                    contenido_completo = :contenido";
        
        $params = [
            ':id' => $id,
            ':titulo' => $titulo,
            ':resumen' => $resumen,
            ':contenido' => $contenido
        ];
        
        if ($rutaImagen !== null) {
            $query .= ", imagen = :imagen";
            $params[':imagen'] = $rutaImagen;
        }
        
        if ($es_destacada !== null) {
            $query .= ", es_destacada = :es_destacada";
            $params[':es_destacada'] = $es_destacada ? 1 : 0;
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Eliminar noticia
     */
    public function delete($id) {
        $query = "DELETE FROM noticias WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>