<?php
// 1. Incluimos TU servicio (La lógica de Jeffri)
require_once __DIR__ . '/../../../business/EventoService.php';

$service = new EventoService();
$mensaje = "";

// LÓGICA: ELIMINAR
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $res = $service->eliminarEvento($_GET['id']);
    if ($res) {
        header("Location: index.php?msg=eliminado");
        exit;
    } else {
        $mensaje = "Error al eliminar el evento.";
    }
}

// LÓGICA: LISTAR
$eventos = $service->listarEventos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestión de Eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Eventos</h1>
        <a href="crear.php" class="btn btn-primary">Agendar Nuevo Evento</a>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg']=='guardado'): ?>
        <div class="alert alert-success">¡Evento guardado correctamente!</div>
    <?php endif; ?>
    <?php if(isset($_GET['msg']) && $_GET['msg']=='eliminado'): ?>
        <div class="alert alert-warning">Evento eliminado correctamente.</div>
    <?php endif; ?>
    <?php if($mensaje): ?>
        <div class="alert alert-danger"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Cupos</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($eventos as $ev): ?>
                    <tr>
                        <td>
                            <img src="../../../../public/<?= $ev['imagen'] ?>" style="height: 50px; width: 50px; object-fit: cover; border-radius: 5px;">
                        </td>
                        <td><?= htmlspecialchars($ev['titulo']) ?></td>
                        <td><?= htmlspecialchars($ev['fecha_texto']) ?></td>
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
                                <?= $cupos_disponibles ?> / <?= $cupos ?>
                            </span>
                        </td>
                        <td>$<?= htmlspecialchars($ev['precio']) ?></td>
                        <td>
                            <a href="crear.php?id=<?= $ev['id'] ?>" class="btn btn-sm btn-info text-white">Editar</a>
                            <a href="index.php?action=delete&id=<?= $ev['id'] ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('¿Estás seguro de eliminar este evento?');">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="../../../index.php" class="btn btn-secondary">← Volver al Sitio Web</a>
    </div>
</div>

</body>
</html>