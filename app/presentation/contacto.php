<?php
require_once '../business/ContactoService.php';
include '../templates/header.php'; 

$resultado = null;

// Si el usuario envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicio = new ContactoService();
    $resultado = $servicio->procesarFormulario();
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="text-center mb-4">Contáctanos</h2>

            <?php if ($resultado): ?>
                <div class="alert alert-<?php echo ($resultado['status'] == 'success') ? 'success' : 'danger'; ?>">
                    <?php echo $resultado['msg']; ?>
                </div>
            <?php endif; ?>

            <div class="card p-4 bg-light">
                <form action="contacto.php" method="POST">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="mensaje" class="form-label">Tu Mensaje</label>
                        <textarea class="form-control" name="mensaje" rows="5" required></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Enviar Mensaje</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
