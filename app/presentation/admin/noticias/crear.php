<?php
require_once '../../../business/NoticiaService.php';
// include '../../templates/header_admin.php'; 

$service = new NoticiaService();
$mensaje = "";

// Si es POST, estamos guardando
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $res = $service->guardarNoticia($_POST, $_FILES['imagen']);
    if ($res) {
        header("Location: index.php?msg=creado");
        exit;
    } else {
        $mensaje = "Error al guardar la noticia.";
    }
}
?>

<div class="container my-5">
    <h2>Publicar Noticia</h2>
    
    <?php if($mensaje): ?><div class="alert alert-danger"><?= $mensaje ?></div><?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Título</label>
            <input type="text" name="titulo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Resumen</label>
            <textarea name="resumen" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Contenido Completo</label>
            <textarea name="contenido" class="form-control" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label>Imagen</label>
            <input type="file" name="imagen" class="form-control" accept="image/*">
        </div>
        
        <button type="submit" class="btn btn-primary">Guardar Noticia</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
