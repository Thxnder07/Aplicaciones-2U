<?php
// Protección de rutas - Requerir autenticación y rol de administrador
require_once __DIR__ . '/../../../middleware/AdminMiddleware.php';
require_once __DIR__ . '/../../../business/DashboardService.php';
require_once __DIR__ . '/../../../utils/MessageHandler.php';

// Verificar que el usuario sea administrador
AdminMiddleware::requerirAdmin();

// Obtener estadísticas del dashboard
$dashboardService = new DashboardService();
$estadisticas = $dashboardService->obtenerEstadisticas();

// Extraer estadísticas para facilitar su uso
$totalEventos = $estadisticas['total_eventos'];
$eventosActivos = $estadisticas['eventos_activos'];
$eventosInactivos = $estadisticas['eventos_inactivos'];
$totalUsuarios = $estadisticas['total_usuarios'];
$totalInscripciones = $estadisticas['total_inscripciones'];
$totalCursos = $estadisticas['total_cursos'];

// Obtener mensaje flash si existe
$mensajeFlash = MessageHandler::getFlash();

$page = 'admin_dashboard';
$title = 'Dashboard Admin - EventHub';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <link rel="stylesheet" href="../../../../public/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
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
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        .stat-label {
            font-size: 1rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        .action-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: background 0.3s;
        }
        .action-card:hover {
            background: #e9ecef;
        }
        .action-card a {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .action-card i {
            font-size: 1.5rem;
            color: #667eea;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../templates/header.php'; ?>

    <div class="dashboard-container">
        <div class="container">
            <!-- Header del Dashboard -->
            <div class="dashboard-header">
                <h1><i class="fas fa-tachometer-alt"></i> Panel de Administración</h1>
                <p class="mb-0">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Administrador'); ?></p>
            </div>

            <!-- Mensajes Flash -->
            <?php if ($mensajeFlash): ?>
                <div class="alert alert-<?php echo $mensajeFlash['tipo']; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $mensajeFlash['tipo'] === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                    <?php echo htmlspecialchars($mensajeFlash['mensaje']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tarjetas de Estadísticas -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon text-primary">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-number"><?php echo $totalEventos; ?></div>
                        <div class="stat-label">Eventos Creados</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon text-success">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?php echo $totalUsuarios; ?></div>
                        <div class="stat-label">Usuarios Registrados</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon text-warning">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-number"><?php echo $totalInscripciones; ?></div>
                        <div class="stat-label">Inscripciones</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon text-info">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="stat-number"><?php echo $totalCursos; ?></div>
                        <div class="stat-label">Cursos</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo $eventosActivos; ?></div>
                        <div class="stat-label">Eventos Activos</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon text-secondary">
                            <i class="fas fa-pause-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo $eventosInactivos; ?></div>
                        <div class="stat-label">Eventos Inactivos</div>
                    </div>
                </div>
            </div>

            <!-- Accesos Rápidos -->
            <div class="quick-actions">
                <h3 class="mb-4"><i class="fas fa-bolt"></i> Accesos Rápidos</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="action-card">
                            <a href="index.php?view=admin/eventos/index">
                                <div>
                                    <h5><i class="fas fa-calendar-plus"></i> Gestión de Eventos</h5>
                                    <p class="mb-0 text-muted">Crear, editar y eliminar eventos</p>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="action-card">
                            <a href="index.php?view=admin/noticias/index">
                                <div>
                                    <h5><i class="fas fa-newspaper"></i> Gestión de Noticias</h5>
                                    <p class="mb-0 text-muted">Administrar noticias del sitio</p>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="action-card">
                            <a href="index.php?view=admin/cursos/index">
                                <div>
                                    <h5><i class="fas fa-graduation-cap"></i> Gestión de Cursos</h5>
                                    <p class="mb-0 text-muted">Crear, editar y administrar cursos</p>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="action-card">
                            <a href="index.php?view=admin/inscripciones/index">
                                <div>
                                    <h5><i class="fas fa-list-check"></i> Inscripciones</h5>
                                    <p class="mb-0 text-muted">Ver y gestionar todas las inscripciones</p>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="action-card">
                            <a href="index.php?view=admin/log/index">
                                <div>
                                    <h5><i class="fas fa-history"></i> Log de Acciones</h5>
                                    <p class="mb-0 text-muted">Ver registro de acciones administrativas</p>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficas -->
            <div class="row g-4 mt-4">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Inscripciones por Evento</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartInscripcionesEvento" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Estado de Eventos</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartEstadoEventos" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../../templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gráfica de inscripciones por evento
        <?php
        $inscripcionesPorEvento = $estadisticas['inscripciones_por_evento'];
        $eventosLabels = [];
        $eventosData = [];
        foreach ($inscripcionesPorEvento as $item) {
            $eventosLabels[] = htmlspecialchars($item['evento_titulo'] ?? 'Evento #' . $item['evento_id']);
            $eventosData[] = $item['total'];
        }
        ?>
        const ctxInscripciones = document.getElementById('chartInscripcionesEvento');
        if (ctxInscripciones) {
            new Chart(ctxInscripciones, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($eventosLabels); ?>,
                    datasets: [{
                        label: 'Inscripciones',
                        data: <?php echo json_encode($eventosData); ?>,
                        backgroundColor: 'rgba(102, 126, 234, 0.8)',
                        borderColor: 'rgba(102, 126, 234, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Gráfica de estado de eventos
        const ctxEstado = document.getElementById('chartEstadoEventos');
        if (ctxEstado) {
            new Chart(ctxEstado, {
                type: 'doughnut',
                data: {
                    labels: ['Activos', 'Inactivos'],
                    datasets: [{
                        data: [<?php echo $eventosActivos; ?>, <?php echo $eventosInactivos; ?>],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(108, 117, 125, 0.8)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(108, 117, 125, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
