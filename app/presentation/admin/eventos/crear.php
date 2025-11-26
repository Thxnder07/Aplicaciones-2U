<?php
// Protección de rutas - Requerir autenticación y rol de administrador
require_once __DIR__ . '/../../../middleware/AdminMiddleware.php';
require_once __DIR__ . '/../../../business/EventoService.php';
require_once __DIR__ . '/../../../utils/MessageHandler.php';

// Verificar que el usuario sea administrador
AdminMiddleware::requerirAdmin();

$service = new EventoService();
$errores = [];
$evento = null; // Datos vacíos por defecto
$esEdicion = false;

// 1. DETECTAR SI ESTAMOS EDITANDO (Viene un ID por GET)
if (isset($_GET['id'])) {
    $evento = $service->obtenerEvento($_GET['id']);
    if ($evento) {
        $esEdicion = true;
    } else {
        MessageHandler::setError('Evento no encontrado.');
        header("Location: index.php?view=admin/eventos/index");
        exit;
    }
}

// 2. PROCESAR FORMULARIO (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // ¿Es actualización o creación?
    if (!empty($_POST['id'])) {
        // ACTUALIZAR
        $res = $service->actualizarEvento($_POST['id'], $_POST, $_FILES);
    } else {
        // CREAR
        $res = $service->registrarEvento($_POST, $_FILES);
    }

    if ($res['success']) {
        MessageHandler::setSuccess($res['msg'] ?? 'Evento guardado correctamente.');
        header("Location: index.php?view=admin/eventos/index");
        exit;
    } else {
        $errores = $res['errores'] ?? ['Error desconocido al guardar el evento.'];
        // Si falló, mantenemos los datos que el usuario escribió para que no los pierda
        $evento = $_POST;
        $esEdicion = !empty($_POST['id']);
    }
}

// Obtener mensaje flash si existe
$mensajeFlash = MessageHandler::getFlash();

// Calcular base_url para rutas de recursos
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$base_url = $path . '/public/';

$page = 'admin_eventos';
$title = ($esEdicion ? 'Editar' : 'Nuevo') . ' Evento - Admin';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/styles.css">
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
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .form-label .required {
            color: #dc3545;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
                    <?= $esEdicion ? 'Editar Evento' : 'Nuevo Evento' ?>
                </h1>
                <p class="mb-0"><?= $esEdicion ? 'Modifica la información del evento' : 'Completa el formulario para crear un nuevo evento' ?></p>
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
                        <h5><i class="fas fa-exclamation-triangle"></i> Errores en el formulario:</h5>
                        <ul class="mb-0">
                            <?php foreach($errores as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($evento['id'] ?? '') ?>">
                    <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($evento['imagen'] ?? '') ?>">

                    <!-- Título -->
                    <div class="mb-4">
                        <label class="form-label">
                            Título del Evento <span class="required">*</span>
                        </label>
                        <input type="text" name="titulo" class="form-control form-control-lg" 
                               placeholder="Ej: Congreso de Tecnología Digital" 
                               required 
                               maxlength="150"
                               value="<?= htmlspecialchars($evento['titulo'] ?? '') ?>">
                        <small class="text-muted">Máximo 150 caracteres</small>
                    </div>

                    <!-- Fechas -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">
                                Fecha (Texto) <span class="required">*</span>
                            </label>
                            <input type="text" name="fecha_texto" class="form-control" 
                                   placeholder="Ej: 15-17 de marzo, 2025" 
                                   required
                                   value="<?= htmlspecialchars($evento['fecha_texto'] ?? '') ?>">
                            <small class="text-muted">Formato libre para mostrar</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Fecha de Inicio
                            </label>
                            <input type="date" name="fecha_inicio" class="form-control"
                                   value="<?= htmlspecialchars($evento['fecha_inicio'] ?? '') ?>">
                            <small class="text-muted">Fecha real del evento (opcional)</small>
                        </div>
                    </div>

                    <!-- Lugar y Horario -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">
                                Lugar <span class="required">*</span>
                            </label>
                            <input type="text" name="lugar" class="form-control" 
                                   placeholder="Ej: Centro de Convenciones Madrid" 
                                   required
                                   maxlength="150"
                                   value="<?= htmlspecialchars($evento['lugar'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Horario</label>
                            <input type="text" name="horario" class="form-control" 
                                   placeholder="Ej: 9:00 - 18:00"
                                   value="<?= htmlspecialchars($evento['horario'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label class="form-label">Descripción Breve</label>
                        <textarea name="descripcion_breve" class="form-control" rows="4"
                                  placeholder="Descripción breve del evento..."><?= htmlspecialchars($evento['descripcion_breve'] ?? '') ?></textarea>
                    </div>

                    <!-- Precio y Cupos -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">
                                Precio ($) <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="precio" step="0.01" class="form-control" 
                                       placeholder="0.00" 
                                       required
                                       min="0"
                                       value="<?= htmlspecialchars($evento['precio'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cupos Totales</label>
                            <input type="number" name="cupos" class="form-control" 
                                   placeholder="100" 
                                   min="1"
                                   value="<?= htmlspecialchars($evento['cupos'] ?? '100') ?>">
                            <small class="text-muted">Por defecto: 100</small>
                        </div>
                    </div>

                    <!-- Estado y Destacado -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="activo" <?= ($evento['estado'] ?? 'activo') == 'activo' ? 'selected' : '' ?>>
                                    Activo
                                </option>
                                <option value="inactivo" <?= ($evento['estado'] ?? 'activo') == 'inactivo' ? 'selected' : '' ?>>
                                    Inactivo
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Opciones</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="destacado" id="destacado" 
                                       value="1" <?= ($evento['destacado'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="destacado">
                                    <i class="fas fa-star text-warning"></i> Evento Destacado
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Imagen -->
                    <div class="mb-4">
                        <label class="form-label">
                            Imagen <?= $esEdicion ? '' : '<span class="required">*</span>' ?>
                        </label>
                        <input type="file" name="imagen" class="form-control" accept="image/*" <?= $esEdicion ? '' : 'required' ?>>
                        <small class="text-muted">Formatos permitidos: JPG, PNG, WEBP. Tamaño máximo: 2MB</small>
                        <?php if(!empty($evento['imagen'])): ?>
                            <div class="mt-3">
                                <p class="mb-2"><strong>Imagen actual:</strong></p>
                                <img src="<?php echo $base_url . htmlspecialchars($evento['imagen']); ?>" 
                                     alt="Imagen actual" 
                                     class="preview-image"
                                     onerror="this.style.display='none'">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botones -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php?view=admin/eventos/index" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> <?= $esEdicion ? 'Actualizar Evento' : 'Crear Evento' ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Botón volver -->
            <div class="mt-4">
                <a href="index.php?view=admin/eventos/index" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a la Lista
                </a>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
