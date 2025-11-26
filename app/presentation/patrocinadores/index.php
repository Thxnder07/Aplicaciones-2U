<?php
// Vista pública de patrocinadores
require_once __DIR__ . '/../../data/PatrocinadorDAO.php';

$patrocinadorDAO = new PatrocinadorDAO();

// Obtener patrocinadores por tipo
$principales = $patrocinadorDAO->obtenerPorTipo('Principal');
$oficiales = $patrocinadorDAO->obtenerPorTipo('Oficial');
$colaboradores = $patrocinadorDAO->obtenerPorTipo('Colaborador');

$page = 'patrocinadores';
$title = 'Patrocinadores - EventHub';
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title; ?></title>

  <link rel="stylesheet" href="public/css/styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <style>
    .patrocinadores-hero {
      background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
      color: white;
      padding: 80px 0 60px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .patrocinadores-hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
      opacity: 0.3;
    }

    .patrocinadores-hero h1 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 1rem;
      position: relative;
      z-index: 1;
    }

    .patrocinadores-hero p {
      font-size: 1.2rem;
      opacity: 0.95;
      max-width: 700px;
      margin: 0 auto;
      position: relative;
      z-index: 1;
    }

    .patrocinadores-section {
      padding: 60px 0;
    }

    .patrocinadores-section:nth-child(even) {
      background: #f8fafc;
    }

    .category-title {
      text-align: center;
      font-size: 2rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 40px;
      position: relative;
      padding-bottom: 15px;
    }

    .category-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(90deg, #7c3aed, #a855f7);
      border-radius: 2px;
    }

    .category-badge {
      display: inline-block;
      padding: 8px 20px;
      border-radius: 50px;
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .badge-principal {
      background: linear-gradient(135deg, #fbbf24, #f59e0b);
      color: white;
    }

    .badge-oficial {
      background: linear-gradient(135deg, #3b82f6, #2563eb);
      color: white;
    }

    .badge-colaborador {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
    }

    .patrocinadores-grid {
      display: grid;
      gap: 30px;
      margin-top: 40px;
    }

    .patrocinadores-grid.principal {
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }

    .patrocinadores-grid.oficial {
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }

    .patrocinadores-grid.colaborador {
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }

    .patrocinador-card {
      background: white;
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .patrocinador-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #7c3aed, #a855f7);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .patrocinador-card:hover::before {
      transform: scaleX(1);
    }

    .patrocinador-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 12px 40px rgba(124, 58, 237, 0.2);
    }

    .logo-container {
      width: 100%;
      height: 150px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      background: #f8fafc;
      border-radius: 15px;
      padding: 20px;
      transition: background 0.3s ease;
    }

    .patrocinador-card:hover .logo-container {
      background: #f1f5f9;
    }

    .logo-container img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }

    .logo-placeholder {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
      border-radius: 10px;
    }

    .logo-placeholder i {
      font-size: 60px;
      color: #7c3aed;
      opacity: 0.5;
    }

    .patrocinador-nombre {
      font-size: 1.3rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 10px;
    }

    .patrocinador-sector {
      font-size: 0.95rem;
      color: #7c3aed;
      font-weight: 600;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .patrocinador-sector i {
      font-size: 0.9rem;
    }

    .patrocinador-descripcion {
      font-size: 0.9rem;
      color: #64748b;
      line-height: 1.6;
    }

    .cta-patrocinio {
      background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
      color: white;
      padding: 80px 0;
      text-align: center;
    }

    .cta-content h2 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 15px;
    }

    .cta-content p {
      font-size: 1.2rem;
      opacity: 0.9;
      margin-bottom: 30px;
    }

    .btn-cta {
      display: inline-block;
      padding: 15px 40px;
      background: linear-gradient(135deg, #7c3aed, #a855f7);
      color: white;
      text-decoration: none;
      border-radius: 50px;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 20px rgba(124, 58, 237, 0.4);
    }

    .btn-cta:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(124, 58, 237, 0.6);
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
    }

    .empty-state i {
      font-size: 60px;
      color: #cbd5e1;
      margin-bottom: 15px;
    }

    .empty-state p {
      color: #94a3b8;
      font-size: 1.1rem;
    }

    @media (max-width: 768px) {
      .patrocinadores-hero h1 {
        font-size: 2rem;
      }

      .patrocinadores-grid.principal,
      .patrocinadores-grid.oficial,
      .patrocinadores-grid.colaborador {
        grid-template-columns: 1fr;
      }

      .cta-content h2 {
        font-size: 1.8rem;
      }
    }
  </style>
</head>

<body>
  <?php include __DIR__ . '/../templates/header.php'; ?>

  <!-- Hero Section -->
  <section class="patrocinadores-hero">
    <div class="container">
      <h1><i class="fas fa-handshake"></i> Nuestros Patrocinadores</h1>
      <p>Empresas e instituciones que hacen posible la realización de eventos de excelencia</p>
    </div>
  </section>

  <!-- Patrocinadores Principales -->
  <?php if (!empty($principales)): ?>
    <section class="patrocinadores-section">
      <div class="container">
        <div style="text-align: center;">
          <span class="category-badge badge-principal">
            <i class="fas fa-crown"></i> Principales
          </span>
        </div>
        <h2 class="category-title">Patrocinadores Principales</h2>

        <div class="patrocinadores-grid principal">
          <?php foreach ($principales as $patrocinador): ?>
            <div class="patrocinador-card">
              <div class="logo-container">
                <?php if (!empty($patrocinador['imagen']) && file_exists('public/' . $patrocinador['imagen'])): ?>
                  <img src="public/<?php echo htmlspecialchars($patrocinador['imagen']); ?>"
                    alt="<?php echo htmlspecialchars($patrocinador['nombre']); ?>">
                <?php else: ?>
                  <div class="logo-placeholder">
                    <i class="fas fa-building"></i>
                  </div>
                <?php endif; ?>
              </div>
              <h3 class="patrocinador-nombre"><?php echo htmlspecialchars($patrocinador['nombre']); ?></h3>

              <?php if (!empty($patrocinador['sector'])): ?>
                <div class="patrocinador-sector">
                  <i class="fas fa-tag"></i>
                  <?php echo htmlspecialchars($patrocinador['sector']); ?>
                </div>
              <?php endif; ?>

              <?php if (!empty($patrocinador['descripcion'])): ?>
                <p class="patrocinador-descripcion"><?php echo htmlspecialchars($patrocinador['descripcion']); ?></p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- Patrocinadores Oficiales -->
  <?php if (!empty($oficiales)): ?>
    <section class="patrocinadores-section">
      <div class="container">
        <div style="text-align: center;">
          <span class="category-badge badge-oficial">
            <i class="fas fa-star"></i> Oficiales
          </span>
        </div>
        <h2 class="category-title">Patrocinadores Oficiales</h2>

        <div class="patrocinadores-grid oficial">
          <?php foreach ($oficiales as $patrocinador): ?>
            <div class="patrocinador-card">
              <div class="logo-container">
                <?php if (!empty($patrocinador['imagen']) && file_exists('public/' . $patrocinador['imagen'])): ?>
                  <img src="public/<?php echo htmlspecialchars($patrocinador['imagen']); ?>"
                    alt="<?php echo htmlspecialchars($patrocinador['nombre']); ?>">
                <?php else: ?>
                  <div class="logo-placeholder">
                    <i class="fas fa-building"></i>
                  </div>
                <?php endif; ?>
              </div>
              <h3 class="patrocinador-nombre"><?php echo htmlspecialchars($patrocinador['nombre']); ?></h3>

              <?php if (!empty($patrocinador['sector'])): ?>
                <div class="patrocinador-sector">
                  <i class="fas fa-tag"></i>
                  <?php echo htmlspecialchars($patrocinador['sector']); ?>
                </div>
              <?php endif; ?>

              <?php if (!empty($patrocinador['descripcion'])): ?>
                <p class="patrocinador-descripcion"><?php echo htmlspecialchars($patrocinador['descripcion']); ?></p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- Patrocinadores Colaboradores -->
  <?php if (!empty($colaboradores)): ?>
    <section class="patrocinadores-section">
      <div class="container">
        <div style="text-align: center;">
          <span class="category-badge badge-colaborador">
            <i class="fas fa-users"></i> Colaboradores
          </span>
        </div>
        <h2 class="category-title">Patrocinadores Colaboradores</h2>

        <div class="patrocinadores-grid colaborador">
          <?php foreach ($colaboradores as $patrocinador): ?>
            <div class="patrocinador-card">
              <div class="logo-container">
                <?php if (!empty($patrocinador['imagen']) && file_exists('public/' . $patrocinador['imagen'])): ?>
                  <img src="public/<?php echo htmlspecialchars($patrocinador['imagen']); ?>"
                    alt="<?php echo htmlspecialchars($patrocinador['nombre']); ?>">
                <?php else: ?>
                  <div class="logo-placeholder">
                    <i class="fas fa-building"></i>
                  </div>
                <?php endif; ?>
              </div>
              <h3 class="patrocinador-nombre"><?php echo htmlspecialchars($patrocinador['nombre']); ?></h3>

              <?php if (!empty($patrocinador['sector'])): ?>
                <div class="patrocinador-sector">
                  <i class="fas fa-tag"></i>
                  <?php echo htmlspecialchars($patrocinador['sector']); ?>
                </div>
              <?php endif; ?>

              <?php if (!empty($patrocinador['descripcion'])): ?>
                <p class="patrocinador-descripcion"><?php echo htmlspecialchars($patrocinador['descripcion']); ?></p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- CTA Section -->
  <section class="cta-patrocinio">
    <div class="container">
      <div class="cta-content">
        <h2>¿Quieres ser nuestro patrocinador?</h2>
        <p>Únete a las empresas líderes que apoyan el desarrollo del conocimiento</p>
        <a href="index.php?view=contacto" class="btn-cta">
          <i class="fas fa-envelope"></i> Contáctanos
        </a>
      </div>
    </div>
  </section>

  <?php include __DIR__ . '/../templates/footer.php'; ?>
</body>

</html>