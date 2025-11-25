<?php
require_once '../business/NoticiaService.php';
// Asumimos que Ferrer creó header.php en templates
include '../templates/header.php'; 

$servicio = new NoticiaService();
$listaNoticias = $servicio->listarNoticias();
?>

<section class="bg-dark text-white text-center py-5">
    <div class="container">
        <h1>Actualidad y Novedades</h1>
        <p class="lead">Entérate de lo último en EventHub</p>
    </div>
</section>

<div class="container my-5">
    <div class="row">
        <?php foreach ($listaNoticias as $noticia): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="../<?php echo htmlspecialchars($noticia['imagen']); ?>" class="card-img-top" alt="Imagen noticia" style="height: 200px; object-fit: cover;">
                    
                    <div class="card-body d-flex flex-column">
                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($noticia['fecha_publicacion'])); ?></small>
                        <h5 class="card-title mt-2"><?php echo htmlspecialchars($noticia['titulo']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($noticia['resumen']); ?></p>
                        
                        <?php if($noticia['es_destacada']): ?>
                            <span class="badge bg-warning text-dark mb-2 w-25">Destacada</span>
                        <?php endif; ?>
                        
                        <a href="#" class="btn btn-primary mt-auto">Leer completa</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
