<?php 
$page = 'contacto';
$title = 'EventHub | Contacto';
include 'app/presentation/templates/header.php'; 
?>

<div class="titulo-seccion">
    <div class="container">
        <h1>Contacto</h1>
        <p>¿Tienes preguntas? Estamos aquí para Ayudarte.</p>
    </div>
</div>
<link rel="stylesheet" href="<?php echo $base_url; ?>public/css/styles.css">
<section class="contact-section container">
    <div class="contact-wrapper">
        <div class="contact-form">
            <h2>Envíanos un mensaje</h2>
            <form action="#" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn-submit">Enviar Mensaje</button>
            </form>
        </div>

        <div class="contact-info">
            <h2>Información</h2>
            <ul style="margin-top: 1rem; line-height: 2;">
                <li><i class="fas fa-envelope"></i> info@eventhub.com</li>
                <li><i class="fas fa-phone"></i> +51 902 565 459</li>
                <li><i class="fas fa-map-marker-alt"></i> 123 Av. Principal, Lima</li>
                <iframe 
                src="https://maps.google.com/maps?q=123%20Av.%20Principal%2C%20Lima&t=&z=14&ie=UTF8&iwloc=&output=embed" 
                width="100%" 
                height="300" 
                style="border:0; border-radius: 8px;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
                 </iframe>
            </ul>
        
        </div>
    </div>
</section>

<?php include 'app/presentation/templates/footer.php'; ?>