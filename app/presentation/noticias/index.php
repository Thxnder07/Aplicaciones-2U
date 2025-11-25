<?php 
$page = 'noticias';
$title = 'EventHub | Noticias';
include 'app/presentation/templates/header.php'; 
?>

<section class="hero hero-noticias">
    <div class="container">
        <h1>Noticias y Actualidad</h1>
    </div>
</section>

<section class="container" style="padding: 4rem 0;">
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/css/styles.css">
    <article class="card" style="display: flex; flex-wrap: wrap; margin-bottom: 3rem;">
        <div style="flex: 1; min-width: 300px;">
            <img src="public/img/noticias/destacada.jpg" style="height: 100%; object-fit: cover;" alt="Destacada">
        </div>
        <div class="card-content" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
            <h2>Innovación educativa 2025</h2>
            <p>Nuevas tendencias sobre metodologías digitales presentadas ante 3,000 asistentes.</p>
            <a href="#" class="btn">Leer más</a>
        </div>
    </article>

    <div class="grid-3">
        <article class="card">
            <img src="public/img/noticias/n1.jpg" alt="Noticia 1" class="event-img">
            <div class="card-content">
                <h3>IA en la Educación</h3>
                <p class="date">14 Oct, 2025</p>
                <a href="#" style="color: var(--primary-color); font-weight: bold;">Leer más →</a>
            </div>
        </article>
        </div>
</section>

<?php include 'app/presentation/templates/footer.php'; ?>