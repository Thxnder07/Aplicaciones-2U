<?php 
$page = 'eventos';
$title = 'EventHub | Eventos';
include 'app/presentation/templates/header.php'; 
?>

<section class="hero hero-eventos">
    <div class="container">
        <h1>Eventos Destacados</h1>
        <p>Descubre los congresos y seminarios más importantes del año</p>
    </div>
</section>
<link rel="stylesheet" href="<?php echo $base_url; ?>public/css/styles.css">
<main class="container" style="padding: 60px 0;">
    <div class="grid-3">
         <!-- Evento 1 -->
      <article class="card">
        <img src="public/img/eventos/tecnologia.jpg" alt="Congreso de Tecnología Digital" class="event-img">
        <h2 class="event-title">Congreso de Tecnología Digital</h2>
        <p class="event-date">15-17 de marzo, 2025</p>
        <p>Un evento imperdible sobre inteligencia artificial, blockchain y desarrollo web.</p>
        <button class="btn toggle-btn" onclick="toggleDetails(this)">Ver detalles ▼</button>
        <div class="event-details">
          <p><strong>Lugar:</strong> Centro de Convenciones Madrid</p>
          <p><strong>Horario:</strong> 9:00 - 18:00</p>
          <p><strong>Precio:</strong> €299</p>
          <p><strong>Ponentes:</strong> 25+ expertos internacionales</p>
        </div>
      </article>

      <!-- Evento 2 -->
      <article class="card">
        <img src="public/img/eventos/marketing.jpg" alt="Seminario de Marketing Digital" class="event-img">
        <h2 class="event-title">Seminario de Marketing Digital</h2>
        <p class="event-date">22 de abril, 2025</p>
        <p>Aprende estrategias reales y efectivas para el crecimiento digital.</p>
        <button class="btn toggle-btn" onclick="toggleDetails(this)">Ver detalles ▼</button>
        <div class="event-details">
          <p><strong>Lugar:</strong> Hotel Business Center Barcelona</p>
          <p><strong>Horario:</strong> 10:00 - 17:00</p>
          <p><strong>Precio:</strong> €149</p>
          <p><strong>Ponentes:</strong> Especialistas en marketing digital</p>
        </div>
      </article>

      <!-- Evento 3 -->
      <article class="card">
        <img src="public/img/eventos/salud.jpg" alt="Congreso Internacional de Salud" class="event-img">
        <h2 class="event-title">Congreso Internacional de Salud</h2>
        <p class="event-date">5-7 de mayo, 2025</p>
        <p>El evento más importante del año en medicina y telemedicina.</p>
        <button class="btn toggle-btn" onclick="toggleDetails(this)">Ver detalles ▼</button>
        <div class="event-details">
          <p><strong>Lugar:</strong> Palacio de Congresos Valencia</p>
          <p><strong>Horario:</strong> 8:30 - 19:00</p>
          <p><strong>Precio:</strong> €450 / €250 (estudiantes)</p>
          <p><strong>Ponentes:</strong> 40+ médicos y expertos</p>
        </div>
      </article>

      <!-- Evento 4 -->
      <article class="card">
        <img src="public/img/eventos/startups.jpg" alt="Foro Global de Innovación y Startups" class="event-img">
        <h2 class="event-title">Foro Global de Innovación y Startups</h2>
        <p class="event-date">10-12 de junio, 2025</p>
        <p>Reúne a emprendedores, inversores y líderes del ecosistema tecnológico internacional.</p>
        <button class="btn toggle-btn" onclick="toggleDetails(this)">Ver detalles ▼</button>
        <div class="event-details">
          <p><strong>Lugar:</strong> Lisboa Convention Center</p>
          <p><strong>Horario:</strong> 9:30 - 18:30</p>
          <p><strong>Precio:</strong> €320</p>
          <p><strong>Ponentes:</strong> 60+ fundadores de startups y CEOs</p>
        </div>
      </article>

      <!-- Evento 5 -->
      <article class="card">
        <img src="public/img/eventos/sostenibilidad.jpg" alt="Cumbre de Sostenibilidad y Medio Ambiente" class="event-img">
        <h2 class="event-title">Cumbre de Sostenibilidad y Medio Ambiente</h2>
        <p class="event-date">3-4 de julio, 2025</p>
        <p>Debate global sobre energías renovables, gestión ambiental y políticas sostenibles.</p>
        <button class="btn toggle-btn" onclick="toggleDetails(this)">Ver detalles ▼</button>
        <div class="event-details">
          <p><strong>Lugar:</strong> Centro Ecológico de Berlín</p>
          <p><strong>Horario:</strong> 9:00 - 17:30</p>
          <p><strong>Precio:</strong> €200</p>
          <p><strong>Ponentes:</strong> 30 expertos en sostenibilidad</p>
        </div>
      </article>

      <!-- Evento 6 -->
      <article class="card">
        <img src="public/img/eventos/arte-digital.jpg" alt="Festival Internacional de Arte Digital" class="event-img">
        <h2 class="event-title">Festival Internacional de Arte Digital</h2>
        <p class="event-date">18-20 de agosto, 2025</p>
        <p>Una experiencia inmersiva donde el arte y la tecnología se fusionan.</p>
        <button class="btn toggle-btn" onclick="toggleDetails(this)">Ver detalles ▼</button>
        <div class="event-details">
          <p><strong>Lugar:</strong> Museo de Arte Contemporáneo de Buenos Aires</p>
          <p><strong>Horario:</strong> 11:00 - 20:00</p>
          <p><strong>Precio:</strong> €180</p>
          <p><strong>Ponentes:</strong> Artistas y tecnólogos de 15 países</p>
        </div>
      </article>

        </div>
</main>

<?php include 'app/presentation/templates/footer.php'; ?>