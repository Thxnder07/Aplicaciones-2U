<?php
require_once __DIR__ . '/../data/LogDAO.php';

class LogService {
    private $logDAO;

    public function __construct() {
        $this->logDAO = new LogDAO();
    }

    /**
     * Registrar una acción en el log
     * @param int $usuario_id ID del usuario
     * @param string $accion Tipo de acción
     * @param string $entidad Tipo de entidad
     * @param int|null $entidad_id ID de la entidad
     * @param string|null $detalles Detalles adicionales
     * @return bool true si se registró correctamente
     */
    public function registrarAccion($usuario_id, $accion, $entidad, $entidad_id = null, $detalles = null) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        return $this->logDAO->registrar($usuario_id, $accion, $entidad, $entidad_id, $detalles, $ip_address);
    }

    /**
     * Registrar creación de evento
     */
    public function logCrearEvento($usuario_id, $evento_id, $titulo) {
        return $this->registrarAccion(
            $usuario_id,
            'crear_evento',
            'evento',
            $evento_id,
            "Creó el evento: $titulo"
        );
    }

    /**
     * Registrar actualización de evento
     */
    public function logActualizarEvento($usuario_id, $evento_id, $titulo) {
        return $this->registrarAccion(
            $usuario_id,
            'editar_evento',
            'evento',
            $evento_id,
            "Editó el evento: $titulo"
        );
    }

    /**
     * Registrar eliminación de evento
     */
    public function logEliminarEvento($usuario_id, $evento_id, $titulo) {
        return $this->registrarAccion(
            $usuario_id,
            'eliminar_evento',
            'evento',
            $evento_id,
            "Eliminó el evento: $titulo"
        );
    }

    /**
     * Registrar creación de curso
     */
    public function logCrearCurso($usuario_id, $curso_id, $nombre) {
        return $this->registrarAccion(
            $usuario_id,
            'crear_curso',
            'curso',
            $curso_id,
            "Creó el curso: $nombre"
        );
    }

    /**
     * Registrar actualización de curso
     */
    public function logActualizarCurso($usuario_id, $curso_id, $nombre) {
        return $this->registrarAccion(
            $usuario_id,
            'editar_curso',
            'curso',
            $curso_id,
            "Editó el curso: $nombre"
        );
    }

    /**
     * Registrar eliminación de curso
     */
    public function logEliminarCurso($usuario_id, $curso_id, $nombre) {
        return $this->registrarAccion(
            $usuario_id,
            'eliminar_curso',
            'curso',
            $curso_id,
            "Eliminó el curso: $nombre"
        );
    }

    /**
     * Obtener todos los logs
     * @param int|null $limite Límite de resultados
     * @return array Lista de logs
     */
    public function obtenerLogs($limite = 100) {
        return $this->logDAO->obtenerTodos($limite);
    }

    /**
     * Obtener logs por usuario
     */
    public function obtenerLogsPorUsuario($usuario_id, $limite = 50) {
        return $this->logDAO->obtenerPorUsuario($usuario_id, $limite);
    }

    /**
     * Obtener logs por entidad
     */
    public function obtenerLogsPorEntidad($entidad, $entidad_id = null) {
        return $this->logDAO->obtenerPorEntidad($entidad, $entidad_id);
    }
}
?>
