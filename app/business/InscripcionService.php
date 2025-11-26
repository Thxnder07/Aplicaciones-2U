<?php
require_once __DIR__ . '/../data/InscripcionDAO.php';
require_once __DIR__ . '/../data/EventoDAO.php';
require_once __DIR__ . '/../data/CursoDAO.php';
require_once __DIR__ . '/LogService.php';

class InscripcionService {
    private $inscripcionDAO;
    private $eventoDAO;
    private $cursoDAO;
    private $logService;

    public function __construct() {
        $this->inscripcionDAO = new InscripcionDAO();
        $this->eventoDAO = new EventoDAO();
        $this->cursoDAO = new CursoDAO();
        $this->logService = new LogService();
    }

    /**
     * Inscribir usuario a un evento
     */
    public function inscribirAEvento($usuario_id, $evento_id) {
        // Validar que el evento exista
        $evento = $this->eventoDAO->obtenerPorId($evento_id);
        if (!$evento) {
            return ["success" => false, "mensaje" => "El evento no existe."];
        }

        // Validar que el evento esté activo
        if (($evento['estado'] ?? 'activo') !== 'activo') {
            return ["success" => false, "mensaje" => "El evento no está disponible para inscripciones."];
        }

        // Validar que haya cupos disponibles
        $cupos_disponibles = $evento['cupos_disponibles'] ?? $evento['cupos'] ?? 0;
        if ($cupos_disponibles <= 0) {
            return ["success" => false, "mensaje" => "No hay cupos disponibles para este evento."];
        }

        // Verificar si ya está inscrito
        if ($this->inscripcionDAO->verificarInscripcionEvento($usuario_id, $evento_id)) {
            return ["success" => false, "mensaje" => "Ya estás inscrito en este evento."];
        }

        // Crear la inscripción
        $creado = $this->inscripcionDAO->crear($usuario_id, $evento_id, null);

        if ($creado) {
            // Decrementar cupos disponibles del evento
            $this->eventoDAO->decrementarCupos($evento_id);

            // Registrar en log si hay usuario en sesión
            if (isset($_SESSION['usuario_id'])) {
                $this->logService->registrarAccion(
                    $_SESSION['usuario_id'],
                    'inscribirse_evento',
                    'inscripcion',
                    null,
                    "Se inscribió al evento: {$evento['titulo']}"
                );
            }

            return ["success" => true, "mensaje" => "Te has inscrito exitosamente al evento."];
        } else {
            return ["success" => false, "mensaje" => "Error al procesar la inscripción."];
        }
    }

    /**
     * Inscribir usuario a un curso
     */
    public function inscribirACurso($usuario_id, $evento_id, $curso_id) {
        // Validar que el curso exista
        $curso = $this->cursoDAO->obtenerPorId($curso_id);
        if (!$curso) {
            return ["success" => false, "mensaje" => "El curso no existe."];
        }

        // Validar que el curso pertenezca al evento
        if ($curso['evento_id'] != $evento_id) {
            return ["success" => false, "mensaje" => "El curso no pertenece a este evento."];
        }

        // Validar que haya cupos disponibles
        $cupos_disponibles = $curso['cupos_disponibles'] ?? $curso['cupos'] ?? 0;
        if ($cupos_disponibles <= 0) {
            return ["success" => false, "mensaje" => "No hay cupos disponibles para este curso."];
        }

        // Verificar si ya está inscrito al curso
        if ($this->inscripcionDAO->verificarInscripcionCurso($usuario_id, $evento_id, $curso_id)) {
            return ["success" => false, "mensaje" => "Ya estás inscrito en este curso."];
        }

        // Crear la inscripción
        $creado = $this->inscripcionDAO->crear($usuario_id, $evento_id, $curso_id);

        if ($creado) {
            // Decrementar cupos disponibles del curso
            $this->cursoDAO->decrementarCupos($curso_id);

            // Registrar en log si hay usuario en sesión
            if (isset($_SESSION['usuario_id'])) {
                $this->logService->registrarAccion(
                    $_SESSION['usuario_id'],
                    'inscribirse_curso',
                    'inscripcion',
                    null,
                    "Se inscribió al curso: {$curso['nombre']}"
                );
            }

            return ["success" => true, "mensaje" => "Te has inscrito exitosamente al curso."];
        } else {
            return ["success" => false, "mensaje" => "Error al procesar la inscripción."];
        }
    }

    /**
     * Obtener inscripciones de un usuario
     */
    public function obtenerInscripcionesUsuario($usuario_id) {
        return $this->inscripcionDAO->obtenerPorUsuario($usuario_id);
    }

    /**
     * Verificar si un usuario está inscrito a un evento
     */
    public function estaInscritoEnEvento($usuario_id, $evento_id) {
        return $this->inscripcionDAO->verificarInscripcionEvento($usuario_id, $evento_id);
    }

    /**
     * Verificar si un usuario está inscrito a un curso
     */
    public function estaInscritoEnCurso($usuario_id, $evento_id, $curso_id) {
        return $this->inscripcionDAO->verificarInscripcionCurso($usuario_id, $evento_id, $curso_id);
    }

    /**
     * Cancelar inscripción
     */
    public function cancelarInscripcion($inscripcion_id, $usuario_id) {
        // Verificar que la inscripción pertenezca al usuario
        $inscripcion = $this->inscripcionDAO->obtenerPorId($inscripcion_id);
        
        if (!$inscripcion) {
            return ["success" => false, "mensaje" => "Inscripción no encontrada."];
        }

        if ($inscripcion['usuario_id'] != $usuario_id) {
            return ["success" => false, "mensaje" => "No tienes permiso para cancelar esta inscripción."];
        }

        // Cancelar la inscripción
        $cancelado = $this->inscripcionDAO->cancelar($inscripcion_id);

        if ($cancelado) {
            // Incrementar cupos disponibles
            if ($inscripcion['curso_id']) {
                $this->cursoDAO->incrementarCupos($inscripcion['curso_id']);
            } else {
                // Incrementar cupos del evento
                $this->eventoDAO->incrementarCupos($inscripcion['evento_id']);
            }

            // Registrar en log
            if (isset($_SESSION['usuario_id'])) {
                $this->logService->registrarAccion(
                    $_SESSION['usuario_id'],
                    'cancelar_inscripcion',
                    'inscripcion',
                    $inscripcion_id,
                    "Canceló su inscripción"
                );
            }

            return ["success" => true, "mensaje" => "Inscripción cancelada exitosamente."];
        } else {
            return ["success" => false, "mensaje" => "Error al cancelar la inscripción."];
        }
    }
}
?>

