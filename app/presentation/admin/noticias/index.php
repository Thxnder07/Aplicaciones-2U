<?php
// Protección de rutas - Requerir autenticación y rol de administrador
require_once __DIR__ . '/../../../middleware/AdminMiddleware.php';
require_once __DIR__ . '/../../../business/NoticiaService.php';
require_once __DIR__ . '/../../../utils/MessageHandler.php';

// Verificar que el usuario sea administrador
AdminMiddleware::requerirAdmin();

$service = new NoticiaService();

// Lógica para eliminar si se recibe un ID
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $resultado = $service->eliminarNoticia($_GET['id']);
    if ($resultado) {
        MessageHandler::setSuccess('Noticia eliminada correctamente.');
    } else {
        MessageHandler::setError('Error al eliminar la noticia.');
    }
    header("Location: index.php?view=admin/noticias/index");
    exit;
}

$noticias = $service->listarNoticias();
$mensajeFlash = MessageHandler::getFlash();

$page = 'admin_noticias';
$title = 'Gestión de Noticias - Admin';
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
                        <h1><i class="fas fa-newspaper"></i> Gestión de Noticias</h1>
                        <p class="mb-0">Administra todas las noticias del sistema</p>
                    </div>
                    <a href="index.php?view=admin/noticias/crear" class="btn btn-light btn-lg">
                        <i class="fas fa-plus"></i> Nueva Noticia
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

            <!-- Tabla de noticias -->
            <div class="table-card">
                <div class="card-body p-0">
                    <?php if (empty($noticias)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay noticias registradas.</p>
                            <a href="index.php?view=admin/noticias/crear" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Primera Noticia
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Título</th>
                                        <th>Destacada</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($noticias as $n): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($n['fecha_publicacion'] ?? 'N/A') ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($n['titulo']) ?></strong>
                                            <?php if (!empty($n['resumen'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars(substr($n['resumen'], 0, 100)) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($n['es_destacada'] ?? 0): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-star"></i> Destacada
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="index.php?view=admin/noticias/crear&id=<?= $n['id'] ?>" 
                                                   class="btn btn-sm btn-info text-white" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="index.php?view=admin/noticias/index&action=delete&id=<?= $n['id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   title="Eliminar"
                                                   onclick="return confirm('¿Estás seguro de eliminar esta noticia? Esta acción no se puede deshacer.');">
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
