<?php 
$page = 'home';
$title = 'EventHub | Inicio';
include 'app/presentation/templates/header.php'; 
?>

<section class="banner">
    <div class="container">
        <h2>Explora los mejores Congresos y Seminarios</h2>
        <p>Conecta con expertos, comparte conocimiento y vive una experiencia académica única.</p>
    </div>
</section>



<section class="container" style="padding: 4rem 0;">
    <h2>Próximos eventos</h2>
    <div class="grid-3">
        <div class="card">
            <img src="public/img/eventos/evento1.jpg" alt="Evento Tech" class="event-img">
            <div class="card-content">
                <h3>Congreso de Innovación</h3>
                <p>Del 10 al 12 de noviembre — Lima, Perú</p>
            </div>
        </div>
        <div class="card">
            <img src="public/img/eventos/evento2.jpg" alt="Evento Digital" class="event-img">
            <div class="card-content">
                <h3>Seminario Digital</h3>
                <p>25 de noviembre — Virtual</p>
            </div>
        </div>
        <div class="card">
            <img src="public/img/eventos/evento3.jpg" alt="Congreso Ciencia" class="event-img">
            <div class="card-content">
                <h3>Congreso de Ciencia</h3>
                <p>2 al 4 de diciembre — Arequipa</p>
            </div>
        </div>
    </div>
</section>

<section class="beneficios">
    <div class="container">
        <h2>¿Por qué participar?</h2>
        <div class="beneficios-lista">
            <div class="beneficio">
                <h3>Aprendizaje</h3>
                <p>Accede a charlas de expertos y amplía tus conocimientos.</p>
            </div>
            <div class="beneficio">
                <h3>Networking</h3>
                <p>Conecta con profesionales e investigadores.</p>
            </div>
            <div class="beneficio">
                <h3>Experiencia</h3>
                <p>Vive una experiencia académica enriquecedora.</p>
            </div>
        </div>
    </div>
</section>

 <section class="sobre">
        <div class="container">
            <h2>Sobre EventHub</h2>
            <p>
                <strong>EventHub</strong> es una plataforma dedicada a la difusión y promoción de congresos, seminarios y eventos académicos
                a nivel nacional e internacional. Nuestro objetivo es conectar a estudiantes, profesionales e instituciones
                en un espacio digital donde el conocimiento y la innovación se encuentren.
            </p>
            <p>
                Aquí encontrarás información actualizada sobre los próximos eventos, ponentes destacados y oportunidades
                para fortalecer tu desarrollo académico y profesional.
            </p>
        </div>
    </section>

<?php include 'app/presentation/templates/footer.php'; ?>