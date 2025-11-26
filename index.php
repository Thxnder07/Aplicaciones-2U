<?php
// index.php - SOLO LÓGICA, CERO HTML AQUÍ

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar middleware para protección de rutas
require_once __DIR__ . '/app/middleware/AuthMiddleware.php';
require_once __DIR__ . '/app/middleware/AdminMiddleware.php';

// 1. Definir la lista de rutas permitidas
$routes = [
    // Rutas públicas
    'home'           => 'app/presentation/home/index.php',
    'eventos'        => 'app/presentation/eventos/index.php',
    'eventos/detalle' => 'app/presentation/eventos/detalle.php',
    'ponentes'       => 'app/presentation/ponentes/index.php',
    'patrocinadores' => 'app/presentation/patrocinadores/index.php',
    'noticias'       => 'app/presentation/noticias/index.php',
    'contacto'       => 'app/presentation/contacto/index.php',
    
    // Rutas de autenticación
    'login'          => 'app/presentation/auth/login.php',
    'registro'       => 'app/presentation/auth/registro.php',
    'procesar_login' => 'app/presentation/auth/procesar_login.php',
    'procesar_registro' => 'app/presentation/auth/procesar_registro.php',
    'logout'         => 'app/presentation/auth/logout.php',
    
    // Rutas de usuario (requieren autenticación)
    'usuario/dashboard' => 'app/presentation/usuario/dashboard/index.php',
    
    // Rutas de administrador (requieren rol admin)
    'admin/dashboard' => 'app/presentation/admin/dashboard/index.php',
    'admin/eventos/index' => 'app/presentation/admin/eventos/index.php',
    'admin/eventos/crear' => 'app/presentation/admin/eventos/crear.php',
    'admin/noticias/index' => 'app/presentation/admin/noticias/index.php',
    'admin/noticias/crear' => 'app/presentation/admin/noticias/crear.php',
    'admin/cursos/index' => 'app/presentation/admin/cursos/index.php',
    'admin/cursos/crear' => 'app/presentation/admin/cursos/crear.php',
    'admin/inscripciones/index' => 'app/presentation/admin/inscripciones/index.php',
    'admin/log/index' => 'app/presentation/admin/log/index.php'
];

// 2. Obtener qué vista quiere ver el usuario (por defecto 'home')
$view = isset($_GET['view']) ? $_GET['view'] : 'home';

// 3. Protección de rutas
// Verificar si la ruta requiere autenticación (empieza con 'admin/' o 'usuario/')
if (strpos($view, 'admin/') === 0) {
    // Rutas de administrador - requieren rol admin
    AdminMiddleware::requerirAdmin();
} elseif (strpos($view, 'usuario/') === 0) {
    // Rutas de usuario - requieren autenticación
    AuthMiddleware::requerirAutenticacion();
}

// 4. Cargar la vista correspondiente
if (array_key_exists($view, $routes)) {
    $filePath = $routes[$view];
    
    if (file_exists($filePath)) {
        // Aquí es donde sucede la magia: cargamos el archivo de la vista
        include $filePath;
    } else {
        echo "Error 500: El archivo de la vista '$view' no existe.";
    }
} else {
    echo "<h1>Error 404</h1><p>Página no encontrada</p>";
}
?>