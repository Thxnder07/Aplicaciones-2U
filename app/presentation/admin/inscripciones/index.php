<?php
// Protección de rutas - Requerir autenticación y rol de administrador
require_once __DIR__ . '/../../../middleware/AdminMiddleware.php';
require_once __DIR__ . '/../../../business/DashboardService.php';
require_once __DIR__ . '/../../../business/EventoService.php';
require_once __DIR__ . '/../../../data/InscripcionDAO.php';
require_once __DIR__ . '/../../../utils/MessageHandler.php';

// Verificar que el usuario sea administrador
AdminMiddleware::requerirAdmin();

$dashboardService = new DashboardService();
$eventoService = new EventoService();
$inscripcionDAO = new InscripcionDAO();
$mensajeFlash = MessageHandler::getFlash();

// Obtener filtros
$evento_id = isset($_GET['evento_id']) ? (int) $_GET['evento_id'] : null;
$curso_id = isset($_GET['curso_id']) ? (int) $_GET['curso_id'] : null;
$tipo_filtro = isset($_GET['tipo']) ? $_GET['tipo'] : 'todas';

// LÓGICA: LISTAR
if ($evento_id && !$curso_id) {
    // Filtrar por evento
    $inscripciones = $dashboardService->obtenerInscripcionesPorEvento($evento_id);
    $titulo_filtro = "Evento: " . ($eventoService->obtenerEvento($evento_id)['titulo'] ?? '');
} elseif ($curso_id) {
    // Filtrar por curso
    require_once __DIR__ . '/../../../business/CursoService.php';
    $cursoService = new CursoService();
    $curso = $cursoService->obtenerCurso($curso_id);
    $inscripciones = $dashboardService->obtenerInscripcionesPorCurso($curso_id);
    $titulo_filtro = "Curso: " . ($curso['nombre'] ?? '');
} else {
    // Todas las inscripciones
    $inscripciones = $inscripcionDAO->obtenerTodas();
    $titulo_filtro = "Todas las Inscripciones";
}

// Obtener listas para filtros
$eventos = $eventoService->listarEventos();

$page = 'admin_inscripciones';
$title = 'Gestión de Inscripciones - Admin';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>

    <link rel="stylesheet" href="../../../../public/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php include __DIR__ . '/../../templates/header.php'; ?>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-list-check"></i> Gestión de Inscripciones</h1>
                <?php if ($evento_id || $curso_id): ?>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($titulo_filtro); ?></p>
                <?php endif; ?>
            </div>
            <div>
                <a href="index.php?view=admin/dashboard" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Mensajes Flash -->
        <?php if ($mensajeFlash): ?>
            <div class="alert alert-<?php echo $mensajeFlash['tipo']; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $mensajeFlash['tipo'] === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                <?php echo htmlspecialchars($mensajeFlash['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Filtrar por Evento</label>
                        <select name="evento_id" class="form-select" id="select_evento">
                            <option value="">Todos los eventos</option>
                            <?php foreach ($eventos as $ev): ?>
                                <option value="<?php echo $ev['id']; ?>" <?php echo ($evento_id == $ev['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ev['titulo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Tipo de Vista</label>
                        <select name="tipo" class="form-select">
                            <option value="todas" <?php echo ($tipo_filtro == 'todas') ? 'selected' : ''; ?>>Todas las inscripciones</option>
                            <option value="solo_evento" <?php echo ($tipo_filtro == 'solo_evento') ? 'selected' : ''; ?>>Solo inscripciones a eventos</option>
                            <option value="con_curso" <?php echo ($tipo_filtro == 'con_curso') ? 'selected' : ''; ?>>Solo inscripciones con curso</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary"><?php echo count($inscripciones); ?></h3>
                        <p class="text-muted mb-0">Total Inscripciones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success">
                            <?php
                            $confirmadas = array_filter($inscripciones, function ($i) {
                                return $i['estado'] == 'confirmada';
                            });
                            echo count($confirmadas);
                            ?>
                        </h3>
                        <p class="text-muted mb-0">Confirmadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-warning">
                            <?php
                            $canceladas = array_filter($inscripciones, function ($i) {
                                return $i['estado'] == 'cancelada';
                            });
                            echo count($canceladas);
                            ?>
                        </h3>
                        <p class="text-muted mb-0">Canceladas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-info">
                            <?php
                            $conCurso = array_filter($inscripciones, function ($i) {
                                return !empty($i['curso_id']);
                            });
                            echo count($conCurso);
                            ?>
                        </h3>
                        <p class="text-muted mb-0">Con Curso</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de inscripciones -->
        <div class="card shadow">
            <div class="card-body">
                <?php if (empty($inscripciones)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay inscripciones registradas<?php echo ($evento_id || $curso_id) ? " con los filtros aplicados" : ""; ?>.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Evento</th>
                                    <th>Curso</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inscripciones as $inscripcion): ?>
                                    <?php
                                    // Aplicar filtro de tipo si está seleccionado
                                    if ($tipo_filtro == 'solo_evento' && !empty($inscripcion['curso_id'])) continue;
                                    if ($tipo_filtro == 'con_curso' && empty($inscripcion['curso_id'])) continue;
                                    ?>
                                    <tr>
                                        <td>
                                            <small><?php echo date('d/m/Y H:i', strtotime($inscripcion['fecha_inscripcion'])); ?></small>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($inscripcion['usuario_nombre'] ?? 'N/A'); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($inscripcion['usuario_email'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($inscripcion['evento_titulo'] ?? 'N/A'); ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($inscripcion['curso_id'])): ?>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($inscripcion['curso_nombre'] ?? 'N/A'); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Solo evento</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo ($inscripcion['estado'] == 'confirmada') ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($inscripcion['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="detalle.php?id=<?php echo $inscripcion['id']; ?>"
                                                class="btn btn-sm btn-info text-white">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include __DIR__ . '/../../templates/footer.php'; ?>
</body>

</html>