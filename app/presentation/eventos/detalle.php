<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../business/EventoService.php';
require_once __DIR__ . '/../../business/CursoService.php';
require_once __DIR__ . '/../../business/InscripcionService.php';
require_once __DIR__ . '/../../utils/MessageHandler.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

$eventoService = new EventoService();
$cursoService = new CursoService();
$inscripcionService = new InscripcionService();

// Obtener ID del evento
$evento_id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if (!$evento_id) {
    MessageHandler::setError('Evento no especificado.');
    header("Location: index.php?view=eventos");
    exit;
}

// PROCESAR INSCRIPCIÓN
if (isset($_GET['inscribir']) && (int)$_GET['inscribir'] == $evento_id) {
    // Verificar que el usuario esté autenticado
    if (!AuthMiddleware::verificar()) {
        MessageHandler::setError('Debes iniciar sesión para inscribirte.');
        header("Location: index.php?view=login&redirect=" . urlencode("index.php?view=eventos/detalle&id=$evento_id"));
        exit;
    }

    $usuario_id = $_SESSION['usuario_id'];
    $resultado = $inscripcionService->inscribirAEvento($usuario_id, $evento_id);

    if ($resultado['success']) {
        MessageHandler::setSuccess($resultado['mensaje']);
    } else {
        MessageHandler::setError($resultado['mensaje']);
    }

    header("Location: index.php?view=eventos/detalle&id=$evento_id");
    exit;
}

// Obtener evento
$evento = $eventoService->obtenerEvento($evento_id);

if (!$evento) {
    MessageHandler::setError('Evento no encontrado.');
    header("Location: index.php?view=eventos");
    exit;
}

// Obtener cursos asociados al evento
$cursos = $cursoService->obtenerCursosPorEvento($evento_id);

// Verificar si el usuario está inscrito (si está autenticado)
$estaInscrito = false;
if (isset($_SESSION['usuario_id'])) {
    $estaInscrito = $inscripcionService->estaInscritoEnEvento($_SESSION['usuario_id'], $evento_id);
}

// Calcular base_url para rutas de recursos
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$base_url = $path . '/public/';

