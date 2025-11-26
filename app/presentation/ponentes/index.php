<?php
// Vista pública de ponentes
require_once __DIR__ . '/../../data/PonenteDAO.php';

$ponenteDAO = new PonenteDAO();
$ponentes = $ponenteDAO->obtenerTodos();

$page = 'ponentes';
$title = 'Ponentes - EventHub';
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
        .ponentes-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 80px 0 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .ponentes-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.3;
        }

        .ponentes-hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .ponentes-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 700px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .ponentes-container {
            padding: 60px 0;
            background: #f8fafc;
        }

        .ponentes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .ponente-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
        }

        .ponente-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .ponente-image {
            width: 100%;
            height: 320px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .ponente-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ponente-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .ponente-image-placeholder i {
            font-size: 80px;
            color: rgba(255, 255, 255, 0.9);
        }

        .ponente-content {
            padding: 25px;
        }

        .ponente-nombre {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .ponente-cargo {
            font-size: 0.95rem;
            color: #3b82f6;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ponente-cargo i {
            font-size: 0.9rem;
        }

        .ponente-tema {
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.6;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .ponente-tema i {
            color: #f59e0b;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .stats-section {
            background: white;
            padding: 40px 0;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 900px;
            margin: 0 auto;
        }

        .stat-item {
            padding: 20px;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 1rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-state i {
            font-size: 80px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: #64748b;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #94a3b8;
        }

        @media (max-width: 768px) {
            .ponentes-hero h1 {
                font-size: 2rem;
            }

            .ponentes-grid {
                grid-template-columns: 1fr;
            }

            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../templates/header.php'; ?>

    <!-- Hero Section -->
    <section class="ponentes-hero">
        <div class="container">
            <h1><i class="fas fa-users"></i> Nuestros Ponentes</h1>
            <p>Expertos líderes en sus campos, compartiendo conocimiento y experiencia de clase mundial</p>
        </div>
    </section>

    <!-- Stats Section -->
    <?php if (!empty($ponentes)): ?>
        <section class="stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo count($ponentes); ?></div>
                        <div class="stat-label">Ponentes Expertos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">15+</div>
                        <div class="stat-label">Años de Experiencia</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Conferencias</div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Ponentes Grid -->
    <section class="ponentes-container">
        <div class="container">
            <?php if (empty($ponentes)): ?>
                <div class="empty-state">
                    <i class="fas fa-user-tie"></i>
                    <h3>No hay ponentes disponibles</h3>
                    <p>Pronto agregaremos expertos destacados a nuestra plataforma</p>
                </div>
            <?php else: ?>
                <div class="ponentes-grid">
                    <?php foreach ($ponentes as $ponente): ?>
                        <div class="ponente-card">
                            <div class="ponente-image">
                                <?php if (!empty($ponente['imagen']) && file_exists('public/' . $ponente['imagen'])): ?>
                                    <img src="public/<?php echo htmlspecialchars($ponente['imagen']); ?>"
                                        alt="<?php echo htmlspecialchars($ponente['nombre']); ?>">
                                <?php else: ?>
                                    <div class="ponente-image-placeholder">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="ponente-content">
                                <h3 class="ponente-nombre"><?php echo htmlspecialchars($ponente['nombre']); ?></h3>

                                <?php if (!empty($ponente['cargo'])): ?>
                                    <div class="ponente-cargo">
                                        <i class="fas fa-briefcase"></i>
                                        <?php echo htmlspecialchars($ponente['cargo']); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($ponente['tema'])): ?>
                                    <div class="ponente-tema">
                                        <i class="fas fa-lightbulb"></i>
                                        <span><?php echo htmlspecialchars($ponente['tema']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include __DIR__ . '/../templates/footer.php'; ?>
</body>

</html>