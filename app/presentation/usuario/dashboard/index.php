<?php
// Protección de rutas - Requerir autenticación
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../business/EventoService.php';
require_once __DIR__ . '/../../../utils/MessageHandler.php';

// Verificar que el usuario esté autenticado
AuthMiddleware::requerirAutenticacion();

// Obtener datos para el dashboard
$eventoService = new EventoService();
$eventos = $eventoService->listarEventos();
$eventosDisponibles = array_slice($eventos, 0, 6); // Mostrar solo los primeros 6

// Obtener mensaje flash si existe
$mensajeFlash = MessageHandler::getFlash();

// Calcular base_url para rutas de recursos
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$base_url = $path . '/public/';

$page = 'usuario_dashboard';
$title = 'Mi Dashboard - EventHub';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>

    <link rel="stylesheet" href="<?php echo $base_url; ?>css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .dashboard-container {
            min-height: 100vh;
            background: #f5f7fa;
            padding: 30px 0;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .welcome-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .event-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .event-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .event-card-body {
            padding: 20px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ddd;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../../templates/header.php'; ?>

    <div class="dashboard-container">
        <div class="container">
            <!-- Header del Dashboard -->
            <div class="dashboard-header">
                <h1><i class="fas fa-user-circle"></i> Mi Dashboard</h1>
                <p class="mb-0">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></p>
            </div>

            <!-- Mensajes Flash -->
            <?php if ($mensajeFlash): ?>
                <div class="alert alert-<?php echo $mensajeFlash['tipo']; ?> alert-dismissible fade show" role="alert">
                    <i
                        class="fas fa-<?php echo $mensajeFlash['tipo'] === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                    <?php echo htmlspecialchars($mensajeFlash['mensaje']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tarjeta de Bienvenida -->
            <div class="welcome-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3><i class="fas fa-star text-warning"></i> ¡Bienvenido a EventHub!</h3>
                        <p class="mb-0">Explora los eventos disponibles e inscríbete en los que más te interesen. Desde
                            aquí puedes gestionar tus inscripciones y mantenerte al día con las últimas novedades.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="index.php?view=eventos" class="btn btn-primary btn-lg">
                            <i class="fas fa-search"></i> Ver Todos los Eventos
                        </a>
                    </div>
                </div>
            </div>

            <!-- Eventos Disponibles -->
            <div class="mb-4">
                <h2 class="section-title">
                    <i class="fas fa-calendar-check"></i> Eventos Disponibles
                </h2>
                <?php if (!empty($eventosDisponibles)): ?>
                    <div class="row g-4">
                        <?php foreach ($eventosDisponibles as $evento): ?>
                            <div class="col-md-4">
                                <div class="event-card">
                                    <img src="<?php echo $base_url . htmlspecialchars($evento['imagen'] ?? 'img/eventos/evento1.jpg'); ?>"
                                        alt="<?php echo htmlspecialchars($evento['titulo']); ?>"
                                        onerror="this.src='<?php echo $base_url; ?>img/eventos/evento1.jpg'">
                                    <div class="event-card-body">
                                        <h5><?php echo htmlspecialchars($evento['titulo']); ?></h5>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo htmlspecialchars($evento['fecha_texto'] ?? 'Fecha no disponible'); ?>
                                        </p>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo htmlspecialchars($evento['lugar'] ?? 'Lugar no disponible'); ?>
                                        </p>
                                        <p class="mb-3">
                                            <strong
                                                class="text-primary">$<?php echo number_format($evento['precio'] ?? 0, 2); ?></strong>
                                        </p>
                                        <a href="index.php?view=eventos/detalle&id=<?php echo $evento['id']; ?>" class="btn btn-outline-primary btn-sm w-100">
                                            <i class="fas fa-info-circle"></i> Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-4">
                        <a href="index.php?view=eventos" class="btn btn-primary">
                            Ver Todos los Eventos <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="empty-state bg-white rounded p-5">
                        <i class="fas fa-calendar-times"></i>
                        <h4>No hay eventos disponibles</h4>
                        <p>Los eventos aparecerán aquí cuando estén disponibles.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Mis Inscripciones (Placeholder) -->
            <div class="bg-white rounded p-4 shadow-sm">
                <h2 class="section-title">
                    <i class="fas fa-clipboard-list"></i> Mis Inscripciones
                </h2>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h4>No tienes inscripciones aún</h4>
                    <p>Cuando te inscribas a un evento, aparecerá aquí.</p>
                    <a href="index.php?view=eventos" class="btn btn-primary mt-3">
                        <i class="fas fa-search"></i> Explorar Eventos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>