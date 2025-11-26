<?php
require_once __DIR__ . '/../../../business/EventoService.php';

$service = new EventoService();
$errores = [];
$evento = null; // Datos vacíos por defecto

// 1. DETECTAR SI ESTAMOS EDITANDO (Viene un ID por GET)
if (isset($_GET['id'])) {
    $evento = $service->obtenerEvento($_GET['id']);
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
        header("Location: index.php?msg=guardado");
        exit;
    } else {
        $errores = $res['errores'];
        // Si falló, mantenemos los datos que el usuario escribió para que no los pierda
        $evento = $_POST; 
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?= isset($_GET['id']) ? 'Editar Evento' : 'Nuevo Evento' ?></h4>
                </div>
                <div class="card-body">
                    
                    <?php if (!empty($errores)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach($errores as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data">
                        
                        <input type="hidden" name="id" value="<?= $evento['id'] ?? '' ?>">
                        <input type="hidden" name="imagen_actual" value="<?= $evento['imagen'] ?? '' ?>">

                        <div class="mb-3">
                            <label class="form-label">Título del Evento *</label>
                            <input type="text" name="titulo" class="form-control" required 
                                   value="<?= htmlspecialchars($evento['titulo'] ?? '') ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha (Texto) *</label>
                                <input type="text" name="fecha_texto" class="form-control" placeholder="Ej: 15-17 de Marzo" required
                                       value="<?= htmlspecialchars($evento['fecha_texto'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Horario</label>
                                <input type="text" name="horario" class="form-control" placeholder="Ej: 9:00 - 18:00"
                                       value="<?= htmlspecialchars($evento['horario'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lugar *</label>
                            <input type="text" name="lugar" class="form-control" required
                                   value="<?= htmlspecialchars($evento['lugar'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción Breve</label>
                            <textarea name="descripcion_breve" class="form-control" rows="3"><?= htmlspecialchars($evento['descripcion_breve'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Precio ($) *</label>
                                <input type="number" name="precio" step="0.01" class="form-control" required
                                       value="<?= htmlspecialchars($evento['precio'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Imagen</label>
                                <input type="file" name="imagen" class="form-control" accept="image/*">
                                <?php if(!empty($evento['imagen'])): ?>
                                    <div class="mt-2">
                                        <small>Actual:</small><br>
                                        <img src="../../../../public/<?= $evento['imagen'] ?>" width="100">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Guardar Evento</button>
                            <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>