<?php
require_once __DIR__ . '/../data/CursoDAO.php';
require_once __DIR__ . '/../data/EventoDAO.php';

class CursoService {
    private $cursoDAO;
    private $eventoDAO;

    public function __construct() {
        $this->cursoDAO = new CursoDAO();
        $this->eventoDAO = new EventoDAO();
    }

    // ==========================================================
    // 1. LÓGICA DE LECTURA (Listar y Ver Detalle)
    // ==========================================================

    /**
     * Listar todos los cursos
     */
    public function listarCursos() {
        return $this->cursoDAO->obtenerTodos();
    }

    /**
     * Obtener un curso por ID
     */
    public function obtenerCurso($id) {
        if (!is_numeric($id)) {
            return null;
        }
        return $this->cursoDAO->obtenerPorId($id);
    }

    /**
     * Obtener cursos por evento
     */
    public function obtenerCursosPorEvento($evento_id) {
        if (!is_numeric($evento_id)) {
            return [];
        }
        return $this->cursoDAO->obtenerPorEvento($evento_id);
    }

    // ==========================================================
    // 2. LÓGICA DE ESCRITURA (Validaciones + CRUD)
    // ==========================================================

    /**
     * Crear nuevo curso
     */
    public function crearCurso($datos) {
        // Validación de datos
        $errores = $this->validarDatos($datos);

        // Validar que el evento exista
        if (!empty($datos['evento_id'])) {
            $evento = $this->eventoDAO->obtenerPorId($datos['evento_id']);
            if (!$evento) {
                $errores[] = "El evento seleccionado no existe.";
            }
        }

        if (!empty($errores)) {
            return ["success" => false, "errores" => $errores];
        }

        // Crear curso
        $creado = $this->cursoDAO->crear(
            (int) $datos['evento_id'],
            trim($datos['nombre'] ?? ''),
            trim($datos['descripcion'] ?? ''),
            $datos['fecha'] ?? null,
            trim($datos['horario'] ?? ''),
            !empty($datos['ponente_id']) ? (int) $datos['ponente_id'] : null,
            !empty($datos['cupos']) ? (int) $datos['cupos'] : 50,
            !empty($datos['precio']) ? (float) $datos['precio'] : null
        );

        if ($creado) {
            return ["success" => true, "msg" => "Curso creado exitosamente."];
        } else {
            return ["success" => false, "errores" => ["Error al crear el curso en la base de datos."]];
        }
    }

    /**
     * Actualizar curso existente
     */
    public function actualizarCurso($id, $datos) {
        // Validar que el curso exista
        $curso = $this->cursoDAO->obtenerPorId($id);
        if (!$curso) {
            return ["success" => false, "errores" => ["El curso no existe."]];
        }

        // Validación de datos
        $errores = $this->validarDatos($datos);

        // Validar cupos disponibles (no puede ser menor que las inscripciones existentes)
        if (!empty($datos['cupos'])) {
            require_once __DIR__ . '/../data/InscripcionDAO.php';
            $inscripcionDAO = new InscripcionDAO();
            $inscritos = $inscripcionDAO->contarPorCurso($id);
            if ((int) $datos['cupos'] < $inscritos) {
                $errores[] = "No se puede reducir los cupos a menos de $inscritos (ya hay $inscritos inscritos).";
            }
        }

        if (!empty($errores)) {
            return ["success" => false, "errores" => $errores];
        }

        // Actualizar curso
        $actualizado = $this->cursoDAO->actualizar(
            $id,
            trim($datos['nombre'] ?? ''),
            trim($datos['descripcion'] ?? ''),
            $datos['fecha'] ?? null,
            trim($datos['horario'] ?? ''),
            !empty($datos['ponente_id']) ? (int) $datos['ponente_id'] : null,
            !empty($datos['cupos']) ? (int) $datos['cupos'] : $curso['cupos'],
            !empty($datos['precio']) ? (float) $datos['precio'] : null
        );

        // Actualizar cupos_disponibles si cambió cupos
        if ($actualizado && !empty($datos['cupos'])) {
            $nuevoCupo = (int) $datos['cupos'];
            $diferencia = $nuevoCupo - $curso['cupos'];
            if ($diferencia > 0) {
                // Se aumentaron los cupos, aumentar disponibles también
                for ($i = 0; $i < $diferencia; $i++) {
                    $this->cursoDAO->incrementarCupos($id);
                }
            }
        }

        if ($actualizado) {
            return ["success" => true, "msg" => "Curso actualizado exitosamente."];
        } else {
            return ["success" => false, "errores" => ["Error al actualizar el curso."]];
        }
    }

    /**
     * Eliminar curso
     */
    public function eliminarCurso($id) {
        // Verificar que el curso exista
        $curso = $this->cursoDAO->obtenerPorId($id);
        if (!$curso) {
            return ["success" => false, "errores" => ["El curso no existe."]];
        }

        // Verificar si hay inscripciones
        require_once __DIR__ . '/../data/InscripcionDAO.php';
        $inscripcionDAO = new InscripcionDAO();
        $inscritos = $inscripcionDAO->contarPorCurso($id);
        
        if ($inscritos > 0) {
            return [
                "success" => false, 
                "errores" => ["No se puede eliminar el curso porque tiene $inscritos inscripciones activas."]
            ];
        }

        // Eliminar curso
        $eliminado = $this->cursoDAO->eliminar($id);

        if ($eliminado) {
            return ["success" => true, "msg" => "Curso eliminado exitosamente."];
        } else {
            return ["success" => false, "errores" => ["Error al eliminar el curso."]];
        }
    }

    // ==========================================================
    // 3. MÉTODOS PRIVADOS (Auxiliares de Lógica)
    // ==========================================================

    /**
     * Validar datos del curso
     */
    private function validarDatos($datos) {
        $errores = [];

        if (empty($datos['nombre']) || strlen(trim($datos['nombre'])) < 3) {
            $errores[] = "El nombre del curso es obligatorio y debe tener al menos 3 caracteres.";
        }

        if (empty($datos['evento_id']) || !is_numeric($datos['evento_id'])) {
            $errores[] = "Debe seleccionar un evento válido.";
        }

        if (!empty($datos['cupos']) && (!is_numeric($datos['cupos']) || (int) $datos['cupos'] <= 0)) {
            $errores[] = "Los cupos deben ser un número positivo.";
        }

        if (!empty($datos['precio']) && (!is_numeric($datos['precio']) || (float) $datos['precio'] < 0)) {
            $errores[] = "El precio debe ser un número positivo o cero.";
        }

        return $errores;
    }
}
?>
