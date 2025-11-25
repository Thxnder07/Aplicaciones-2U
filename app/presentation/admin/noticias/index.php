<?php
require_once '../../../business/NoticiaService.php';
// Aquí deberías incluir una validación de sesión (AuthService)
// include '../../templates/header_admin.php'; 

$service = new NoticiaService();

// Lógica para eliminar si se recibe un ID
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $service->eliminarNoticia($_GET['id']);
    header("Location: index.php?msg=eliminado");
    exit;
}

$noticias = $service->listarNoticias();
?>

<div class="container my-5">
    <h2>Gestión de Noticias</h2>
    <a href="crear.php" class="btn btn-success mb-3">Nueva Noticia</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Título</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($noticias as $n): ?>
            <tr>
                <td><?= $n['fecha_publicacion'] ?></td>
                <td><?= $n['titulo'] ?></td>
                <td>
                    <a href="crear.php?id=<?= $n['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    
                    <a href="index.php?action=delete&id=<?= $n['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Seguro que deseas eliminar esta noticia?');">
                       Eliminar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
