<?php
// Protección de rutas - Requerir autenticación y rol de administrador
require_once __DIR__ . '/../../../middleware/AdminMiddleware.php';
require_once __DIR__ . '/../../../business/EventoService.php';
require_once __DIR__ . '/../../../utils/MessageHandler.php';

// Verificar que el usuario sea administrador
AdminMiddleware::requerirAdmin();

$service = new EventoService();

// LÓGICA: CAMBIAR ESTADO
if (isset($_GET['action']) && $_GET['action'] == 'cambiar_estado' && isset($_GET['id']) && isset($_GET['estado'])) {
    $resultado = $service->cambiarEstado($_GET['id'], $_GET['estado']);
    if ($resultado['success']) {
        MessageHandler::setSuccess($resultado['mensaje']);
    } else {
        MessageHandler::setError($resultado['mensaje']);
    }
    header("Location: index.php?view=admin/eventos/index");
    exit;
}

// LÓGICA: ELIMINAR
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $res = $service->eliminarEvento($_GET['id']);
    if ($res) {
        MessageHandler::setSuccess('Evento eliminado correctamente.');
    } else {
        MessageHandler::setError('Error al eliminar el evento.');
    }
    header("Location: index.php?view=admin/eventos/index");
    exit;
}

// LÓGICA: LISTAR
$eventos = $service->listarEventos();

// Obtener mensaje flash si existe
$mensajeFlash = MessageHandler::getFlash();

$page = 'admin_eventos';
$title = 'Gestión de Eventos - Admin';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <link rel="stylesheet" href="../../../../public/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .admin-container {
            min-height: 100vh;
            background: #f5f7fa;
            padding: 30px 0;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table-card .card-body {
            padding: 0;
        }
        .table img {
            height: 50px;
            width: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .btn-action {
            margin: 2px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../templates/header.php'; ?>

    <div class="admin-container">
        <div class="container">
            <!-- Header de la página -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1><i class="fas fa-calendar-alt"></i> Gestión de Eventos</h1>
                        <p class="mb-0">Administra todos los eventos del sistema</p>
                    </div>
                    <a href="index.php?view=admin/eventos/crear" class="btn btn-light btn-lg">
                        <i class="fas fa-plus"></i> Nuevo Evento
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

            <!-- Tabla de eventos -->
            <div class="table-card">
                <div class="card-body">
                    <?php if (empty($eventos)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay eventos registrados.</p>
                            <a href="index.php?view=admin/eventos/crear" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Primer Evento
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Título</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Cupos</th>
                                        <th>Precio</th>
                                        <th>Destacado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($eventos as $ev): ?>
                                    <tr>
                                        <td>
                                            <img src="../../../../public/<?= htmlspecialchars($ev['imagen'] ?? '') ?>" 
                                                 alt="<?= htmlspecialchars($ev['titulo']) ?>" 
                                                 onerror="this.src='../../../../public/img/eventos/evento1.jpg'">
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($ev['titulo']) ?></strong>
                                            <?php if (!empty($ev['lugar'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($ev['lugar']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($ev['fecha_texto'] ?? 'N/A') ?>
                                            <?php if (!empty($ev['fecha_inicio'])): ?>
                                                <br><small class="text-muted"><?= date('d/m/Y', strtotime($ev['fecha_inicio'])) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $estado = $ev['estado'] ?? 'activo';
                                            $badgeClass = ($estado == 'activo') ? 'bg-success' : 'bg-secondary';
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($estado) ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                            $cupos_disponibles = $ev['cupos_disponibles'] ?? $ev['cupos'] ?? 0;
                                            $cupos = $ev['cupos'] ?? 0;
                                            $cupoClass = ($cupos_disponibles > 0) ? 'text-success' : 'text-danger';
                                            ?>
                                            <span class="<?= $cupoClass ?>">
                                                <strong><?= $cupos_disponibles ?></strong> / <?= $cupos ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong>$<?= number_format($ev['precio'], 2) ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($ev['destacado'] ?? 0): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-star"></i> Destacado
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="index.php?view=admin/eventos/crear&id=<?= $ev['id'] ?>" 
                                                   class="btn btn-sm btn-info text-white btn-action" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php 
                                                $nuevoEstado = ($estado == 'activo') ? 'inactivo' : 'activo';
                                                $iconoEstado = ($estado == 'activo') ? 'pause' : 'play';
                                                $colorEstado = ($estado == 'activo') ? 'warning' : 'success';
                                                ?>
                                                <a href="index.php?view=admin/eventos/index&action=cambiar_estado&id=<?= $ev['id'] ?>&estado=<?= $nuevoEstado ?>" 
                                                   class="btn btn-sm btn-<?= $colorEstado ?> btn-action" 
                                                   title="Cambiar a <?= $nuevoEstado ?>"
                                                   onclick="return confirm('¿Cambiar estado a <?= $nuevoEstado ?>?');">
                                                    <i class="fas fa-<?= $iconoEstado ?>"></i>
                                                </a>
                                                <a href="index.php?view=admin/eventos/index&action=delete&id=<?= $ev['id'] ?>" 
                                                   class="btn btn-sm btn-danger btn-action" 
                                                   title="Eliminar"
                                                   onclick="return confirm('¿Estás seguro de eliminar este evento? Esta acción no se puede deshacer.');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Botón volver -->
            <div class="mt-4">
                <a href="index.php?view=admin/dashboard" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
