<?php
// Protección de rutas - Requerir autenticación y rol de administrador
require_once __DIR__ . '/../../../middleware/AdminMiddleware.php';
require_once __DIR__ . '/../../../business/LogService.php';
require_once __DIR__ . '/../../../utils/MessageHandler.php';

// Verificar que el usuario sea administrador
AdminMiddleware::requerirAdmin();

$logService = new LogService();
$mensajeFlash = MessageHandler::getFlash();

// Obtener filtros
$filtro_usuario = isset($_GET['usuario_id']) ? (int) $_GET['usuario_id'] : null;
$filtro_entidad = isset($_GET['entidad']) ? $_GET['entidad'] : null;
$limite = isset($_GET['limite']) ? (int) $_GET['limite'] : 100;

// Obtener logs según filtros
if ($filtro_usuario) {
    $logs = $logService->obtenerLogsPorUsuario($filtro_usuario, $limite);
} elseif ($filtro_entidad) {
    $logs = $logService->obtenerLogsPorEntidad($filtro_entidad);
} else {
    $logs = $logService->obtenerLogs($limite);
}

$page = 'admin_log';
$title = 'Log de Acciones - Admin';
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
    
    <style>
        .log-table {
            font-size: 0.9rem;
        }
        .log-badge {
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../templates/header.php'; ?>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-history"></i> Log de Acciones</h1>
                <p class="text-muted mb-0">Registro de todas las acciones administrativas</p>
            </div>
            <div>
                <a href="../../../../index.php?view=admin/dashboard" class="btn btn-secondary">
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
                    <div class="col-md-4">
                        <label class="form-label">Filtrar por Entidad</label>
                        <select name="entidad" class="form-select">
                            <option value="">Todas las entidades</option>
                            <option value="evento" <?php echo ($filtro_entidad == 'evento') ? 'selected' : ''; ?>>Eventos</option>
                            <option value="curso" <?php echo ($filtro_entidad == 'curso') ? 'selected' : ''; ?>>Cursos</option>
                            <option value="inscripcion" <?php echo ($filtro_entidad == 'inscripcion') ? 'selected' : ''; ?>>Inscripciones</option>
                            <option value="usuario" <?php echo ($filtro_entidad == 'usuario') ? 'selected' : ''; ?>>Usuarios</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Límite de Registros</label>
                        <select name="limite" class="form-select">
                            <option value="50" <?php echo ($limite == 50) ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo ($limite == 100) ? 'selected' : ''; ?>>100</option>
                            <option value="200" <?php echo ($limite == 200) ? 'selected' : ''; ?>>200</option>
                            <option value="500" <?php echo ($limite == 500) ? 'selected' : ''; ?>>500</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="index.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo"></i> Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de logs -->
        <div class="card shadow">
            <div class="card-body">
                <?php if (empty($logs)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay registros de acciones disponibles.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover log-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Entidad</th>
                                    <th>ID Entidad</th>
                                    <th>Detalles</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($logs as $log): ?>
                                <tr>
                                    <td>
                                        <small><?php echo date('d/m/Y H:i:s', strtotime($log['fecha'])); ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($log['usuario_nombre'] ?? 'N/A'); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($log['usuario_email'] ?? ''); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary log-badge">
                                            <?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($log['accion']))); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info log-badge">
                                            <?php echo ucfirst($log['entidad']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($log['entidad_id']): ?>
                                            <code>#<?php echo $log['entidad_id']; ?></code>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($log['detalles'] ?? '-'); ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo htmlspecialchars($log['ip_address'] ?? '-'); ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            Mostrando <?php echo count($logs); ?> registro(s)
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include __DIR__ . '/../../templates/footer.php'; ?>
</body>
</html>
