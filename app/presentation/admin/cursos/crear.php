<?php
// Protección de rutas - Requerir autenticación y rol de administrador
require_once __DIR__ . '/../../../middleware/AdminMiddleware.php';
require_once __DIR__ . '/../../../business/CursoService.php';
require_once __DIR__ . '/../../../business/EventoService.php';
require_once __DIR__ . '/../../../data/PonenteDAO.php';
require_once __DIR__ . '/../../../utils/MessageHandler.php';

// Verificar que el usuario sea administrador
AdminMiddleware::requerirAdmin();

$cursoService = new CursoService();
$eventoService = new EventoService();
$ponenteDAO = new PonenteDAO();

$errores = [];
$curso = null;
$eventoPredefinido = null;

// Obtener evento predefinido si viene por GET
$evento_id_get = isset($_GET['evento_id']) ? (int) $_GET['evento_id'] : null;
if ($evento_id_get) {
    $eventoPredefinido = $eventoService->obtenerEvento($evento_id_get);
}

// 1. DETECTAR SI ESTAMOS EDITANDO (Viene un ID por GET)
if (isset($_GET['id'])) {
    $curso = $cursoService->obtenerCurso($_GET['id']);
    if (!$curso) {
        MessageHandler::setError('El curso no existe.');
        header("Location: index.php");
        exit;
    }
}

// 2. PROCESAR FORMULARIO (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ¿Es actualización o creación?
    if (!empty($_POST['id'])) {
        // ACTUALIZAR
        $resultado = $cursoService->actualizarCurso($_POST['id'], $_POST);
    } else {
        // CREAR
        $resultado = $cursoService->crearCurso($_POST);
    }

    if ($resultado['success']) {
        MessageHandler::setSuccess($resultado['msg']);
        $evento_id_redirect = $_POST['evento_id'] ?? $evento_id_get;
        $redirect = $evento_id_redirect ? "&evento_id=$evento_id_redirect" : "";
        header("Location: index.php?view=admin/cursos/index$redirect");
        exit;
    } else {
        $errores = $resultado['errores'];
        // Si falló, mantenemos los datos que el usuario escribió
        $curso = $_POST;
        if (!isset($curso['id']) && isset($_POST['id'])) {
            $curso['id'] = $_POST['id'];
        }
    }
}

// Obtener listas para selects
$eventos = $eventoService->listarEventos();
$ponentes = $ponenteDAO->obtenerTodos();

$mensajeFlash = MessageHandler::getFlash();
$page = 'admin_cursos';
$title = isset($curso['id']) ? 'Editar Curso - Admin' : 'Nuevo Curso - Admin';
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
                    <i class="fas fa-<?= isset($curso['id']) ? 'edit' : 'plus-circle' ?>"></i> 
                    <?= isset($curso['id']) ? 'Editar Curso' : 'Nuevo Curso' ?>
                </h1>
                <p class="mb-0"><?= isset($curso['id']) ? 'Modifica la información del curso' : 'Completa el formulario para crear un nuevo curso' ?></p>
            </div>

            <!-- Formulario -->
            <div class="form-card">
                        
                        <!-- Mensajes Flash -->
                        <?php if ($mensajeFlash): ?>
                            <div class="alert alert-<?php echo $mensajeFlash['tipo']; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($mensajeFlash['mensaje']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errores)): ?>
                            <div class="alert alert-danger">
                                <strong><i class="fas fa-exclamation-triangle"></i> Errores encontrados:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach($errores as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            
                            <input type="hidden" name="id" value="<?php echo $curso['id'] ?? ''; ?>">

                            <div class="mb-3">
                                <label class="form-label">Evento *</label>
                                <select name="evento_id" class="form-select" required <?php echo (isset($curso['id'])) ? 'disabled' : ''; ?>>
                                    <option value="">Seleccione un evento</option>
                                    <?php foreach($eventos as $ev): ?>
                                        <?php 
                                        $selected = '';
                                        if (isset($curso['evento_id'])) {
                                            $selected = ($curso['evento_id'] == $ev['id']) ? 'selected' : '';
                                        } elseif ($eventoPredefinido && $eventoPredefinido['id'] == $ev['id']) {
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="<?php echo $ev['id']; ?>" <?php echo $selected; ?>>
                                            <?php echo htmlspecialchars($ev['titulo']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($curso['evento_id'])): ?>
                                    <input type="hidden" name="evento_id" value="<?php echo $curso['evento_id']; ?>">
                                <?php elseif ($eventoPredefinido): ?>
                                    <input type="hidden" name="evento_id" value="<?php echo $eventoPredefinido['id']; ?>">
                                <?php endif; ?>
                                <small class="form-text text-muted"><?php echo (isset($curso['id'])) ? 'No se puede cambiar el evento de un curso existente.' : ''; ?></small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nombre del Curso *</label>
                                <input type="text" name="nombre" class="form-control" required 
                                       placeholder="Ej: Introducción a PHP"
                                       value="<?php echo htmlspecialchars($curso['nombre'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="4" 
                                          placeholder="Descripción detallada del curso..."><?php echo htmlspecialchars($curso['descripcion'] ?? ''); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha</label>
                                    <input type="date" name="fecha" class="form-control"
                                           value="<?php echo $curso['fecha'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Horario</label>
                                    <input type="text" name="horario" class="form-control" 
                                           placeholder="Ej: 9:00 - 13:00"
                                           value="<?php echo htmlspecialchars($curso['horario'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ponente</label>
                                    <select name="ponente_id" class="form-select">
                                        <option value="">Sin asignar</option>
                                        <?php foreach($ponentes as $ponente): ?>
                                            <option value="<?php echo $ponente['id']; ?>" 
                                                    <?php echo (isset($curso['ponente_id']) && $curso['ponente_id'] == $ponente['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($ponente['nombre']); ?> 
                                                <?php if ($ponente['cargo']): ?>
                                                    - <?php echo htmlspecialchars($ponente['cargo']); ?>
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Cupos *</label>
                                    <input type="number" name="cupos" class="form-control" 
                                           min="1" required
                                           value="<?php echo $curso['cupos'] ?? '50'; ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Precio ($)</label>
                                    <input type="number" name="precio" class="form-control" 
                                           step="0.01" min="0"
                                           placeholder="0.00"
                                           value="<?php echo $curso['precio'] ?? ''; ?>">
                                    <small class="form-text text-muted">Dejar vacío si es gratis</small>
                                </div>
                            </div>

                            <?php if (isset($curso['id'])): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Cupos disponibles:</strong> <?php echo $curso['cupos_disponibles'] ?? 0; ?> de <?php echo $curso['cupos'] ?? 0; ?>
                                </div>
                            <?php endif; ?>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php?view=admin/cursos/index<?php echo isset($curso['evento_id']) ? "&evento_id={$curso['evento_id']}" : ($evento_id_get ? "&evento_id=$evento_id_get" : ""); ?>" 
                                   class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> <?= isset($curso['id']) ? 'Actualizar Curso' : 'Crear Curso' ?>
                                </button>
                            </div>
                        </form>
            </div>

            <!-- Botón volver -->
            <div class="mt-4">
                <a href="index.php?view=admin/cursos/index<?php echo isset($curso['evento_id']) ? "&evento_id={$curso['evento_id']}" : ($evento_id_get ? "&evento_id=$evento_id_get" : ""); ?>" 
                   class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a la Lista
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include __DIR__ . '/../../templates/footer.php'; ?>
</body>
</html>