$mensajeFlash = MessageHandler::getFlash();
$page = 'eventos';
$title = htmlspecialchars($evento['titulo']) . ' - EventHub';
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .event-detail-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        .event-detail-hero img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .event-info-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .info-item i {
            width: 30px;
            color: #667eea;
            margin-right: 10px;
        }
        .price-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
        }
        .price-badge .price {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .curso-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        .curso-card h5 {
            color: #333;
            margin-bottom: 10px;
        }
        .badge-cupos {
            font-size: 0.9rem;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../templates/header.php'; ?>

    <!-- Mensajes Flash -->
    <?php if ($mensajeFlash): ?>
        <div class="container mt-4">
            <div class="alert alert-<?php echo $mensajeFlash['tipo']; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $mensajeFlash['tipo'] === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                <?php echo htmlspecialchars($mensajeFlash['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Hero Section con Imagen -->
    <div class="event-detail-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 mb-3"><?php echo htmlspecialchars($evento['titulo']); ?></h1>
                    <?php if ($evento['destacado'] ?? 0): ?>
                        <span class="badge bg-warning text-dark mb-3">
                            <i class="fas fa-star"></i> Evento Destacado
                        </span>
                    <?php endif; ?>
                    <p class="lead"><?php echo htmlspecialchars($evento['descripcion_breve'] ?? ''); ?></p>
                </div>
                <div class="col-md-6">
                    <?php if (!empty($evento['imagen'])): ?>
                        <img src="<?php echo $base_url . htmlspecialchars($evento['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($evento['titulo']); ?>"
                             onerror="this.src='<?php echo $base_url; ?>img/eventos/evento1.jpg'">
                    <?php else: ?>
                        <img src="<?php echo $base_url; ?>img/eventos/evento1.jpg" 
                             alt="<?php echo htmlspecialchars($evento['titulo']); ?>">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Información del Evento -->
                <div class="event-info-card">
                    <h2 class="mb-4"><i class="fas fa-info-circle text-primary"></i> Información del Evento</h2>
                    
                    <?php if (!empty($evento['descripcion_breve'])): ?>
                        <div class="mb-4">
                            <h4>Descripción</h4>
                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($evento['descripcion_breve'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <div>
                            <strong>Fecha:</strong> <?php echo htmlspecialchars($evento['fecha_texto'] ?? 'No especificada'); ?>
                            <?php if (!empty($evento['fecha_inicio'])): ?>
                                <br><small class="text-muted">Fecha de inicio: <?php echo date('d/m/Y', strtotime($evento['fecha_inicio'])); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($evento['horario'])): ?>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Horario:</strong> <?php echo htmlspecialchars($evento['horario']); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Lugar:</strong> <?php echo htmlspecialchars($evento['lugar'] ?? 'No especificado'); ?>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-users"></i>
                        <div>
                            <strong>Cupos:</strong> 
                            <?php 
                            $cupos_disponibles = $evento['cupos_disponibles'] ?? $evento['cupos'] ?? 0;
                            $cupos = $evento['cupos'] ?? 0;
                            $cupoClass = ($cupos_disponibles > 0) ? 'text-success' : 'text-danger';
                            ?>
                            <span class="<?= $cupoClass ?>">
                                <?= $cupos_disponibles ?> disponibles de <?= $cupos ?> cupos totales
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-toggle-<?php echo ($evento['estado'] ?? 'activo') == 'activo' ? 'on' : 'off'; ?>"></i>
                        <div>
                            <strong>Estado:</strong> 
                            <span class="badge bg-<?php echo ($evento['estado'] ?? 'activo') == 'activo' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($evento['estado'] ?? 'activo'); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Cursos del Evento -->
                <?php if (!empty($cursos)): ?>
                <div class="event-info-card">
                    <h2 class="mb-4"><i class="fas fa-graduation-cap text-primary"></i> Cursos Disponibles</h2>
                    <?php foreach ($cursos as $curso): ?>
                        <div class="curso-card">
                            <h5><?php echo htmlspecialchars($curso['nombre']); ?></h5>
                            <?php if (!empty($curso['descripcion'])): ?>
                                <p class="text-muted mb-2"><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php if ($curso['fecha']): ?>
                                        <p class="mb-1"><i class="fas fa-calendar text-muted"></i> 
                                            <small><?php echo date('d/m/Y', strtotime($curso['fecha'])); ?></small>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($curso['horario']): ?>
                                        <p class="mb-1"><i class="fas fa-clock text-muted"></i> 
                                            <small><?php echo htmlspecialchars($curso['horario']); ?></small>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($curso['ponente_nombre']): ?>
                                        <p class="mb-1"><i class="fas fa-user text-muted"></i> 
                                            <small><?php echo htmlspecialchars($curso['ponente_nombre']); ?></small>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p class="mb-1">
                                        <span class="badge badge-cupos bg-<?php echo ($curso['cupos_disponibles'] > 0) ? 'success' : 'danger'; ?>">
                                            <?php echo $curso['cupos_disponibles']; ?> / <?php echo $curso['cupos']; ?> cupos
                                        </span>
                                    </p>
                                    <?php if ($curso['precio']): ?>
                                        <p class="mb-0"><strong class="text-primary">$<?php echo number_format($curso['precio'], 2); ?></strong></p>
                                    <?php else: ?>
                                        <p class="mb-0"><span class="text-muted">Gratis</span></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Precio y Acción -->
                <div class="price-badge">
                    <div class="price">$<?php echo number_format($evento['precio'] ?? 0, 2); ?></div>
                    <p class="mb-0">Precio del evento</p>
                </div>

                <!-- Botón de Inscripción -->
                <?php if (($evento['estado'] ?? 'activo') == 'activo'): ?>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <?php if ($estaInscrito): ?>
                            <div class="event-info-card text-center">
                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-check-circle"></i> 
                                    <strong>Ya estás inscrito</strong>
                                    <p class="mb-0 small">Tu inscripción está confirmada</p>
                                </div>
                                <a href="index.php?view=usuario/dashboard" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="fas fa-user"></i> Ver Mis Inscripciones
                                </a>
                            </div>
                        <?php else: ?>
                            <?php 
                            $cupos_disponibles = $evento['cupos_disponibles'] ?? $evento['cupos'] ?? 0;
                            if ($cupos_disponibles > 0): 
                            ?>
                                <div class="event-info-card text-center">
                                    <a href="index.php?view=eventos/detalle&id=<?php echo $evento['id']; ?>&inscribir=<?php echo $evento['id']; ?>" 
                                       class="btn btn-primary btn-lg w-100 mb-3"
                                       onclick="return confirm('¿Confirmas tu inscripción a este evento?');">
                                        <i class="fas fa-user-plus"></i> Inscribirse al Evento
                                    </a>
                                    <?php if (!empty($cursos)): ?>
                                        <p class="text-muted small">También puedes inscribirte a cursos específicos del evento</p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="event-info-card text-center">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        <strong>Sin cupos disponibles</strong>
                                        <p class="mb-0 small">Este evento ya no tiene cupos disponibles</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="event-info-card text-center">
                            <p class="mb-3">Para inscribirte necesitas iniciar sesión</p>
                            <a href="index.php?view=login&redirect=<?php echo urlencode('index.php?view=eventos/detalle&id=' . $evento['id']); ?>" 
                               class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="event-info-card text-center">
                        <p class="text-muted mb-0">Este evento no está disponible actualmente</p>
                    </div>
                <?php endif; ?>

                <!-- Información Adicional -->
                <div class="event-info-card">
                    <h5 class="mb-3"><i class="fas fa-share-alt"></i> Compartir</h5>
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                           target="_blank" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($evento['titulo']); ?>" 
                           target="_blank" 
                           class="btn btn-outline-info btn-sm">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón Volver -->
        <div class="text-center mt-5 mb-5">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="index.php?view=usuario/dashboard" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            <?php else: ?>
                <a href="index.php?view=eventos" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left"></i> Volver a Eventos
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php include __DIR__ . '/../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

