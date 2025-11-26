<?php
require_once __DIR__ . '/../data/EventoDAO.php';
require_once __DIR__ . '/../data/UsuarioDAO.php';
require_once __DIR__ . '/../data/InscripcionDAO.php';
require_once __DIR__ . '/../data/CursoDAO.php';

class DashboardService {
    private $eventoDAO;
    private $usuarioDAO;
    private $inscripcionDAO;
    private $cursoDAO;

    public function __construct() {
        $this->eventoDAO = new EventoDAO();
        $this->usuarioDAO = new UsuarioDAO();
        $this->inscripcionDAO = new InscripcionDAO();
        $this->cursoDAO = new CursoDAO();
    }

    /**
     * Obtener todas las estadísticas para el dashboard
     * @return array Estadísticas completas
     */
    public function obtenerEstadisticas() {
        $totalEventos = $this->contarEventos();
        $totalUsuarios = $this->usuarioDAO->contarUsuarios();
        $totalInscripciones = $this->inscripcionDAO->contarTotal();
        $totalCursos = $this->contarCursos();

        // Eventos activos e inactivos
        $eventosActivos = $this->contarEventosActivos();
        $eventosInactivos = $totalEventos - $eventosActivos;

        // Inscripciones por evento (top 5)
        $inscripcionesPorEvento = $this->obtenerInscripcionesPorEvento(null, 5);

        return [
            'total_eventos' => $totalEventos,
            'eventos_activos' => $eventosActivos,
            'eventos_inactivos' => $eventosInactivos,
            'total_usuarios' => $totalUsuarios,
            'total_inscripciones' => $totalInscripciones,
            'total_cursos' => $totalCursos,
            'inscripciones_por_evento' => $inscripcionesPorEvento
        ];
    }

    /**
     * Obtener inscripciones por evento
     * @param int|null $evento_id Si es null, retorna todos
     * @param int|null $limite Limite de resultados
     * @return array Lista de inscripciones con detalles
     */
    public function obtenerInscripcionesPorEvento($evento_id = null, $limite = null) {
        if ($evento_id !== null) {
            return $this->inscripcionDAO->obtenerPorEvento($evento_id);
        }

        // Si no se especifica evento, obtener todos y agrupar por evento
        $todasInscripciones = $this->inscripcionDAO->obtenerTodas();
        $agrupadas = [];

        foreach ($todasInscripciones as $inscripcion) {
            $eventoId = $inscripcion['evento_id'];
            if (!isset($agrupadas[$eventoId])) {
                $agrupadas[$eventoId] = [
                    'evento_id' => $eventoId,
                    'evento_titulo' => $inscripcion['evento_titulo'],
                    'total' => 0,
                    'inscripciones' => []
                ];
            }
            $agrupadas[$eventoId]['total']++;
            if ($limite === null || count($agrupadas[$eventoId]['inscripciones']) < $limite) {
                $agrupadas[$eventoId]['inscripciones'][] = $inscripcion;
            }
        }

        // Ordenar por total descendente
        usort($agrupadas, function($a, $b) {
            return $b['total'] - $a['total'];
        });

        return array_values($agrupadas);
    }

    /**
     * Obtener inscripciones por curso
     * @param int|null $curso_id Si es null, retorna todos agrupados
     * @return array Lista de inscripciones
     */
    public function obtenerInscripcionesPorCurso($curso_id = null) {
        if ($curso_id !== null) {
            return $this->inscripcionDAO->obtenerPorCurso($curso_id);
        }

        // Retornar todas las inscripciones a cursos
        $todasInscripciones = $this->inscripcionDAO->obtenerTodas();
        return array_filter($todasInscripciones, function($inscripcion) {
            return !empty($inscripcion['curso_id']);
        });
    }

    /**
     * Contar total de eventos
     * @return int
     */
    private function contarEventos() {
        $eventos = $this->eventoDAO->obtenerTodos();
        return count($eventos);
    }

    /**
     * Contar eventos activos
     * @return int
     */
    private function contarEventosActivos() {
        $eventos = $this->eventoDAO->obtenerTodos();
        $activos = array_filter($eventos, function($evento) {
            return isset($evento['estado']) && $evento['estado'] === 'activo';
        });
        return count($activos);
    }

    /**
     * Contar total de cursos
     * @return int
     */
    private function contarCursos() {
        $cursos = $this->cursoDAO->obtenerTodos();
        return count($cursos);
    }

    /**
     * Obtener eventos con más inscripciones
     * @param int $limite Número de eventos a retornar
     * @return array Lista de eventos con conteo de inscripciones
     */
    public function obtenerEventosMasPopulares($limite = 5) {
        $eventos = $this->eventoDAO->obtenerTodos();
        $eventosConInscripciones = [];

        foreach ($eventos as $evento) {
            $eventosConInscripciones[] = [
                'evento' => $evento,
                'total_inscripciones' => $this->inscripcionDAO->contarPorEvento($evento['id'])
            ];
        }

        // Ordenar por número de inscripciones descendente
        usort($eventosConInscripciones, function($a, $b) {
            return $b['total_inscripciones'] - $a['total_inscripciones'];
        });

        return array_slice($eventosConInscripciones, 0, $limite);
    }
}
?>
