<?php
$page = 'contacto';
$title = 'EventHub | Contacto';
include 'app/presentation/templates/header.php';
?>

<section class="hero" style="padding: 4rem 0; background: var(--primary-color); color: var(--white);">
    <div class="container reveal">
        <h1 style="color: var(--white); margin-bottom: 1rem;">Contáctanos</h1>
        <p style="color: #cbd5e1; max-width: 600px; margin: 0 auto;">Estamos aquí para ayudarte. Envíanos tus dudas o
            sugerencias y te responderemos a la brevedad.</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="contact-wrapper">

            <!-- Form Column -->
            <div class="contact-form-container reveal reveal-delay-1">
                <h2 class="mb-4">Envíanos un mensaje</h2>
                <form action="#" method="POST">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>
                    </div>
                    <div class="form-group">
                        <label for="asunto">Asunto</label>
                        <input type="text" id="asunto" name="asunto" placeholder="Motivo de tu consulta" required>
                    </div>
                    <div class="form-group">
                        <label for="mensaje">Mensaje</label>
                        <textarea id="mensaje" name="mensaje" rows="5" placeholder="¿En qué podemos ayudarte?"
                            required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Enviar Mensaje</button>
                </form>
            </div>

            <!-- Info Column -->
            <div class="contact-info-container reveal reveal-delay-2">
                <h2>Información de Contacto</h2>
                <p class="mb-8" style="color: #64748b;">Visítanos en nuestras oficinas o contáctanos por nuestros
                    canales oficiales.</p>

                <div class="contact-details">
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="contact-text">
                            <h4>Ubicación</h4>
                            <p>Av. Principal 123, San Isidro<br>Lima, Perú</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                        <div class="contact-text">
                            <h4>Email</h4>
                            <p>contacto@eventhub.com<br>soporte@eventhub.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-phone"></i></div>
                        <div class="contact-text">
                            <h4>Teléfono</h4>
                            <p>+51 (01) 123-4567<br>+51 999 888 777</p>
                        </div>
                    </div>
                </div>

                <div class="map-container">
                    <iframe
                        src="https://maps.google.com/maps?q=San%20Isidro%2C%20Lima&t=&z=15&ie=UTF8&iwloc=&output=embed"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'app/presentation/templates/footer.php'; ?>