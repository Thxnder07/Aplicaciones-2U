<?php 
$page = 'ponentes';
$title = 'EventHub | Ponentes';
// El header ya incluye el CSS, no necesitas ponerlo abajo
include 'app/presentation/templates/header.php'; 
?>

<div class="titulo-seccion">
    <div class="container">
        <h1>Ponentes Destacados</h1>
        <p>Conoce a los expertos que compartirán sus conocimientos.</p>
    </div>
</div>

<div class="container grid-3" style="padding-bottom: 4rem;">

    <div class="tarjeta-ponente">
        <img src="public/img/ponentes/ponente2.jpg" alt="Ponente 2">
        <div class="card-content">
            <h3>Lic. Ana Torres</h3>
            <p class="cargo">Especialista en Finanzas Corporativas</p>
            <p class="tema">Tema: “Innovación Financiera”</p>
        </div>
    </div>

    <div class="tarjeta-ponente">
        <img src="public/img/ponentes/ponente3.jpg" alt="Ponente 3">
        <div class="card-content">
            <h3>Ing. Roberto López</h3>
            <p class="cargo">Director de Innovación Tecnológica</p>
            <p class="tema">Tema: “Transformación Digital”</p>
        </div>
    </div>

    <div class="tarjeta-ponente">
        <img src="public/img/ponentes/ponente4.jpg" alt="Ponente 4">
        <div class="card-content">
            <h3>Dra. Lucía Fernández</h3>
            <p class="cargo">Profesora Investigadora</p>
            <p class="tema">Tema: “Educación y Tecnología”</p>
        </div>
    </div>

    <div class="tarjeta-ponente">
        <img src="public/img/ponentes/ponente5.jpg" alt="Ponente 5">
        <div class="card-content">
            <h3>Dr. Javier Ramos</h3>
            <p class="cargo">Experto en Ciberseguridad</p>
            <p class="tema">Tema: “Protección de Datos e IA”</p>
        </div>
    </div>

    <div class="tarjeta-ponente">
        <img src="public/img/ponentes/ponente6.jpg" alt="Ponente 6">
        <div class="card-content">
            <h3>Lic. Mariana Silva</h3>
            <p class="cargo">Consultora en Innovación</p>
            <p class="tema">Tema: “Creatividad y Liderazgo”</p>
        </div>
    </div>

</div>

<?php include 'app/presentation/templates/footer.php'; ?>
