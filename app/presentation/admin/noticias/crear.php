<?php
// Protección de rutas - Requerir autenticación y rol de administrador
require_once __DIR__ . '/../../../middleware/AdminMiddleware.php';
require_once __DIR__ . '/../../../business/NoticiaService.php';
require_once __DIR__ . '/../../../utils/MessageHandler.php';

// Verificar que el usuario sea administrador
AdminMiddleware::requerirAdmin(); 

$service = new NoticiaService();
$errores = [];
$noticia = null;
$esEdicion = false;

// Detectar si estamos editando
if (isset($_GET['id'])) {
    $noticia = $service->obtenerPorId($_GET['id']);
    if ($noticia) {
        $esEdicion = true;
    } else {
        MessageHandler::setError('Noticia no encontrada.');
        header("Location: index.php?view=admin/noticias/index");
        exit;
    }
}

// Si es POST, estamos guardando
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $res = $service->guardarNoticia($_POST, $_FILES['imagen'] ?? null);
    if ($res) {
        MessageHandler::setSuccess('Noticia guardada correctamente.');
        header("Location: index.php?view=admin/noticias/index");
        exit;
    } else {
        MessageHandler::setError('Error al guardar la noticia.');
        $errores = ['Error al guardar la noticia.'];
        $noticia = $_POST;
        $esEdicion = !empty($_POST['id']);
    }
}

$mensajeFlash = MessageHandler::getFlash();
$page = 'admin_noticias';
$title = ($esEdicion ? 'Editar' : 'Nueva') . ' Noticia - Admin';
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
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../templates/header.php'; ?>

    <div class="admin-container">
        <div class="container">
            <!-- Header de la página -->
            <div class="page-header">
                <h1>
                    <i class="fas fa-<?= $esEdicion ? 'edit' : 'plus-circle' ?>"></i> 
                    <?= $esEdicion ? 'Editar Noticia' : 'Nueva Noticia' ?>
                </h1>
                <p class="mb-0"><?= $esEdicion ? 'Modifica la información de la noticia' : 'Completa el formulario para publicar una nueva noticia' ?></p>
            </div>

            <!-- Mensajes Flash -->
            <?php if ($mensajeFlash): ?>
                <div class="alert alert-<?php echo $mensajeFlash['tipo']; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $mensajeFlash['tipo'] === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                    <?php echo htmlspecialchars($mensajeFlash['mensaje']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <div class="form-card">
                <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach($errores as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <?php if ($esEdicion && isset($noticia['id'])): ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($noticia['id']) ?>">
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control form-control-lg" 
                               required 
                               value="<?= htmlspecialchars($noticia['titulo'] ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Resumen <span class="text-danger">*</span></label>
                        <textarea name="resumen" class="form-control" rows="3" required><?= htmlspecialchars($noticia['resumen'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Contenido Completo <span class="text-danger">*</span></label>
                        <textarea name="contenido" class="form-control" rows="8" required><?= htmlspecialchars($noticia['contenido_completo'] ?? '') ?></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Imagen</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*" <?= $esEdicion ? '' : 'required' ?>>
                            <small class="text-muted">Formatos permitidos: JPG, PNG, WEBP</small>
                            <?php if (!empty($noticia['imagen'])): ?>
                                <div class="mt-2">
                                    <p class="mb-1"><strong>Imagen actual:</strong></p>
                                    <img src="../../../../public/<?= htmlspecialchars($noticia['imagen']) ?>" 
                                         alt="Imagen actual" 
                                         style="max-width: 200px; border-radius: 8px;"
                                         onerror="this.style.display='none'">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Opciones</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="es_destacada" id="es_destacada" 
                                       value="1" <?= ($noticia['es_destacada'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="es_destacada">
                                    <i class="fas fa-star text-warning"></i> Noticia Destacada
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php?view=admin/noticias/index" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> <?= $esEdicion ? 'Actualizar Noticia' : 'Publicar Noticia' ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Botón volver -->
            <div class="mt-4">
                <a href="index.php?view=admin/noticias/index" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a la Lista
                </a>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
