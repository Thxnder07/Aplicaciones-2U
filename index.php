<?php
// index.php - SOLO LÓGICA, CERO HTML AQUÍ

// 1. Definir la lista de rutas permitidas
$routes = [
    'home'           => 'app/presentation/home/index.php',
    'eventos'        => 'app/presentation/eventos/index.php',
    'ponentes'       => 'app/presentation/ponentes/index.php',
    'patrocinadores' => 'app/presentation/patrocinadores/index.php',
    'noticias'       => 'app/presentation/noticias/index.php',
    'contacto'       => 'app/presentation/contacto/index.php',
    'auth'           => 'app/presentation/auth/login.php'
];

// 2. Obtener qué vista quiere ver el usuario (por defecto 'home')
$view = isset($_GET['view']) ? $_GET['view'] : 'home';

// 3. Cargar la vista correspondiente
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