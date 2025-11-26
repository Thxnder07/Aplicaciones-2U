<?php
// Protección de rutas - Requerir autenticación y rol de administrador
require_once __DIR__ . '/../../../middleware/AdminMiddleware.php';
require_once __DIR__ . '/../../../business/CursoService.php';
require_once __DIR__ . '/../../../business/EventoService.php';
require_once __DIR__ . '/../../../utils/MessageHandler.php';

// Verificar que el usuario sea administrador
AdminMiddleware::requerirAdmin();

$cursoService = new CursoService();
$eventoService = new EventoService();
$mensajeFlash = MessageHandler::getFlash();

// Obtener filtro de evento si existe
$evento_id = isset($_GET['evento_id']) ? (int) $_GET['evento_id'] : null;

// LÓGICA: ELIMINAR
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $resultado = $cursoService->eliminarCurso($_GET['id']);
    if ($resultado['success']) {
        MessageHandler::setSuccess($resultado['msg']);
        $redirect = $evento_id ? "?evento_id=$evento_id" : "";
        header("Location: index.php$redirect");
        exit;
    } else {
        MessageHandler::setError(implode(' ', $resultado['errores']));
    }
}

// LÓGICA: LISTAR
if ($evento_id) {
    $cursos = $cursoService->obtenerCursosPorEvento($evento_id);
    $evento = $eventoService->obtenerEvento($evento_id);
} else {
    $cursos = $cursoService->listarCursos();
    $evento = null;
}

$eventos = $eventoService->listarEventos();

$page = 'admin_cursos';
$title = 'Gestión de Cursos - Admin';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>

    <link rel="stylesheet" href="public/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php include __DIR__ . '/../../templates/header.php'; ?>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-graduation-cap"></i> Gestión de Cursos</h1>
                <?php if ($evento): ?>
                    <p class="text-muted mb-0">Evento: <strong><?php echo htmlspecialchars($evento['titulo']); ?></strong>
                    </p>
                <?php endif; ?>
            </div>
            <div>
                <a href="index.php?view=admin/cursos/crear<?php echo $evento_id ? "&evento_id=$evento_id" : ""; ?>"
                    class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Curso
                </a>
                <a href="index.php?view=admin/dashboard" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Mensajes Flash -->
        <?php if ($mensajeFlash): ?>
            <div class="alert alert-<?php echo $mensajeFlash['tipo']; ?> alert-dismissible fade show" role="alert">
                <i
                    class="fas fa-<?php echo $mensajeFlash['tipo'] === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                <?php echo htmlspecialchars($mensajeFlash['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filtro por evento -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Filtrar por Evento</label>
                        <select name="evento_id" class="form-select">
                            <option value="">Todos los eventos</option>
                            <?php foreach ($eventos as $ev): ?>
                                <option value="<?php echo $ev['id']; ?>" <?php echo ($evento_id == $ev['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ev['titulo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de cursos -->
        <div class="card shadow">
            <div class="card-body">
                <?php if (empty($cursos)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay cursos registrados<?php echo $evento_id ? " para este evento" : ""; ?>.
                        </p>
                        <a href="index.php?view=admin/cursos/crear<?php echo $evento_id ? "&evento_id=$evento_id" : ""; ?>"
                            class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primer Curso
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Evento</th>
                                    <th>Nombre del Curso</th>
                                    <th>Fecha</th>
                                    <th>Horario</th>
                                    <th>Ponente</th>
                                    <th>Cupos</th>
                                    <th>Precio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cursos as $curso): ?>
                                    <tr>
                                        <td>
                                            <?php if (!$evento_id): ?>
                                                <small><?php echo htmlspecialchars($curso['evento_titulo'] ?? 'N/A'); ?></small>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Mismo evento</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($curso['nombre']); ?></strong>
                                            <?php if ($curso['descripcion']): ?>
                                                <br><small
                                                    class="text-muted"><?php echo htmlspecialchars(substr($curso['descripcion'], 0, 50)); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $curso['fecha'] ? date('d/m/Y', strtotime($curso['fecha'])) : 'N/A'; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($curso['horario'] ?: 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($curso['ponente_nombre'] ?: 'Sin asignar'); ?></td>
                                        <td>
                                            <span
                                                class="badge bg-<?php echo ($curso['cupos_disponibles'] > 0) ? 'success' : 'danger'; ?>">
                                                <?php echo $curso['cupos_disponibles']; ?> / <?php echo $curso['cupos']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($curso['precio']): ?>
                                                $<?php echo number_format($curso['precio'], 2); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Gratis</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="index.php?view=admin/cursos/crear&id=<?php echo $curso['id']; ?><?php echo $evento_id ? "&evento_id=$evento_id" : ""; ?>"
                                                class="btn btn-sm btn-info text-white">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="index.php?action=delete&id=<?php echo $curso['id']; ?><?php echo $evento_id ? "&evento_id=$evento_id" : ""; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Estás seguro de eliminar este curso?');">
                                                <i class="fas fa-trash"></i> Eliminar
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